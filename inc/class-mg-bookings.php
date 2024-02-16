<?php

class MG_Bookings {

    /**
     * @param array $request $_POST request
     * 
     * @return WP_Error|MG_Booking
     */
    public static function create_from_request( array $request ) {
        $data = self::get_data_from_request( $request );
        $data = wc_clean( $data );

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
            'name',
            'email',
            'lastname1',
            'lastname2',
            'phone',
            'birthdate',
            'sex',
            // 'age',
            // 'birth_state',
            // 'curp',
            'datetime',
            'product_id',
        );
        
        $only_letters = array(
            'name',
            'lastname1',
            'lastname2',
            'sex',
        );

        $length50_fields = array_keys( MG_Booking_Form::FORM_FIELDS );

        foreach ( $required_fields as $field ) {
            if ( ! isset( $data[ $field ] ) || ! $data[ $field ] ) {
                $field_name = MG_Booking_Form::FORM_FIELDS[ $field ];
                $error->add( "{$field}_required", "{$field_name} es requerido" );
            }
        }

        if ( $error->has_errors() ) {
            return $error;
        }

        foreach ( $length50_fields as $field ) {
            if ( strlen( $data[ $field ] ) > 50 ) {
                $field_name = MG_Booking_Form::FORM_FIELDS[ $field ];
                $error->add( "{$field}_length", "{$field_name} no puede contener más de 50 caractéres" );
            }
        }

        foreach ( $only_letters as $field ) {
            if ( preg_match('/[^A-Za-z]/', $data[ $field ]) ) {
                $field_name = MG_Booking_Form::FORM_FIELDS[ $field ];
                $error->add( "{$field}_letters", "{$field_name} solo puede contener letras" );
            }
        }

        if( ! filter_var( $data['email'], FILTER_VALIDATE_EMAIL ) ) {
            $error->add( 'email_invalid', 'El correo electrónico no es válido' );
        }

        $data['apex_calendar_id'] = get_field( 'apex_calendar_id', $data['product_id'] );

        if ( ! $data['apex_calendar_id'] ) {
            $error->add( "apex_calendar_id_required", 'Producto no cuenta con Calendar ID para su agenda' );
        }

        $datetime_ts = strtotime( $data['datetime'] );
        if ( ! $datetime_ts || date( MG_Booking_Form::DATETIME_FORMAT, $datetime_ts ) !== $data['datetime'] ) {
            $error->add( "datetime_invalid", 'Fecha y hora inválida' );
        }

        $birthdate_ts = strtotime( $data['birthdate'] );
        if ( ! $birthdate_ts || date( MG_Booking_Form::BIRTHDATE_FORMAT, $birthdate_ts ) !== $data['birthdate'] ) {
            $error->add( 'birthdate_invalid', 'Fecha de nacimiento inválida' );
        } else if ( $birthdate_ts > time() ) {
            $error->add( 'birthdate_no_future', 'Fecha de nacimiento no puede ser en el futuro' );
        }

        return $error;
    }


    public static function get_data_from_request( array $request ) {
        $data = array_intersect_key( $request, MG_Booking_Form::FORM_FIELDS );

        return $data;
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
