<?php

use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * @property string $url
 * @property array $config
 * @property array $params
 * @property array $body Body is in JSON
 * @property array $headers
 * @property string $endpoint
 * @property string $method
 * @property MG_Api_Response $response
 */
class MG_Api_Request {
    public $url;
    public $config;

    public $params;
    public $body;
    public $headers;
    public $endpoint;
    public $method;

    public $response;

    public static function build_config( string $method, array $config ) {
        $params       = isset( $config['params'] )       ? $config['params']       : array();
        $body         = isset( $config['body'] )         ? $config['body']         : array();
        $headers      = isset( $config['headers'] )      ? $config['headers']      : array();

        $timeout      = isset( $config['timeout'] )      ? $config['timeout']      : 60;
        $base_headers = isset( $config['base_headers'] ) ? $config['base_headers'] : array();
        $endpoint     = isset( $config['endpoint'] )     ? $config['endpoint']     : array();

        $config = array(
            'method'    => $method,
            'timeout'   => $timeout,
            'body'      => wp_json_encode( $body ),
            'headers'   => array_merge( $base_headers, $headers ),
            'params'    => $params,
            'endpoint'  => $endpoint,
        );

        return ( object ) $config;
    }

    public static function build_url( $base_url, $endpoint, $params ) {
        $query = http_build_query( $params );
        return "{$base_url}/{$endpoint}?{$query}";
    }

    public function __construct( string $url, object $config ) {
        $this->url      = $url;
        $this->config    = $this->get_wp_config( $config );

        $this->params   = $config->params;
        $this->body     = $config->body;
        $this->headers  = $config->headers;
        $this->endpoint = $config->endpoint;
        $this->method   = $config->method;
    }

    public function get_response() {
        $this->response = new MG_Api_Response( wp_remote_request( $this->url, $this->config ), $this->endpoint );

        return $this->response;
    }

    public function log() {
        $request_logger  = new Logger( "API_REQUEST {$this->endpoint}" );
        $request_logger->pushHandler( new StreamHandler( MG_LOGS_PATH . 'api.log') );
        $request_logger->pushHandler( new BrowserConsoleHandler() );

        $request_logger->info( 'URL ' . $this->url );

        if ( $this->params ) {
            $request_logger->info( "PARAMS " . wp_json_encode( $this->params ) );
        }

        if ( $this->body ) {
            $request_logger->info( "BODY " . $this->body );
        }

        if ( $this->headers ) {
            // $request_logger->info( "HEADERS " . json_encode( $this->headers ) );
        }
    }

    public function get_wp_config( object $config ) {
        $_config = array(
            'method'  => $config->method,
            'timeout' => $config->timeout,
            'body'    => $config->body,
            'headers' => $config->headers,
        );

        if ( $config->method === 'GET' ) {
            unset( $_config['body'] );
        }

        return $_config;
    }
}