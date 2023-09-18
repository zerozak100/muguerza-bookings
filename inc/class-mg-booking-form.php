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
        add_action( 'woocommerce_after_single_product', array( $this, 'loadModal' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'wp_loaded', array( $this, 'handleConfigSave' ), 20 );

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

        $this->calendar = new MG_Calendar( date( 'Y-m-d' ) );
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
        $this->apex_create_appointments( $order_id );
    }

    public function woocommerce_order_status_changed( $order_id, $from, $to, $order ) {
        if ( in_array( $to, array( 'processing', 'completed' ) ) ) {
            $this->apex_create_appointments( $order_id );
        }
    }

    protected function apex_create_appointments( $order_id ) {
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
                $instance_id = $API->create_appointment( $booking_item );
                $apex_appointment_ids[ $booking_item->getId() ] = $instance_id;
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

    public function scripts() {
        if ( is_product() ) {
            $this->calendar->scripts();
        }
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
            $errors = array();

            $fields = array(
                'datetime'         => 'Fecha y hora',
                'product_id'       => 'ID del producto',
                'name'             => 'Nombre',
                'email'            => 'Correo electrónico',
                'first_last_name'  => 'Apellido paterno',
                'second_last_name' => 'Apellido materno',
                'phone'            => 'Celular',
                'birthdate'        => 'Fecha de nacimiento',
            );

            foreach ( $fields as $key => $label ) {
                if ( ! isset( $_POST[ $key ] ) || ! $_POST[ $key ] ) {
                    $errors[] = "$label es requerido";
                }
            }
            
            if ( count( $errors ) > 0 ) {
                foreach ( $errors as $error ) {
                    wc_add_notice( $error, 'error' );
                }
                return;
            }

            $product_id = sanitize_text_field( $_POST['product_id'] );

            $data = array(
                'datetime'         => sanitize_text_field( $_POST['time'] ),
                'product_id'       => $product_id,
                'email'            => sanitize_email( $_POST['email'] ),
                'name'             => sanitize_text_field( $_POST['name'] ),
                'first_last_name'   => sanitize_text_field( $_POST['first_last_name'] ),
                'second_last_name' => sanitize_text_field( $_POST['second_last_name'] ),
                'phone'            => sanitize_text_field( $_POST['phone'] ),
                'birthdate'        => sanitize_text_field( $_POST['birthdate'] ),
                'sex'              => sanitize_text_field( $_POST['sex'] ),
                'age'              => sanitize_text_field( $_POST['age'] ),
                'birth_state'      => sanitize_text_field( $_POST['birth_state'] ),
                'curp'             => sanitize_text_field( $_POST['curp'] ),
            );

            // TODO: Agregar timezone
            $data['datetime'] = date( 'c', strtotime( $data['datetime'] ) );

            $booking_item = MG_Booking_Item_Session::create( $product_id, $data );

            WC()->cart->add_to_cart( $product_id );

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
