<?php

class MG_Booking_Item_Order_Item extends MG_Booking_Item {

    public $order_item_id;

    public function __construct( $item_id, $booking_id ) {
        $this->order_item_id = $item_id;

        $bookings = wc_get_order_item_meta( $item_id, 'mgb_bookings', true );
        if ( isset( $bookings[ $booking_id ] ) ) {
            $this->data = $bookings[ $booking_id ];
        }
    }

    public function save() {
        // foreach ( $this->data as $key => $value ) {
        //     wc_update_order_item_meta( $this->order_item_id, $key, $value );
        // }
    }
}
