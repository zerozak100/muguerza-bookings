<?php

// TODO: disable qty input in cart item for bookable products

/**
 * @property MG_Booking_Form $instance
 * @property MG_Calendar $calendar
 */
class MG_Booking_Form {

    private static $instance;
    protected $calendar;

    /**
     * Gets an instance of our plugin.
     *
     * @return MG_Booking_Form
     */
    public static function getInstance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
		// add_action( 'wp_loaded', array( $this, 'saveBookingItemInSession' ), 20 );
		add_action( 'wp_loaded', array( $this, 'saveBooking' ), 20 );

        add_action( 'template_redirect', array( $this, 'init' ) );
        add_action( 'woocommerce_after_single_product', array( $this, 'loadModal' ) );

        // cart
        add_filter( 'woocommerce_get_item_data', array( $this, 'show_cart_item_bookings' ), 10, 2 );
        add_action( 'woocommerce_remove_cart_item', array( $this, 'removeCartItemBookings' ) );
        add_action( 'woocommerce_quantity_input_args', array( $this, 'validateBookingItemQuantity' ), 10, 2 );

        // order
        add_action( 'woocommerce_checkout_order_created', array( $this, 'updateBookingsWithOrder' ) );
        add_action( 'woocommerce_payment_complete', array( $this, 'apexConfirmAppointments' ) );
        add_action( 'woocommerce_order_status_changed', array( $this, 'woocommerce_order_status_changed' ), 10, 4 );

        add_action( 'thwcfd_order_details_before_custom_fields_table', array( $this, 'addHeadingInThankYouPage' ) );

        add_action( 'muguerza_cancel_booking_item', function( $type, $data ) {
            add_action( 'template_redirect', function() use ( $type, $data ) {
                $this->cancel_booking_item( $type, $data );
            } );
        }, 10, 2 );

        // add_filter( 'woocommerce_add_cart_item_data', array( $this, '' ) );
        // add_action( 'woocommerce_add_order_item_meta', array( $this, 'addBookingsToOrderItem' ), 10, 2 );
        // add_action( 'woocommerce_new_order_item', array( $this, 'addBookingsToOrderItem' ), 10, 3 );
    }

    public function updateBookingsWithOrder( $order ) {
        $mg_order = new MG_Order( $order );

        if ( ! $mg_order->has_bookable_product() ) {
            return;
        }

        $order_items    = $mg_order->get_items();
        $order_bookings = MG_Bookings::get_bookings_from_order( $mg_order->get_id() );

        foreach ( $order_bookings as $booking ) {
            foreach ( $order_items as $item_id => $item ) {
                if ( $item['product_id'] == $booking->get_product_id() ) {
                    $booking->set_order_item_id( $item_id );
                    $booking->set_order_id( $mg_order->get_id() );
                }
            }
            $booking->save();
        }

        MG_Booking_Session::clean();
    }

    /**
     * Saves booking in DB and Session
     */
    public function saveBooking() {
        if ( isset( $_POST['mgb-booking-save'] ) && '1' === $_POST['mgb-booking-save'] ) {
            $booking = MG_Bookings::create_from_request( $_POST );

            if ( $booking instanceof WP_Error ) {
                return array_map( 'wc_add_notice', $booking->get_error_messages(), array_fill( 0, count( $booking->get_error_messages() ), 'error' ) );
            }

            $apex = MG_Api_Apex::instance();
            $apex_appointment_id = $apex->create_appointment( $booking );

            if ( ! $apex_appointment_id ) {
                return wc_add_notice( 'Error al agendar: APEX no pudo crear la cita', 'error' );
            } else {
                $booking->set_apex_appointment_id( $apex_appointment_id );
                $booking->schedule_cancelation_1();
            }

            $cart_item_key = WC()->cart->add_to_cart( $booking->get_product_id() );
            $booking->set_cart_item_key( $cart_item_key );
            $booking->save();

            MG_Booking_Session::saveBooking( $booking->get_cart_item_key(), $booking->get_id(), $booking->get_data() );

            wp_safe_redirect( wc_get_cart_url() );
            exit();
        }
    }

    /**
     * Cancel booking item only if status is of Pending payment
     * 
     * TODO: Session: remover o decrementar cart item al cancelar cita
     * TODO: Order: cancelar pedido al cancelar cita
     */
    public function cancel_booking_item( $type, $data ) {
        // if ( MG_Booking_Item_Session::class === $type ) {
        //     $booking_item = new MG_Booking_Item_Session( $data['product_id'], $data['booking_id'] );
        //     $status = $booking_item->getStatus();
        // }

        // if ( MG_Booking_Item_Order_Item::class === $type ) {
        //     $booking_item = new MG_Booking_Item_Order_Item( $data['order_item_id'], $data['booking_id'] );
        //     $status = $booking_item->getStatus();
        // }

        // if ( $booking_item && 'Y' !== $status ) {
        //     // $api = MG_Api_Apex::instance();
        //     // $success = $api->cancel_appointment( $booking_item );
        //     $booking_item->cancel();
        //     // $booking_item->setStatus( 'N' );
        //     // $booking_item->save();
        //     // if ( true ) {
        //     // }
        // }
    }

    public function init() {
        if ( is_product() ) {
            global $post;
            $apex_calendar_id = get_field( 'apex_calendar_id', $post->ID );

            if ( ! $apex_calendar_id ) {
                mg_redirect_with_error( home_url( 'servicios' ), 'El producto agendable no cuenta con un Calendar ID de APEX' );
            }

            $this->calendar = new MG_Calendar( date( 'Y-m-d' ), $apex_calendar_id );

            add_action( 'wp_enqueue_scripts', array( $this->calendar, 'scripts' ) );
        }
    }

    public function addHeadingInThankYouPage() {
        echo "<h4>Datos del paciente</h4>";
    }

    public function validateBookingItemQuantity( $args, $product ) {
        $mg_product = new MG_Product( $product );

        if ( ! $mg_product->is_agendable() ) {
            return $args;
        }

        $args['readonly'] = true;

        return $args;
    }

    public function woocommerce_order_status_changed( $order_id, $from, $to, $order ) {
        if ( in_array( $to, array( 'processing', 'completed' ) ) ) {
            $this->apexConfirmAppointments( $order_id );
        }
    }

    protected function apexConfirmAppointments( $order_id ) {
        $order = wc_get_order( $order_id );
        $success_appointments = $order->get_meta( 'success_appointments' ) ?: array();

        $API = MG_Api_Apex::instance();

        $order_bookings = MG_Bookings::get_bookings_from_order( $order_id );

        foreach ( $order_bookings as $booking ) {
            if ( ! in_array( $booking->get_id(), $success_appointments ) ) {
                $success = $API->confirm_appointment( $booking );
                if ( $success ) {
                    $$success_appointments[] = $booking->get_id();
                }
            }
        }

        $order->update_meta_data( 'success_appointments', $success_appointments );
        $order->save();
    }

    public function getCalendar() {
        return $this->calendar;
    }

    /**
     * @hook woocommerce_after_single_product
     */
    public function loadModal() {
        global $product;
        $mg_product = new MG_Product( $product );

        if ( ! $mg_product->is_agendable() ) {
            return;
        } 

        mgb_get_template( 'booking-form/modal.php', array( 'form' => $this, 'product' => $mg_product ) );
    }

    public function showFields() {
        mgb_get_template( 'booking-form/fields.php', array( 'form' => $this ) );
    }
    
    /**
     * Creates apex appointment for the first time
     */
    // public function saveBookingItemInSession() {
    //     if ( isset( $_POST['mgb-booking-save'] ) && '1' === $_POST['mgb-booking-save'] ) {
    //         $booking_item = MG_Booking_Item_Session::createFromRequest( $_POST );

    //         if ( $booking_item instanceof WP_Error ) {
    //             array_map( 'wc_add_notice', $booking_item->get_error_messages(), array_fill( 0, count( $booking_item->get_error_messages() ), 'error' ) );
    //         }

    //         $apex = MG_Api_Apex::instance();
    //         $apex_appointment_id = $apex->create_appointment( $booking_item );

    //         if ( ! $apex_appointment_id ) {
    //             wc_add_notice( 'Error al agendar: APEX no pudo crear la cita', 'error' );
    //         } else {
    //             $booking_item->setId( $apex_appointment_id );
    //             $booking_item->setApexAppointmentId( $apex_appointment_id );
    //             $booking_item->schedule_cancelation();
    //         }

    //         $cart_item_key = WC()->cart->add_to_cart( $booking_item->getProductId() );
    //         $booking_item->setCartItemKey( $cart_item_key );
    //         $booking_item->save();

    //         wp_safe_redirect( wc_get_cart_url() );
    //         exit();
    //     }
    // }

    /**
     * @param array $item_data Data to display.
     * @param array $cart_item Cart item data.
     */
    public function show_cart_item_bookings( $item_data, $cart_item ) {
        // dd( $item_data, $cart_item );
        // $product_id = $cart_item['product_id'];

        // $mg_product = new MG_Product( $product_id );

        // if ( ! $mg_product->is_agendable() ) {
        //     return $item_data;
        // }

        $bookings = MG_Booking_Session::getCartItemBookings( $cart_item['key'] );

        if ( ! empty( $bookings ) ) {
            foreach ( $bookings as $booking_id => $data ) {
                $item_data[ $booking_id ]            = array();
                $item_data[ $booking_id ]['key']     = $data['name'] . ' ' . $data['lastname1'];
                $item_data[ $booking_id ]['display'] = $data['datetime'] . ' (' . $data['id'] . ')';
            }
        }

        return $item_data;
    }

    /**
     * TODO: Cancelar todas las citas si se remueven desde aquí
     */
    public function removeCartItemBookings( $item_key ) {
        $bookings = MG_Booking_Session::getCartItemBookings( $item_key );

        foreach ( $bookings as $booking_id => $data ) {
            $booking = new MG_Booking( $booking_id );

            $apex_api = MG_Api_Apex::instance();
            $canceled = $apex_api->cancel_appointment( $booking );

            if ( $canceled ) {
                $booking->set_apex_status( 'N' );
                $booking->save();
            }

            // wp_delete_post( $booking_id );
        }
        
        MG_Booking_Session::removeCartItemBookings( $item_key );
    }

    public function showOpenButton() {
        mgb_get_template( 'booking-form/open-button.php' );
    }
}
