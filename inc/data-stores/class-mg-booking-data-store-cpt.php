<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MG_Booking_Data_Store_CPT extends WC_Data_Store_WP implements WC_Object_Data_Store_Interface {

    /**
	 * Data stored in meta keys, but not considered "meta".
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $internal_meta_keys = array(
        '_product_id'          => '',
        '_datetime'            => '',
        '_apex_calendar_id'    => '',
        '_apex_appointment_id' => '',
        '_apex_status'         => '', // [P, Y, N]
		'_order_id'			   => '',
		'_order_item_id'	   => '',
		'_cart_item_key'	   => '',
		'_timezone'	   		   => '',
		// patient
        '_name'                => '',
        '_lastname1'     	   => '',
        '_lastname2'    	   => '',
        '_email'               => '',
        '_phone'               => '',
        '_birthdate'           => '',
        '_sex'                 => '',
        '_age'                 => '',
        '_birth_state'         => '',
        '_curp'                => '',
	);

	/**
	 * Meta data which should exist in the DB, even if empty.
	 *
	 * @since 3.6.0
	 *
	 * @var array
	 */
	// protected $must_exist_meta_keys = array(
	// 	'_tax_class',
	// );

	/**
	 * If we have already saved our extra data, don't do automatic / default handling.
	 *
	 * @var bool
	 */
	protected $extra_data_saved = false;

	/**
	 * Stores updated props.
	 *
	 * @var array
	 */
	protected $updated_props = array();

	/*
	|--------------------------------------------------------------------------
	| CRUD Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Method to create a new booking in the database.
	 * 
	 * @param MG_Booking $booking Product object.
	 */
    public function create( &$booking ) {
		$id = wp_insert_post(
			apply_filters(
				'woocommerce_new_booking_data',
				array(
					'post_type'      => 'booking',
					'post_status'    => $booking->get_status() ? $booking->get_status() : 'publish',
					'post_author'    => get_current_user_id(),
					'post_title'     => $booking->get_title() ? $booking->get_title() : 'Agenda',
					'post_content'   => $booking->get_description(),
					'post_excerpt'   => $booking->get_short_description(),
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					// 'menu_order'     => $booking->get_menu_order(),
					// 'post_date'      => gmdate( 'Y-m-d H:i:s', $booking->get_date_created( 'edit' )->getOffsetTimestamp() ),
					// 'post_date_gmt'  => gmdate( 'Y-m-d H:i:s', $booking->get_date_created( 'edit' )->getTimestamp() ),
					// 'post_name'      => $booking->get_slug( 'edit' ),
				)
			),
			true
		);

		if ( $id && ! is_wp_error( $id ) ) {
			$booking->set_id( $id );

			$this->update_post_meta( $booking, true );

			$booking->save_meta_data();
			$booking->apply_changes();

			do_action( 'woocommerce_new_booking', $id, $booking );
		}
    }

	/**
	 * Method to read a booking from the database.
	 *
	 * @param MG_Booking $booking Booking object.
	 * @throws Exception If invalid booking.
	 */
    public function read( &$booking ) {
		$booking->set_defaults();
		$post_object = get_post( $booking->get_id() );

		if ( ! $booking->get_id() || ! $post_object || 'booking' !== $post_object->post_type ) {
			throw new Exception( __( 'Invalid booking.', 'woocommerce' ) );
		}

		$booking->set_props(
			array(
				'title'             => $post_object->post_title,
				'slug'              => $post_object->post_name,
				'date_created'      => $this->string_to_timestamp( $post_object->post_date_gmt ),
				'date_modified'     => $this->string_to_timestamp( $post_object->post_modified_gmt ),
				'status'            => $post_object->post_status,
				'description'       => $post_object->post_content,
				'short_description' => $post_object->post_excerpt,
				// 'parent_id'         => $post_object->post_parent,
				// 'menu_order'        => $post_object->menu_order,
				// 'post_password'     => $post_object->post_password,
				// 'reviews_allowed'   => 'open' === $post_object->comment_status,
			)
		);

		$this->read_product_data( $booking );
		// $this->read_extra_data( $booking );
		$booking->set_object_read( true );

		do_action( 'woocommerce_booking_read', $booking->get_id() );
    }

	/**
	 * Method to update a booking in the database.
	 *
	 * @param MG_Booking $booking Booking object.
	 */
    public function update( &$booking ) {
		$booking->save_meta_data();
		// $changes = $booking->get_changes();

		$post_data = array(
			'post_content'   => $booking->get_description( 'edit' ),
			'post_excerpt'   => $booking->get_short_description( 'edit' ),
			'post_title'     => $booking->get_title( 'edit' ),
			'post_type'      => 'booking',
			'post_status'    => $booking->get_status( 'edit' ) ? $booking->get_status( 'edit' ) : 'publish',
		);

		/**
		 * When updating this object, to prevent infinite loops, use $wpdb
		 * to update data, since wp_update_post spawns more calls to the
		 * save_post action.
		 *
		 * This ensures hooks are fired by either WP itself (admin screen save),
		 * or an update purely from CRUD.
		 */
		if ( doing_action( 'save_post' ) ) {
			$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $booking->get_id() ) );
			clean_post_cache( $booking->get_id() );
		} else {
			wp_update_post( array_merge( array( 'ID' => $booking->get_id() ), $post_data ) );
		}
		$booking->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.


		$this->update_post_meta( $booking );

		$booking->apply_changes();

		do_action( 'woocommerce_update_booking', $booking->get_id(), $booking );
    }

	/**
	 * Method to delete a booking from the database.
	 *
	 * @param MG_Booking $booking Booking object.
	 * @param array      $args Array of args to pass to the delete method.
	 */
    public function delete( &$booking, $args = array() ) {
		$id        = $booking->get_id();
		$post_type = 'booking';

		$args = wp_parse_args(
			$args,
			array(
				'force_delete' => false,
			)
		);

		if ( ! $id ) {
			return;
		}

		if ( $args['force_delete'] ) {
			do_action( 'woocommerce_before_delete_' . $post_type, $id );
			wp_delete_post( $id );
			$booking->set_id( 0 );
			do_action( 'woocommerce_delete_' . $post_type, $id );
		} else {
			wp_trash_post( $id );
			$booking->set_status( 'trash' );
			do_action( 'woocommerce_trash_' . $post_type, $id );
		}
    }

	/*
	|--------------------------------------------------------------------------
	| Additional Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Helper method that updates all the post meta for a booking based on it's settings in the MG_Booking class.
	 *
	 * @param MG_Booking $booking Booking object.
	 * @param bool       $force Force update. Used during create.
	 * @since 3.0.0
	 */
	protected function update_post_meta( &$booking, $force = false ) {
		$meta_key_to_props = array(
			'_product_id'          => 'product_id',
			'_datetime'            => 'datetime',
			'_apex_calendar_id'    => 'apex_calendar_id',
			'_apex_appointment_id' => 'apex_appointment_id',
			'_apex_status'         => 'apex_status', // [P, Y, N]
			'_order_id'			   => 'order_id',
			'_order_item_id'	   => 'order_item_id',
			'_cart_item_key'	   => 'cart_item_key',
			'_timezone'	   		   => 'timezone',
			// patient
			'_name'                => 'name',
			'_lastname1'     	   => 'lastname1',
			'_lastname2'    	   => 'lastname2',
			'_email'               => 'email',
			'_phone'               => 'phone',
			'_birthdate'           => 'birthdate',
			'_sex'                 => 'sex',
			'_age'                 => 'age',
			'_birth_state'         => 'birth_state',
			'_curp'                => 'curp',
		);

		// Make sure to take extra data (like booking url or text for external products) into account.
		// $extra_data_keys = $booking->get_extra_data_keys();

		// foreach ( $extra_data_keys as $key ) {
		// 	$meta_key_to_props[ '_' . $key ] = $key;
		// }

		$props_to_update = $force ? $meta_key_to_props : $this->get_props_to_update( $booking, $meta_key_to_props );

		foreach ( $props_to_update as $meta_key => $prop ) {
			$value = $booking->{"get_$prop"}( 'edit' );
			$value = is_string( $value ) ? wp_slash( $value ) : $value;

			$updated = $this->update_or_delete_post_meta( $booking, $meta_key, $value );

			if ( $updated ) {
				$this->updated_props[] = $prop;
			}
		}
	}

	/**
	 * Read booking data. Can be overridden by child classes to load other props.
	 *
	 * @param MG_Booking $booking Booking object.
	 * @since 3.0.0
	 */
	protected function read_product_data( &$booking ) {
		$id                = $booking->get_id();
		$post_meta_values  = get_post_meta( $id );
		$meta_key_to_props = array(
			'_product_id'          => 'product_id',
			'_datetime'            => 'datetime',
			'_apex_calendar_id'    => 'apex_calendar_id',
			'_apex_appointment_id' => 'apex_appointment_id',
			'_apex_status'         => 'apex_status', // [P, Y, N]
			'_order_id'			   => 'order_id',
			'_order_item_id'	   => 'order_item_id',
			'_cart_item_key'	   => 'cart_item_key',
			'_timezone'	   		   => 'timezone',
			// patient
			'_name'                => 'name',
			'_lastname1'     	   => 'lastname1',
			'_lastname2'    	   => 'lastname2',
			'_email'               => 'email',
			'_phone'               => 'phone',
			'_birthdate'           => 'birthdate',
			'_sex'                 => 'sex',
			'_age'                 => 'age',
			'_birth_state'         => 'birth_state',
			'_curp'                => 'curp',
		);

		$set_props = array();

		foreach ( $meta_key_to_props as $meta_key => $prop ) {
			$meta_value         = isset( $post_meta_values[ $meta_key ][0] ) ? $post_meta_values[ $meta_key ][0] : null;
			$set_props[ $prop ] = maybe_unserialize( $meta_value ); // get_post_meta only unserializes single values.
		}

		$booking->set_props( $set_props );
	}
}
