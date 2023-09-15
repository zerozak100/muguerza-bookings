<?php

class MG_Order extends WC_Order {
    
    public function has_booking_item() {
        foreach ( $this->get_items() as $key => $item ) {
            $mg_product = new MG_Product( $item['product_id'] );

            if ( $mg_product->is_agendable() ) {
                return true;
            }
        }

        return false;
    }
}