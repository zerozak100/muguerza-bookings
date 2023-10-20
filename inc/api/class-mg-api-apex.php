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

    public function get_available_time_list( DateTime $datetime ) {
        $time_list = array();

        $body = array(
            'AvailableTime' => array(
                'Parameters' => array(
                    array(
                        'p_dia_semana'  => '3',
                        'p_id_calendar' => '51',
                        'p_date'        => $datetime->format( 'd/m/Y' ),
                    ),
                ),
            ),
        );

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

    public function create_appointment( MG_Booking_Item_Order_Item $booking_item ) {

        $body = array(
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

        $body = array(
            'p_email'           => $booking_item->getEmail(),
            'p_calendar'        => '51',
            'p_date_start'      => $booking_item->getDatetime(),
            'p_curp'            => $booking_item->getCurp(),
            'p_state'           => $booking_item->getBirthState(),
            'p_birth_date'      => $booking_item->getBirthdate(),
            'p_first_name'       => $booking_item->getName(),
            'p_first_lastname'   => $booking_item->getFirstLastName(),
            'p_second_lastname' => $booking_item->getSecondLastName(),
            'p_genre'           => $booking_item->getSex(),
            'p_phone'           => $booking_item->getPhone(),
            'p_age'             => $booking_item->getAge(),
        );

        $response = $this->put( 'CalendarService/CreateAppointment', $body );

        if ( $response->ok ) {
            return $response->data['Response']['Instance']['InstanceID'];
        }

        return false;
    }

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
}