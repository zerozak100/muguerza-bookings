<?php

class MG_Booking_Form {

    private static $instance;

    /**
     * @var MG_Calendar
     */
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
		add_action( 'wp_loaded', array( $this, 'handleConfigSave' ), 20 );
        add_action( 'template_redirect', array( $this, 'init' ) );

        add_action( 'woocommerce_after_single_product', array( $this, 'loadModal' ) );

        // cart
        // add_action( 'woocommerce_simple_add_to_cart', array( $this, 'showOpenButton' ) );
        add_filter( 'woocommerce_get_item_data', array( $this, 'showBookingInfoInCartItem' ), 10, 2 );
        add_action( 'woocommerce_remove_cart_item', array( $this, 'removeBookingSessionProduct' ) );
        add_action( 'woocommerce_quantity_input_args', array( $this, 'validateBookingItemQuantity' ), 10, 2 );
        // TODO disable qty input in cart item for bookable products

        // add_filter( 'woocommerce_add_cart_item_data', array( $this, '' ) );

        // order
        // // add_action( 'woocommerce_add_order_item_meta', array( $this, 'addBookingsToOrderItem' ), 10, 2 );
        // add_action( 'woocommerce_new_order_item', array( $this, 'addBookingsToOrderItem' ), 10, 3 );

        add_action( 'woocommerce_checkout_order_created', array( $this, 'save_order' ) );
        add_action( 'woocommerce_payment_complete', array( $this, 'woocommerce_payment_complete' ) );
        add_action( 'woocommerce_order_status_changed', array( $this, 'woocommerce_order_status_changed' ), 10, 4 );

        add_action( 'thwcfd_order_details_before_custom_fields_table', array( $this, 'addHeadingInThankYouPage' ) );
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

    public function woocommerce_payment_complete( $order_id ) {
        $this->apex_confirm_appointments( $order_id );
    }

    public function woocommerce_order_status_changed( $order_id, $from, $to, $order ) {
        if ( in_array( $to, array( 'processing', 'completed' ) ) ) {
            $this->apex_confirm_appointments( $order_id );
        }
    }

    protected function apex_confirm_appointments( $order_id ) {
        $order = wc_get_order( $order_id );
        $apex_appointment_ids = $order->get_meta( 'apex_appointment_ids' ) ?: array();

        if ( ! empty( $apex_appointment_ids ) ) {
            return;
        }

        $API = MG_Api_Apex::instance();

        foreach ( $order->get_items() as $item ) {
            $bookable_order_item = new MG_Bookable_Order_Item( $item->get_id() );
            $booking_items = $bookable_order_item->getBookings();
            foreach ( $booking_items as $booking_item ) {
                // $instance_id = $API->create_appointment( $booking_item );
                $success = $API->confirm_appointment( $booking_item );
                if ( $success ) {
                    $apex_appointment_ids[ $booking_item->getId() ] = $booking_item->getApexAppointmentId();
                }
            }
        }

        $order->update_meta_data( 'apex_appointment_ids', $apex_appointment_ids );
        $order->save();
    }

    public function save_order( $order ) {
        $mg_order = new MG_Order( $order );
        // FIXME: checar si un producto agendable puede ser comprado sin tener que agendar
        if ( ! $mg_order->has_booking_item() ) {
            return;
        }

        $mg_order->update_meta_data( 'mgb_booking_data', MG_Booking_Session::getData() );

        foreach ( $mg_order->get_items() as $item_id => $item ) {
            $this->addBookingsToOrderItem( $item_id );
        }

        MG_Booking_Session::clean();
    }

    public function getCalendar() {
        return $this->calendar;
    }

    public function loadModal() {
        global $product;
        $mg_product = new MG_Product( $product );

        if ( ! $mg_product->is_agendable() ) {
            return;
        } 

        $form = $this;
        include_once MGB_PLUGIN_PATH . '/templates/booking-form/modal.php';
    }

    public function showFields() {
        $form = $this;
        include_once MGB_PLUGIN_PATH . '/templates/booking-form/fields.php';
    }

    public function showOpenButton() {
        // global $product;
        // $mg_product = new MG_Product( $product );

        // if ( ! $mg_product->is_agendable() ) {
        //     return;
        // }

        include_once MGB_PLUGIN_PATH . '/templates/booking-form/open-button.php';
    }
    
    public function handleConfigSave() {
        if ( isset( $_POST['mgb-booking-save'] ) && '1' === $_POST['mgb-booking-save'] ) {
            $booking_item = MG_Booking_Item_Session::createFromRequest( $_POST );

            if ( $booking_item instanceof WP_Error ) {
                array_map( 'wc_add_notice', $booking_item->get_error_messages(), array_fill( 0, count( $booking_item->get_error_messages() ), 'error' ) );
            }

            $apex = MG_Api_Apex::instance();
            $apex_appointment_id = $apex->create_appointment( $booking_item );

            if ( ! $apex_appointment_id ) {
                wc_add_notice( 'Error al agendar: APEX no pudo crear la cita', 'error' );
            } else {
                $booking_item->setApexAppointmentId( $apex_appointment_id );
                $booking_item->save();
            }

            WC()->cart->add_to_cart( $booking_item->getProductId() );

            wp_safe_redirect( wc_get_cart_url() );
            exit();
        }
    }

    protected function addBookingsToOrderItem( $item_id ) {
        $item       = new MG_Bookable_Order_Item( $item_id );
        $product_id = $item->get_product_id();

        $bookings = MG_Booking_Session::getProductBookings( $product_id );

        if ( is_array( $bookings ) ) {
            $item->saveBookings( $bookings );
        }
    }

    public function showBookingInfoInCartItem( $item_data, $cart_item ) {
        $product_id = $cart_item['product_id'];

        $mg_product = new MG_Product( $product_id );

        if ( ! $mg_product->is_agendable() ) {
            return $item_data;
        }

        $bookings = MG_Booking_Session::getProductBookings( $product_id );

        if ( ! empty( $bookings ) ) {
            foreach ( $bookings as $booking_id => $data ) {
                $item = new MG_Booking_Item_Session( $product_id, $booking_id );

                $item_data[ $booking_id ]            = array();
                $item_data[ $booking_id ]['key']     = $item->getKey();
                $item_data[ $booking_id ]['display'] = $item->getLabel();
            }
        }

        return $item_data;
    }

    public function removeBookingSessionProduct( $item_key ) {
        $cart_item    = WC()->cart->get_cart_item( $item_key );
        $product_id   = $cart_item['product_id'];

        $mg_product = new MG_Product( $product_id );

        if ( ! $mg_product->is_agendable() ) {
            return;
        }

        MG_Booking_Session::removeProduct( $product_id );
    }

    public function cleanBookingSession() {

    }
}

/**
 * Prints scripts or data before the closing body tag on the front end.
 *
 */
add_action( 'wp_footer', function() : void {
    // dd( MG_Booking_Session::getData() );
} );
