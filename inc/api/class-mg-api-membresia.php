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

    protected function get_access_token(): string {
        $access_token = '';

        $url        = MG_API_MEMBRESIA_OAUTH_URL;
        $username   = MG_API_MEMBRESIA_OAUTH_USER;
        $password   = MG_API_MEMBRESIA_OAUTH_PASSWORD;

        $config = array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( "$username:$password" ),
            ),
            "body" => array(
                'scope'      => MG_API_MEMBRESIA_OAUTH_SCOPE,
                'grant_type' => MG_API_MEMBRESIA_OAUTH_GRANT_TYPE,
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
