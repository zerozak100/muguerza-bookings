<?php

class MG_Booking_Session {

    public static function getData() {
        return WC()->session->get( 'mgb_booking_data', array() );
    }

    public static function save( array $data ) {
        WC()->session->set( 'mgb_booking_data', $data );
    }

    public static function clean() {
        WC()->session->__unset( 'mgb_booking_data' );
    }

    public static function removeProduct( $product_id ) {
        $booking_data = self::getData();
        unset( $booking_data[ $product_id ] );
        self::save( $booking_data );
    }

    public static function addBookingItem( $product_id, array $data ) {
        $booking_id = uniqid();
        self::saveBookingItem( $product_id, $booking_id, $data );
    }

    public static function saveBookingItem( $product_id, $booking_id, array $data ) {
        $booking_data = self::getData();

        if ( ! isset( $booking_data[ $product_id ] ) ) {
            $booking_data[ $product_id ] = array();
        }

        $booking_data[ $product_id ][ $booking_id ] = $data;

        self::save( $booking_data );
    }

    public static function removeBooking( $product_id, $unique_id ) {
        $booking_data = self::getData();
        unset( $booking_data[ $product_id ][ $unique_id ] );
        self::save( $booking_data );
    }

    public static function getBookingItem( $product_id, $booking_id ) {
        $booking_data = self::getData();
        if ( isset( $booking_data[ $product_id ] ) && isset( $booking_data[ $product_id ][ $booking_id ] ) ) {
            return $booking_data[ $product_id ][ $booking_id ];
        }
        return false;
    }

    public static function getProductBookings( $product_id ) {
        $booking_data = self::getData();
        return $booking_data[ $product_id ];
    }

    public static function removeBookings( $product_id ) {
        
    }
}