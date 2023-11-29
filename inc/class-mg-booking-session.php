<?php

class MG_Booking_Session {

    public static function getData() {
        return WC()->session->get( 'mgbookings', array() );
    }

    public static function save( array $data ) {
        WC()->session->set( 'mgbookings', $data );
    }

    public static function clean() {
        WC()->session->__unset( 'mgbookings' );
    }

    public static function getAll( $ids = false ) {
        $all = array();

        foreach ( self::getData() as $cart_item_key => $bookings ) {
            foreach ( $bookings as $booking_id => $booking_data ) {
                if ( $ids ) {
                    $all[] = $booking_id;
                } else {
                    $all[$booking_id] = $booking_data;
                }
            }
        }

        return $all;
    }

    public static function getCartItemBookings( $cart_item_key ) {
        $booking_data = self::getData();
        return $booking_data[ $cart_item_key ];
    }

    public static function removeCartItemBookings( $cart_item_key ) {
        $bookings = self::getData();
        unset( $bookings[ $cart_item_key ] );
        self::save( $bookings );
    }

    public static function saveBooking( $cart_item_key, $booking_id, array $data ) {
        $bookings = self::getData();
        $bookings[ $cart_item_key ][ $booking_id ] = $data;
        self::save( $bookings );
    }

    public static function removeBooking( $cart_item_key, $booking_id ) {
        $bookings = self::getData();
        unset( $bookings[ $cart_item_key ][ $booking_id ] );
        self::save( $bookings );
    }
}
