<?php

class MG_Bookings {

    /**
     * @param array $request $_POST request
     * 
     * @return WP_Error|MG_Booking
     */
    public static function create_from_request( array $request ) {
        $data             = wc_clean( $request );
        $data['email']    = sanitize_email( $request['email'] );
        $data['datetime'] = date( 'd/m/Y g:i a', strtotime( $data['datetime'] ) ); // TODO: Agregar timezone

        $error = self::validate_create_request( $data );

        if ( $error->has_errors() ) {
            return $error;
        }

        $booking = new MG_Booking();
		$booking->set_props( $data );

        $product = get_post( $booking->get_product_id() );

        $booking->set_apex_status( 'P' );
        $booking->set_title(
            sprintf(
                '%s - %s %s %s - %s',
                $product->post_title,
                $booking->get_name(),
                $booking->get_lastname1(),
                $booking->get_lastname2(),
                $booking->get_datetime(),
            )
        );
        $booking->set_short_description(
            sprintf(
                '%s %s - %s',
                $booking->get_name(),
                $booking->get_lastname1(),
                $booking->get_datetime(),
            )
        );

        return $booking;
    }

    public static function validate_create_request( array &$data ) {
        $error = new WP_Error();

        $required_fields = array(
            'datetime'   => 'Fecha y hora',
            'product_id' => 'ID del producto',
            'name'       => 'Nombre',
            'email'      => 'Correo electrónico',
            'lastname1'  => 'Apellido paterno',
            'lastname2'  => 'Apellido materno',
            'phone'      => 'Celular',
            'birthdate'  => 'Fecha de nacimiento',
        );

        foreach ( $required_fields as $key => $label ) {
            if ( ! isset( $data[ $key ] ) || ! $data[ $key ] ) {
                $error->add( "{$key}_required", "$label es requerido" );
            }
        }

        if ( $data['product_id'] ) {
            $data['apex_calendar_id'] = get_field( 'apex_calendar_id', $data['product_id'] );
        }

        if ( ! $data['apex_calendar_id'] ) {
            $error->add( "apex_calendar_id_required", 'Producto no cuenta con Calendar ID para su agenda' );
        }

        return $error;
    }

    public static function get_bookings() {
        $bookings = get_posts(
            array(

            )
        );
    }

    /**
     * @return MG_Booking[]
     */
    public static function get_bookings_from_order_item( $order_item_id ) {
        $posts = get_posts(
            array(
                'post_type'      => 'booking',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'meta_query'     => array(
                    'AND',
                    array(
                        'key'   => '_order_item_id',
                        'value' => $order_item_id,
                    ),
                ),
            )
        );

        $bookings = array();

        foreach ( $posts as $booking_id ) {
            $bookings[] = new MG_Booking( $booking_id );
        }

        return $bookings;
    }

    /**
     * @return MG_Booking[]
     */
    public static function get_bookings_from_order( $order_id ) {
        $posts = get_posts(
            array(
                'post_type'      => 'booking',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'meta_query'     => array(
                    'AND',
                    array(
                        'key'   => '_order_id',
                        'value' => $order_id,
                    ),
                ),
            )
        );

        $bookings = array();

        foreach ( $posts as $booking_id ) {
            $bookings[] = new MG_Booking( $booking_id );
        }

        return $bookings;
    }
    
}
