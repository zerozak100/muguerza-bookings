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

    /**
     * Periodo 2
     * 
     * A partir de que se crea el pedido y se refleja el pago
     */
    public function schedule_cancelation() {

        $order_id = wc_get_order_id_by_order_item_id( $this->order_item_id );
        $order    = wc_get_order( $order_id );

        $payment_method = $order->get_payment_method(); // conektacard|conektaoxxopay|conektaspei
        $order_total    = $order->get_total();

        if ( 'conektacard' === $payment_method ) {
            $tolerance = strtotime( '10 minutes' );
        } else if ( in_array( $payment_method, array( 'conektaoxxopay', 'conektaspei' ) ) ) {
            if ( $order_total >= 40000 ) {
                $tolerance = strtotime( '+24 hours' );
            } else {
                $tolerance = strtotime( '+70 minutes' );
            }
        }

        $args = array(
            'type' => self::class,
            'data' => array(
                'order_item_id' => $this->getProductId(),
                'booking_id'    => $this->getId(),
            ),
        );

        as_schedule_single_action( $tolerance, 'muguerza_cancel_booking_item', array_values( $args ) );
    }

    public function cancel() {
        
    }
}
