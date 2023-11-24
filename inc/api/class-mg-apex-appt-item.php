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

    public static function from_booking( MG_Booking $booking ) {
        $apex_item = new self();

        $apex_item->p_email           = $booking->get_email();
        $apex_item->p_calendar        = $booking->get_apex_calendar_id();
        $apex_item->p_date_start      = $booking->get_datetime();
        $apex_item->p_curp            = $booking->get_curp();
        $apex_item->p_state           = $booking->get_birth_state();
        $apex_item->p_birth_date      = $booking->get_birthdate();
        $apex_item->p_first_name      = $booking->get_name();
        $apex_item->p_first_lastname  = $booking->get_lastname1();
        $apex_item->p_second_lastname = $booking->get_lastname2();
        $apex_item->p_genre           = $booking->get_sex();
        $apex_item->p_phone           = $booking->get_phone();
        $apex_item->p_age             = $booking->get_age();

        return $apex_item;
    }

    public static function from_array( array $data ) {
        return new self( $data );
    }

    public function __construct( array $data = array() ) {

        $_body = array(
            'p_email'           => 'ARTURO.SALAS@CHRISTUS.MX',
            'p_dayweek'         => '4',
            // 'p_user'            => 'api',
            // 'p_confirm'          => 'Y',
            // 'p_pacient_name'    => 'arturo salas',
            'p_calendar'        => '51',
            // 'p_date_finish'      => '2023-07-02T12:00:00',
            // 'p_unit'            => '1',
            // 'p_phone'           => '8127325334',
            'p_date_start'      => '2023-07-04T11:00:00',
            // 'p_comments'        => '',
            // 'p_title'           => '10:00 AM a 11:00 PM-CITA: ARTURO SALAS FLORES',
            // 'p_curp'            => 'SAFA92',
            // 'p_state'           => 'NL',
            // 'p_birth_date'      => '',
            // 'p_genre'           => '',
            // 'p_second_lastname' => '',
            // 'p_first_lastname'   => '',
            // 'p_middle_name'     => '',
            // 'p_first_name'       => '',
        );
        
        $data = wp_parse_args( $data, array(
            'p_email'           => '',
            'p_calendar'        => '',
            'p_date_start'      => '',
            'p_curp'            => '',
            'p_state'           => '',
            'p_birth_date'      => '',
            'p_first_name'       => '',
            'p_first_lastname'   => '',
            'p_second_lastname' => '',
            'p_genre'           => '',
            'p_phone'           => '',
            'p_age'             => '',
            'p_confirm'          => 'P',
        ) );

        $this->p_email           = $data['p_email'];
        $this->p_calendar        = $data['p_calendar'];
        $this->p_date_start      = $data['p_date_start'];
        $this->p_curp            = $data['p_curp'];
        $this->p_state           = $data['p_state'];
        $this->p_birth_date      = $data['p_birth_date'];
        $this->p_first_name       = $data['p_first_name'];
        $this->p_first_lastname   = $data['p_first_lastname'];
        $this->p_second_lastname = $data['p_second_lastname'];
        $this->p_genre           = $data['p_genre'];
        $this->p_phone           = $data['p_phone'];
        $this->p_age             = $data['p_age'];
        $this->p_confirm          = $data['p_confirm'];
    }

    public function get_data() {
        return ( array ) $this;
    }
}
