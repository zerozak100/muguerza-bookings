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

    public function get_available_time_list( DateTimeImmutable $datetime, $calendar_id ) {
        $time_list = array();

        $start_date = $datetime;
        $finish_date = $start_date->modify( '+5 days' );

        $data = array(
            'p_id_calendar' => $calendar_id,
            'p_star_date'   => $start_date->format( 'c' ),
            'p_finish_date' => $finish_date->format( 'c' ),
        );

        $body = $this->get_body( 'AvailableTime', $data );

        $response = $this->post( 'CalendarService/AvailableTimeListByDateRange', $body );

        if ( $response->ok ) {
            $data = $response->data;

            if ( isset( $data['AvailableTime'] ) && isset( $data['AvailableTime']['ListDate'] ) ) {
                foreach ( $data['AvailableTime']['ListDate'] as $day ) {
                    $date = DateTime::createFromFormat( 'd/m/Y', $day['Date'] );
                    $date_key = $date->format( 'Y-m-d' );
                    $time_list[ $date_key ] = array();
                    if ( isset( $day['time24hrs'] ) ) {
                        $time_list[ $date_key ] = $day['time24hrs'];
                    }
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
        $data = array(
            'p_confirm'  => 'Y',
            'id_event'   => $booking->get_apex_appointment_id(),
            'p_comments' => 'actualización via Api-Rest', // Solicitado por el equipo de APEX
        );

        $body = $this->get_body( 'UpdateDate', $data );

        $response = $this->post( 'CalendarService/UpdateAppointment', $body );

        return $response->ok;
    }

    /**
     * Cancel appoinment
     * 
     * Only this service can actually cancel an appointment
     * 
     * Special case, only works with POST, parameters and without application/json header
     */
    public function cancel_appointment( MG_Booking $booking ) {
        $data = array(
            'id_event'  => $booking->get_apex_appointment_id(),
            'p_user'    => 'tienda',
            'p_comment' => "cancelacion por falta de pago", // TODO: cambiar dependiendo el motivo
        );

        $body = $this->get_body( 'CancelDate', $data );

        $response = $this->post( 'CalendarService/CancelAppointment', $body );

        return $response->ok;
    }

    public function consult_appointment( MG_Booking $booking ) {
        $params = array(
            'id_cita' => $booking->get_apex_appointment_id(),
        );

        $response = $this->get( 'CalendarService/GetConsultAppoiment', $params );

        return $response;
    }

    protected function get_access_token(): string {
        $access_token = '';

        $url = MG_API_APEX_OAUTH_URL;

        $body = array(
            'scope'      => MG_API_APEX_OAUTH_SCOPE,
            'grant_type' => MG_API_APEX_OAUTH_GRANT_TYPE,
        );

        $username   = MG_API_APEX_OAUTH_USER;
        $password   = MG_API_APEX_OAUTH_PASSWORD;
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
