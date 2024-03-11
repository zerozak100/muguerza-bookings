<?php

use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * @property bool $ok
 * @property string $message
 * @property array $data
 * @property int $code
 * @property array|WP_Error $original_response
 */
class MG_Api_Response {
    public $ok;
    public $message;
    public $data;
    public $code;
    public $original_response;
    public $endpoint;
    public $logger;

    const HTTP_CODES = array(
        '401' => 'Sin autenticar',
        '404' => 'Recurso no encontrado',
        '500' => 'Error del servidor',
    );

    /**
     * From wp_remote_request
     * 
     * @param array|WP_Error $response
     */
    public function __construct( $response, $endpoint = '' ) {
        $this->endpoint = $endpoint;

        $this->logger = new Logger( "API_RESPONSE {$this->endpoint}" );
        $this->logger->pushHandler( new StreamHandler( MG_LOGS_PATH . 'api.log') );
        // $this->logger->pushHandler( new BrowserConsoleHandler() );

        if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message() );
        } else {
            $this->data              = json_decode( wp_remote_retrieve_body( $response ), true );
            $this->code              = wp_remote_retrieve_response_code( $response );
            $this->original_response = $response;

            if ( $this->code >= 200 && $this->code < 400 ) {
                $this->ok      = true;
                $this->message = 'Operación exitosa';
            } else {
                $this->ok      = false;
                $this->message = self::HTTP_CODES[ $this->code ];
            }
        }
    }

    public function __toString() {
        $data = json_encode( $this->data );
        return "[code {$this->code}] :: [message {$this->message}] :: [data $data]";
    }

    public function log() {
        $this->logger->info( 'CODE ' . $this->code );
        $this->logger->info( 'MESSAGE ' . $this->message );
        $this->logger->info( 'DATA ' . json_encode( $this->data ) );
    }

    // public static function send_json_error( $data, $status_code ) {
    //     $response = array( 'ok' => false );

    //     if ( isset( $data ) ) {
    //         if ( is_wp_error( $data ) ) {
    //             $result = array();
    //             foreach ( $data->errors as $code => $messages ) {
    //                 foreach ( $messages as $message ) {
    //                     $result[] = array(
    //                         'code'    => $code,
    //                         'message' => $message,
    //                     );
    //                 }
    //             }

    //             $response['data'] = $result;
    //         } else {
    //             $response['data'] = $data;
    //         }
    //     }

    //     wp_send_json( $response, $status_code );
    // }

}
