<?php

use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

abstract class MG_Api {
    protected $base_url;
    protected $timeout = 60;
    protected $headers = array(
        'Content-Type' => 'application/json',
        'Accept'       => 'application/json',
    );

    protected $access_token;
    protected $request_access_token = false;

    protected function __construct() {
        $this->base_url = $this->get_base_url();
        $this->access_token = get_option( $this->get_access_token_name() );

        if ( ! $this->access_token ) {
            $this->access_token = $this->get_access_token();
        }

        $this->set_access_token( $this->access_token );
    }

    public function get( string $endpoint, array $params = array() ) {
        $config = array(
            'params' => $params,
        );

        return $this->request( $endpoint, 'GET', $config );
    }

    public function post( string $endpoint, array $body = array(), array $params = array() ) {
        $config = array(
            'body'   => $body,
            'params' => $params,
        );

        return $this->request( $endpoint, 'POST', $config );
    }

    public function put( string $endpoint, array $body = array(), array $params = array() ) {
        $config = array(
            'body'   => $body,
            'params' => $params,
        );

        return $this->request( $endpoint, 'PUT', $config );
    }

    public function get_headers() {
        $headers = $this->headers;
    }

    public function set_headers( $headers ) {
        $this->headers = array_merge( $this->headers, $headers );
    }

    public function request( string $endpoint, string $method, array $config = array() ): MG_Api_Response {
        $params  = isset( $config['params'] )  ? $config['params']  : array();
        $body    = isset( $config['body'] ) ? $config['body'] : array();
        $headers = isset( $config['headers'] ) ? $config['headers'] : array();

        $url = $this->get_url( $endpoint, $params );

        // dd( $this->headers, $headers, array_merge( $this->headers, $headers ) );
        // dd( $body );

        $data = array(
            'method'  => $method,
            'timeout' => $this->timeout,
            'body'    => wp_json_encode( $body ),
            'headers' => array_merge( $this->headers, $headers ),
        );

        if ( $method === 'GET' ) {
            unset( $data['body'] );
        }

        $response = wp_remote_request( $url, $data );

        $response = new MG_Api_Response( $response );

        // LOG START
        $logger = new Logger("API $endpoint");
        $logger->pushHandler( new StreamHandler( MG_LOGS_PATH . 'api.log') );
        $logger->pushHandler( new BrowserConsoleHandler() );
        $logger->info( $response .  ':: body :: ' . json_encode( $body ) );
        // LOG END

        // FIXME: cuidado con loop infinito
        if ( $this->request_access_token && $response->code === 401 ) {
            $this->access_token = $this->get_access_token();
            $this->set_access_token( $this->access_token );
            update_option( $this->get_access_token_name(), $this->access_token );
        }

        return $response;
    }

    private function get_url( string $endpoint, array $params ) {
        $query = http_build_query( $params );
        return "{$this->base_url}/{$endpoint}?{$query}";
    }

    public function set_access_token( $access_token ): void {
        $this->set_headers( array( 'Authorization' => "Bearer $access_token" ) );
    }

    abstract protected function get_access_token(): string;
    abstract protected function get_base_url(): string;
    abstract protected function get_access_token_name(): string;
}
