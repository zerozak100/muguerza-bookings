<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class MG_API_Membresia extends MG_Api {
    public static $instance;
    protected $request_access_token = true;

    /**
     * @return MG_API_Membresia
     */
    public static function instance() {
        if ( self::$instance ) {
            return self::$instance;
        }

        return new self();
    }

    public function consultar_membresia( $email ) {
        $params = array(
            'p_email_m' => strtoupper( $email ),
        );

        $response = $this->get( 'Membresias/ConsultaMembresia', $params );

        if ( $response->ok ) {
            if ( ! empty( $response->data['Membresias'] ) ) {
                return $response->data;
            }
        }

        return false;
    }

    // TODO: guardar credenciales en el wp-config.php
    protected function get_access_token(): string {
        $access_token = '';

        $url        = 'https://idcs-8332050b9ca94ab48f84d174e8db9675.identity.oraclecloud.com/oauth2/v1/token';
        $username   = "ee582dd231684bb0801b2576b975e322";
        $password   = "174126f8-0d27-47eb-856a-a9fb7d903c29";
        $scope      = "christusmuguerza.com.mx/acsyt";
        $grant_type = "client_credentials";

        $config = array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( "$username:$password" ),
            ),
            "body" => array(
                'scope'      => $scope,
                'grant_type' => $grant_type,
            ),
        );

        $response = wp_remote_post( $url, $config );

        $response = new MG_Api_Response( $response );

        if ( $response->ok ) {
            $access_token = $response->data['access_token'];
        }

        return $access_token;
    }

    protected function get_base_url(): string {
        return 'https://servicios-ords-dev.christus.mx';
    }

    protected function get_access_token_name(): string {
        return 'membresia_access_token';
    }
}
