<?php

class MG_Bookable_Order_Item extends WC_Order_Item_Product {

    public function saveBookings( array $bookings ) {
        $this->update_meta_data( 'mgb_bookings', $bookings );
        $this->save();

        foreach ( $bookings as $booking_id => $data ) {
            $booking_item = $this->getBookingItem( $booking_id );
            $this->update_meta_data( $booking_item->getKey(), $booking_item->getLabel() );
        }

        $this->save();
    }

    /**
     * @return MG_Booking_Item_Order_Item
     */
    public function getBookingItem( $booking_id ) {
        $booking_item = new MG_Booking_Item_Order_Item( $this->get_id(), $booking_id );
        return $booking_item;
    }

    /**
     * @return MG_Booking_Item_Order_Item[]
     */
    public function getBookings() {
        $mgb_bookings = $this->get_meta( 'mgb_bookings', true );

        if ( is_array( $mgb_bookings ) ) {
            return array_map( array( $this, 'getBookingItem' ), array_keys( $mgb_bookings ) );
        }

        return array(); // If its not agendable
    }
}
