<?php

abstract class MG_Api {
    protected $base_url;
    protected $timeout = 60;
    protected $headers = array(
        'Content-Type' => 'application/json',
        'Accept'       => 'application/json',
    );

    protected $access_token;
    protected $request_access_token = false;

    public function __construct() {

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

        $response = wp_remote_request( $url, array(
            'method'  => $method,
            'timeout' => $this->timeout,
            'body'    => wp_json_encode( $body ),
            'headers' => array_merge( $this->headers, $headers ),
        ) );

        $response = new MG_Api_Response( $response );

        mg_log( $response->code );
        mg_log( $response->data );
        mg_log( $response->message );

        // FIXME: cuidado con loop infinito
        if ( $this->request_access_token && $response->code === 401 ) {
            $this->access_token = $this->get_access_token();
            $this->set_headers( array( 'Authorization' => "Bearer {$this->access_token}" ) );
        }

        return $response;
    }

    private function get_url( string $endpoint, array $params ) {
        $query = http_build_query( $params );
        return "{$this->base_url}/{$endpoint}?{$query}";
    }

    abstract public function get_access_token(): string;
}
