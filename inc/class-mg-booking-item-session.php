<?php

class MG_Booking_Item_Session extends MG_Booking_Item {

    public function __construct( $product_id, $booking_id ) {
        $this->setId( $booking_id );
        
        $booking_data = MG_Booking_Session::getData();

        if ( isset( $booking_data[ $product_id ] ) && isset( $booking_data[ $product_id ][ $booking_id ] ) ) {
            $this->data = $booking_data[ $product_id ][ $booking_id ];
        }
    }

    public function save() {
        MG_Booking_Session::saveBookingItem( $this->getProductId(), $this->getId(), $this->data );
    }

    public static function create( $product_id, array $data ) {
        $booking_id = uniqid();
        $item       = new self( $product_id, $booking_id );

        foreach ( $data as $key => $value ) {
            $callable = $item->snakeToCamel( "set_$key" );
            if ( is_callable( array( $item, $callable ) ) ) {
				$item->{"$callable"}( $value );
            }
        }

        $item->save();

        return $item;
    }
}
