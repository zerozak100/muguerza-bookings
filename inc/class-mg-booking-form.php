<?php

// TODO: disable qty input in cart item for bookable products

/**
 * @property MG_Booking_Form $instance
 * @property MG_Calendar $calendar
 */
class MG_Booking_Form {

    const FORM_FIELDS = array(
        'datetime'    => 'Fecha y hora',
        'product_id'  => 'ID del producto',
        'name'        => 'Nombre',
        'email'       => 'Correo electrónico',
        'lastname1'   => 'Apellido paterno',
        'lastname2'   => 'Apellido materno',
        'phone'       => 'Celular',
        'birthdate'   => 'Fecha de nacimiento',
        'sex'         => 'Sexo',
        // 'age'         => 'Edad',
        // 'birth_state' => 'Estado de nacimiento',
        // 'curp'        => 'CURP',
        'timezone'    => 'Zona horaria',
        'mgb-booking-save' => 'mgb-booking-save',
    );

    const DATETIME_FORMAT = 'Y-m-d H:i';
    const BIRTHDATE_FORMAT = 'Y-m-d';

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
        add_action( 'woocommerce_checkout_create_order', array( $this, 'validate_bookings' ) );
        add_action( 'woocommerce_checkout_order_created', array( $this, 'updateBookingsWithOrder' ) );
        add_action( 'woocommerce_payment_complete', array( $this, 'apexConfirmAppointments' ) );
        add_action( 'woocommerce_order_status_changed', array( $this, 'woocommerce_order_status_changed' ), 10, 4 );

        add_action( 'thwcfd_order_details_before_custom_fields_table', array( $this, 'addHeadingInThankYouPage' ) );

        // add_action( 'muguerza_cancel_booking', function( $type, $data ) {
        //     add_action( 'template_redirect', function() use ( $type, $data ) {
        //         $this->cancel_booking_item( $type, $data );
        //     } );
        // }, 10, 2 );

        add_action( 'muguerza_cancel_booking', array( $this, 'cancel_booking_item' ), 10, 2 );

        // add_filter( 'woocommerce_add_cart_item_data', array( $this, '' ) );
        // add_action( 'woocommerce_add_order_item_meta', array( $this, 'addBookingsToOrderItem' ), 10, 2 );
        // add_action( 'woocommerce_new_order_item', array( $this, 'addBookingsToOrderItem' ), 10, 3 );
    }

    public function validate_bookings( $order ) {
        $session_bookings = MG_Booking_Session::getAll( true );
        foreach ( $session_bookings as $booking_id ) {
            $booking = new MG_Booking( $booking_id );
            if ( 'N' === $booking->get_apex_status() ) {
                throw new Exception( 'Expiró el tiempo para confirmar agenda para uno o más artículos' ); // TODO: redireccionar y borrar carrito completo o borrar en automatico la sesion o al entrar en pagina de checkout borrar pendientes
                // MG_Booking_Session::clean();
                // WC()->cart->empty_cart();
                // wc_add_notice( 'Expiró el tiempo para confirmar agenda para uno o más artículos', 'error' );
                // wp_safe_redirect( wc_get_cart_url() );
            }
        }
    }

    /**
     * @param WC_Order $order
     */
    public function updateBookingsWithOrder( $order ) {
        $mg_order = new MG_Order( $order );

        if ( ! $mg_order->has_bookable_product() ) {
            return;
        }

        $order_items    = $mg_order->get_items();
        $session_bookings = MG_Booking_Session::getAll( true );

        foreach ( $session_bookings as $booking_id ) {
            $booking = new MG_Booking( $booking_id );

            if ( $booking->get_order_id() ) {
                continue;
            }

            foreach ( $order_items as $item_id => $item ) {
                if ( $item['product_id'] == $booking->get_product_id() && $booking->get_product_id() ) {
                    $booking->set_order_item_id( $item_id );
                    $booking->set_order_id( $mg_order->get_id() );
                }
            }

            $booking->save();
            $booking->schedule_cancelation_2();
            MG_Booking_Session::saveBooking( $booking->get_cart_item_key(), $booking->get_id(), $booking->get_data() );
        }

        /**
         * Delete session with offline methods since payment will be processed later
         * 
         * Since the request to process payment will be trigger by other than the actual user we need to clear session now
         * 
         * conektacard | conektaoxxopay | conektaspei | cod
         */
        if ( in_array( $order->get_payment_method(), array( 'conektaoxxopay', 'conektaspei', 'cod' ) ) ) { // conektacard
            MG_Booking_Session::clean();
        }
    }

    /**
     * Saves booking in DB and Session
     */
    public function saveBooking() {
        if ( isset( $_POST['mgb-booking-save'] ) && '1' === $_POST['mgb-booking-save'] ) {
            $booking = MG_Bookings::create_from_request( $_POST );

            if ( $booking instanceof WP_Error ) {
                WC()->session->set( 'booking_form_data', MG_Bookings::get_data_from_request( $_POST ) );
                return array_map( 'wc_add_notice', $booking->get_error_messages(), array_fill( 0, count( $booking->get_error_messages() ), 'error' ) );
            }

            $apex = MG_Api_Apex::instance();
            $apex_appointment_id = $apex->create_appointment( $booking );

            if ( ! $apex_appointment_id ) {
                return wc_add_notice( 'Error al agendar: APEX no pudo crear la cita', 'error' );
            } else {
                $booking->set_apex_appointment_id( $apex_appointment_id );
            }

            $cart_item_key = WC()->cart->add_to_cart( $booking->get_product_id() );
            $booking->set_cart_item_key( $cart_item_key );
            $booking->save();
            $booking->schedule_cancelation_1();

            MG_Booking_Session::saveBooking( $booking->get_cart_item_key(), $booking->get_id(), $booking->get_data() );

            WC()->session->__unset( 'booking_form_data' );
            wp_safe_redirect( wc_get_cart_url() );
            exit();
        }
    }

    public function cancel_booking_item( $booking_id, $type ) {

        $booking = new MG_Booking( $booking_id );

        /**
         * Onced confirmed it can't be cancelled
         */
        if ( 'Y' === $booking->get_apex_status() ) {
            return;
        }

        /**
         * Onced booking has order it can't be canceled by type 1
         */
        if ( MG_Booking::CANCEL_TYPE_1 == $type && $booking->get_order_id() ) {
            return;
        }

        $api = MG_Api_Apex::instance();
        $success = $api->cancel_appointment( $booking );
        if ( $success ) {
            $booking->set_apex_status( 'N' );
            $booking->save();
            // mlog( did_action( 'template_redirect' ) );
            // MG_Booking_Session::removeBooking( $booking->get_cart_item_key(), $booking->get_id() );
            add_action( 'init', function () use ( $booking ) {
                // mlog( 'inside cancel_booking_item init' );
                $this->cancel_booking_item_session( $booking );
            } );
        }
    }

    public function cancel_booking_item_session( MG_Booking $booking ) {
        // mlog( 'inside cancel_booking_item_session' );
        MG_Booking_Session::removeBooking( $booking->get_cart_item_key(), $booking->get_id() );
    }

    /**
     * Cancel booking item only if status is of Pending payment
     * 
     * TODO: Session: remover o decrementar cart item al cancelar cita
     * TODO: Order: cancelar pedido al cancelar cita
     */
    // public function cancel_booking_item( $type, $data ) {
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
    // }

    public function init() {
        if ( is_product() ) {
            global $post;
            $apex_calendar_id = get_field( 'apex_calendar_id', $post->ID );

            $mg_product = new MG_Product( $post );

            if ( ! $apex_calendar_id && $mg_product->is_servicio() && $mg_product->is_agendable() ) {
                $user_id = get_current_user_id();
                $user = get_user_by( 'ID', $user_id );
                $error = 'El producto agendable no cuenta con un Calendar ID de APEX';

                if ( ! $user_id || ! in_array( 'administrator', $user->roles ) ) {
                    mg_redirect_with_error( home_url( 'servicios' ), $error );
                }

                // mg_redirect_with_error( get_edit_post_link( $post->ID, false ), $error );

                $adminnotice = new WC_Admin_Notices();
                $adminnotice->add_custom_notice("Hello","<div class='notice notice-error is-dismissible'><p>$error</p></div>");
                $adminnotice->output_custom_notices();

                wp_safe_redirect( get_edit_post_link( $post->ID, false ) );
                exit();

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

    public function apexConfirmAppointments( $order_id ) {
        $order = wc_get_order( $order_id );
        $success_appointments = $order->get_meta( 'success_appointments' ) ?: array();

        $API = MG_Api_Apex::instance();

        $order_bookings = MG_Bookings::get_bookings_from_order( $order_id );

        foreach ( $order_bookings as $booking ) {
            if ( ! in_array( $booking->get_id(), $success_appointments ) ) {
                $success = $API->confirm_appointment( $booking );
                if ( $success ) {
                    $$success_appointments[] = $booking->get_id();
                    $booking->set_apex_status( 'Y' );
                    $booking->save();
                }
            }
        }

        $order->update_meta_data( 'success_appointments', $success_appointments );
        $order->save();

        if ( isset( WC()->session ) ) {
            MG_Booking_Session::clean(); // TODO: care with offline payment methods
        }
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
                $data = ( object ) $data;
                $datetime = date( 'd/m/Y g:i a', strtotime( $data->datetime ) );

                $item_data[ "{$booking_id}_nombre" ]['key']      ='NOMBRE DEL PACIENTE';
                $item_data[ "{$booking_id}_nombre" ]['display']  = "$data->name $data->lastname1";
                $item_data[ "{$booking_id}_fecha" ]['key']       ='FECHA DE LA CITA';
                $item_data[ "{$booking_id}_fecha" ]['display']   = $datetime;
				$item_data[ "{$booking_id}_id_cita" ]['key']     ='ID DE LA CITA';
                $item_data[ "{$booking_id}_id_cita" ]['display'] = $data->id;
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

    /**
     * Get field from session
     */
    public function get_field( string $field ) {
        $form_data = WC()->session->get( 'booking_form_data' );

        if ( ! $form_data || ! is_array( $form_data ) || ! isset( $form_data[ $field ] ) ) {
            return '';
        }

        return $form_data[ $field ];
    }

    public static function input( $field, $type, $value ) {

    }
}
