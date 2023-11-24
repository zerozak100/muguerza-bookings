<?php

class MG_Api_Apex extends MG_Api {
    public static $instance;
    protected $request_access_token = true;

    /**
     * @return MG_Api_Apex
     */
    public static function instance() {
        if ( self::$instance ) {
            return self::$instance;
        }

        return new self();
    }

    public function get_available_time_list( DateTime $datetime, $calendar_id ) {
        $time_list = array();

        $data = array(
            'p_dia_semana'  => $datetime->format( 'N' ),
            'p_id_calendar' => $calendar_id,
            'p_date' => $datetime->format( 'd/m/Y' ),
        );

        $body = $this->get_body( 'AvailableTime', $data );

        $response = $this->post( 'CalendarService/GetAvailableTimeList', $body );

        if ( $response->ok ) {
            $data = $response->data;

            if ( isset( $data['AvailableTime'] ) && isset( $data['AvailableTime']['time_24hrs'] ) ) {
                $hours = $data['AvailableTime']['time_24hrs'];
                foreach ( $hours as $hour ) {
                    $time_list[] = $hour['Time_24'];
                }
            }
        }

        return $time_list;
    }

    /**
     * Crear cita
     * 
     * Por defecto las citas se crean como pendiente de pago al momento de añadirlas al carrito para separarlas
     * 
     * @param MG_Booking $booking Booking item
     */
    public function create_appointment( MG_Booking $booking ) {

        $apex_item = MG_Apex_Appt_Item::from_booking( $booking );

        $body = $this->get_body( 'CreationDate', $apex_item->get_data() );

        $response = $this->put( 'CalendarService/CreateAppointment', $body );

        if ( $response->ok ) {
            return $response->data['Response']['Instance']['InstanceID'];
        }

        return false;
    }

    // public function update_appointment( $apex_appointment_id, array $data ) {
    //     $apex_item = new MG_Apex_Appt_Item( $data );

    //     $defaults = array(
    //         'p_confirm' => 'P',
    //     );

    //     $body = wp_parse_args( $data, $defaults );
    //     $data = array_intersect( $apex_item->get_data(), $defaults );

    //     $response = $this->post( 'CalendarService/UpdateAppointment' );


    // }

    public function confirm_appointment( MG_Booking $booking ) {
        $body = array(
            'p_confirm' => 'Y',
            'id_event' => $booking->get_apex_appointment_id(),
        );

        $response = $this->post( 'CalendarService/UpdateAppointment', $body );

        return $response->ok;
    }

    public function cancel_appointment( MG_Booking $booking ) {
        $body = array(
            'p_confirm' => 'N',
            'id_event'  => $booking->get_apex_appointment_id(),
        );

        $response = $this->post( 'CalendarService/UpdateAppointment', $body );

        return $response->ok;
    }

    /**
     * 
     */
    public function consult_appointment( MG_Booking $booking ) {
        $params = array(
            'id_cita' => $booking->get_apex_appointment_id(),
        );

        $response = $this->get( 'CalendarService/GetConsultAppoiment', $params );

        return $response;
    }

    // function hold_appointment

    // TODO: guardar credenciales en el wp-config.php
    protected function get_access_token(): string {
        $access_token = '';

        $url = 'https://idcs-8332050b9ca94ab48f84d174e8db9675.identity.oraclecloud.com/oauth2/v1/token';

        $body = array(
            'scope'      => 'christusmuguerza.com.mx/Ecommerce',
            'grant_type' => 'client_credentials',
        );

        $username   = '8c4e30a91aed4b68a4ed0994d4a18f8c';
        $password   = 'd16a98e1-5ccf-4c13-9fb8-2495157cbf81';
        $auth_token = base64_encode( "$username:$password" );

        $response = wp_remote_post( $url, array(
            'timeout' => 60,
            'body'    => $body,
            'headers' => array(
                'Content-Type'  => 'application/x-www-form-urlencoded',
                'Accept'        => 'application/json',
                'Authorization' => "Basic $auth_token",
            ),
        ));

        $response = new MG_Api_Response( $response );

        if ( $response->ok ) {
            $access_token = $response->data['access_token'];
        }

        return $access_token;
    }

    protected function get_base_url(): string {
        return 'https://servicios-oic-dev.christus.mx';
    }

    protected function get_access_token_name(): string {
        return 'apex_access_token';
    }

    protected function get_body( string $name, array $data ) {
        return array(
            'Header' => array(
                'TrackinID' => '232',
                'SOurce'    => 'APEX',
            ),
            $name => array(
                'Parameters' => array( $data )
            ),
        );
    }
}
