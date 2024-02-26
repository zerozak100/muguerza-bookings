<?php

class MG_Apex_Appt_Item {
    public $p_email;
    public $p_calendar;
    public $p_date_start;
    public $p_curp;
    public $p_state;
    public $p_birth_date;
    public $p_first_name;
    public $p_first_lastname;
    public $p_second_lastname;
    public $p_genre;
    public $p_phone;
    public $p_age;
    public $p_confirm;
    public $p_user; // Solicitado por el equipo de APEX
    public $p_lattribute1; // Product name

    public static function from_booking( MG_Booking $booking ) {
        $apex_item = new self();

        $datetime = DateTime::createFromFormat( MG_Booking_Form::DATETIME_FORMAT, $booking->get_datetime() );
        $birthdate = DateTime::createFromFormat( MG_Booking_Form::BIRTHDATE_FORMAT, $booking->get_birthdate() );

        $apex_item->p_email           = $booking->get_email();
        $apex_item->p_calendar        = $booking->get_apex_calendar_id();
        $apex_item->p_date_start      = $datetime->format( 'c' );
        // $apex_item->p_curp            = $booking->get_curp();
        // $apex_item->p_state           = $booking->get_birth_state();
        $apex_item->p_birth_date      = $birthdate->format( MG_Booking_Form::BIRTHDATE_FORMAT ); // Needs to be ISO8601 compliant
        $apex_item->p_first_name      = $booking->get_name();
        $apex_item->p_first_lastname  = $booking->get_lastname1();
        $apex_item->p_second_lastname = $booking->get_lastname2();
        $apex_item->p_genre           = $booking->get_sex();
        $apex_item->p_phone           = $booking->get_phone();
        // $apex_item->p_age             = $booking->get_age();
        $apex_item->p_user            = 'api'; // Solicitado por el equipo de APEX
        $apex_item->p_lattribute1     = get_the_title( $booking->get_product_id() );

        return $apex_item;
    }

    public static function from_array( array $data ) {
        return new self( $data );
    }

    public function __construct( array $data = array() ) {        
        $data = wp_parse_args( $data, array(
            'p_email'           => '',
            'p_calendar'        => '',
            'p_date_start'      => '',
            'p_curp'            => '',
            'p_state'           => '',
            'p_birth_date'      => '',
            'p_first_name'      => '',
            'p_first_lastname'  => '',
            'p_second_lastname' => '',
            'p_genre'           => '',
            'p_phone'           => '',
            'p_age'             => '',
            'p_confirm'         => 'P',
            'p_user'            => 'api',
            'p_lattribute1'     => '',
        ) );

        $this->p_email           = $data['p_email'];
        $this->p_calendar        = $data['p_calendar'];
        $this->p_date_start      = $data['p_date_start'];
        $this->p_curp            = $data['p_curp'];
        $this->p_state           = $data['p_state'];
        $this->p_birth_date      = $data['p_birth_date'];
        $this->p_first_name      = $data['p_first_name'];
        $this->p_first_lastname  = $data['p_first_lastname'];
        $this->p_second_lastname = $data['p_second_lastname'];
        $this->p_genre           = $data['p_genre'];
        $this->p_phone           = $data['p_phone'];
        $this->p_age             = $data['p_age'];
        $this->p_user            = $data['p_user'];
        $this->p_lattribute1     = $data['p_lattribute1'];
        $this->p_confirm         = $data['p_confirm'];
    }

    public function get_data() {
        return ( array ) $this;
    }
}
