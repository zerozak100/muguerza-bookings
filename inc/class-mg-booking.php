<?php

class MG_Booking extends WC_Data {

    /**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'booking';

    /**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'booking';

	/**
	 * Cache group.
	 *
	 * @var string
	 */
	protected $cache_group = 'bookings';

    /**
	 * Stores booking data.
	 *
	 * @var array
	 */
	protected $data = array(
        'title'               => '',
		'description'		  => '',
		'short_description'	  => '',
        'product_id'          => '',
        'datetime'            => '', // stored as string [d/m/Y g:i a]
        'apex_calendar_id'    => '',
        'apex_appointment_id' => '',
        'apex_status'         => '', // [P, Y, N]
		'status'			  => 'publish',
		'order_id'			  => '',
		'order_item_id'		  => '',
		'cart_item_key'		  => '',
		'timezone'			  => '',
		// patient
        'name'        		  => '',
        'lastname1'   		  => '',
        'lastname2'   		  => '',
        'email'               => '',
        'phone'               => '',
        'birthdate'           => '',
        'sex'                 => '',
        'age'                 => '',
        'birth_state'         => '',
        'curp'                => '',
	);

    /**
	 * Get the booking if ID is passed, otherwise the booking is new and empty.
	 * This class should NOT be instantiated, but the wc_get_product() function
	 * should be used. It is possible, but the wc_get_product() is preferred.
	 *
	 * @param int|MG_Booking|object $booking Booking to init.
	 */
	public function __construct( $booking = 0 ) {
		parent::__construct( $booking );
		if ( is_numeric( $booking ) && $booking > 0 ) {
			$this->set_id( $booking );
		} elseif ( $booking instanceof self ) {
			$this->set_id( absint( $booking->get_id() ) );
		} elseif ( ! empty( $booking->ID ) ) {
			$this->set_id( absint( $booking->ID ) );
		} else {
			$this->set_object_read( true );
		}

		$this->data_store = WC_Data_Store::load( 'booking' );
		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
		}
	}

    /*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Functions for setting product data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	*/

	public function get_order_id( $context = 'view' ) {
		return $this->get_prop( 'order_id', $context );
	}

	public function get_order_item_id( $context = 'view' ) {
		return $this->get_prop( 'order_item_id', $context );
	}

	public function get_cart_item_key( $context = 'view' ) {
		return $this->get_prop( 'cart_item_key', $context );
	}

	public function get_timezone( $context = 'view' ) {
		return $this->get_prop( 'timezone', $context );
	}

	public function get_status( $context = 'view' ) {
		return $this->get_prop( 'status', $context );
	}

    public function get_title( $context = 'view' ) {
		return $this->get_prop( 'title', $context );
    }

	public function get_description( $context = 'view' ) {
		return $this->get_prop( 'description', $context );
    }

	public function get_short_description( $context = 'view' ) {
		return $this->get_prop( 'short_description', $context );
    }

    /**
	 * Get product ID.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return int
	 */
	public function get_product_id( $context = 'view' ) {
		return $this->get_prop( 'product_id', $context );
	}

    public function get_name( $context = 'view' ) {
		return $this->get_prop( 'name', $context );
	}

    public function get_lastname1( $context = 'view' ) {
		return $this->get_prop( 'lastname1', $context );
	}

    public function get_lastname2( $context = 'view' ) {
		return $this->get_prop( 'lastname2', $context );
	}

	/**
	 * Date format [d/m/Y g:i a]
	 */
    public function get_datetime( $context = 'view' ) {
		return $this->get_prop( 'datetime', $context );
    }

    public function get_email( $context = 'view' ) {
		return $this->get_prop( 'email', $context );
    }

    public function get_phone( $context = 'view' ) {
		return $this->get_prop( 'phone', $context );
    }

    public function get_birthdate( $context = 'view' ) {
		return $this->get_prop( 'birthdate', $context );
    }

    public function get_sex( $context = 'view' ) {
		return $this->get_prop( 'sex', $context );
    }

    public function get_age( $context = 'view' ) {
		return $this->get_prop( 'age', $context );
    }

    public function get_birth_state( $context = 'view' ) {
		return $this->get_prop( 'birth_state', $context );
    }

    public function get_curp( $context = 'view' ) {
		return $this->get_prop( 'curp', $context );
    }

    public function get_apex_calendar_id( $context = 'view' ) {
		return $this->get_prop( 'apex_calendar_id', $context );
    }

    public function get_apex_appointment_id( $context = 'view' ) {
		return $this->get_prop( 'apex_appointment_id', $context );
    }

    public function get_apex_status( $context = 'view' ) {
		return $this->get_prop( 'apex_status', $context );
    }

    /*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting product data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	*/

	public function set_order_id( $value ) {
		$this->set_prop( 'order_id', $value );
	}

	public function set_order_item_id( $value ) {
		$this->set_prop( 'order_item_id', $value );
	}

	public function set_cart_item_key( $value ) {
		$this->set_prop( 'cart_item_key', $value );
	}

	public function set_timezone( $value ) {
		$this->set_prop( 'timezone', $value );
	}

	public function set_status( $value ) {
		$this->set_prop( 'status', $value );
    }

    public function set_title( $value ) {
		$this->set_prop( 'title', $value );
    }

	public function set_description( $value ) {
		$this->set_prop( 'description', $value );
	}

	public function set_short_description( $value ) {
		$this->set_prop( 'short_description', $value );
	}

    /**
	 * Get product ID.
	 *
	 * @param  string $context What the value is for. Valid values are 'view' and 'edit'.
	 * @return int
	 */
	public function set_product_id( $value ) {
		$this->set_prop( 'product_id', $value );
	}

    public function set_name( $value ) {
		$this->set_prop( 'name', $value );
	}

    public function set_lastname1( $value ) {
		$this->set_prop( 'lastname1', $value );
	}

    public function set_lastname2( $value ) {
		$this->set_prop( 'lastname2', $value );
	}

    public function set_datetime( $value ) {
		$this->set_prop( 'datetime', $value );
    }

    public function set_email( $value ) {
		$this->set_prop( 'email', $value );
    }

    public function set_phone( $value ) {
		$this->set_prop( 'phone', $value );
    }

    public function set_birthdate( $value ) {
		$this->set_prop( 'birthdate', $value );
    }

    public function set_sex( $value ) {
		$this->set_prop( 'sex', $value );
    }

    public function set_age( $value ) {
		$this->set_prop( 'age', $value );
    }

    public function set_birth_state( $value ) {
		$this->set_prop( 'birth_state', $value );
    }

    public function set_curp( $value ) {
		$this->set_prop( 'curp', $value );
    }

    public function set_apex_calendar_id( $value ) {
		$this->set_prop( 'apex_calendar_id', $value );
    }

    public function set_apex_appointment_id( $value ) {
		$this->set_prop( 'apex_appointment_id', $value );
    }

    public function set_apex_status( $value ) {
		$this->set_prop( 'apex_status', $value );
    }

	/*
	|--------------------------------------------------------------------------
	| Additional Methods
	|--------------------------------------------------------------------------
	*/

	/**
     * Periodo 1
     * 
     * Tiempo entre que el cliente agrega al carrito y crea su pedido
     */
    public function schedule_cancelation_1() {
        $args = array(
            'type' => self::class,
            'data' => array(
                'product_id' => $this->get_product_id(),
                'booking_id' => $this->get_id(),
            ),
        );

        as_schedule_single_action( strtotime( '+20 minutes' ), 'muguerza_cancel_booking_item', array_values( $args ) );
    }

	public function schedule_cancelation_2() {
        // $order_id = wc_get_order_id_by_order_item_id( $this->get_order_item_id() );
        $order    = wc_get_order( $this->get_order_id() );

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
                'order_item_id' => $this->get_product_id(),
                'booking_id'    => $this->get_id(),
            ),
        );

        as_schedule_single_action( $tolerance, 'muguerza_cancel_booking_item', array_values( $args ) );
    }

    /**
	 * Get internal type. Should return string and *should be overridden* by child classes.
	 *
	 * The product_type property is deprecated but is used here for BW compatibility with child classes which may be defining product_type and not have a get_type method.
	 *
	 * @since  3.0.0
	 * @return string
	 */
	// public function get_type() {
	// 	return isset( $this->product_type ) ? $this->product_type : 'simple';
	// }
}
