<?php

abstract class MG_Booking_Item {

    protected $data = array(
        'id'                  => '', // Local ID
        'product_id'          => '',
        'name'                => '',
        'first_last_name'     => '',
        'second_last_name'    => '',
        'datetime'            => '',
        'email'               => '',
        'phone'               => '',
        'birthdate'           => '',
        'sex'                 => '',
        'age'                 => '',
        'birth_state'         => '',
        'curp'                => '',
        'apex_calendar_id'    => '',
        'apex_appointment_id' => '',
        'status'              => '', // [P, Y, N]
    );

    public function getData() {
        return $this->data;
    }

    protected function getProp( $prop ) {
        return isset( $this->data[ $prop ] ) ? $this->data[ $prop ] : '';
    }

    protected function setProp( $prop, $value ) {
        $this->data[ $prop ] = $value;
    }

    public function getId() {
        return $this->getProp( 'id' );
    }

    public function getProductId() {
        return $this->getProp( 'product_id' );
    }

    public function getDatetime() {
        return $this->getProp( 'datetime' );
    }

    public function getName() {
        return $this->getProp( 'name' );
    }

    public function getFirstLastName() {
        return $this->getProp( 'first_last_name' );
    }

    public function getSecondLastName() {
        return $this->getProp( 'second_last_name' );
    }

    public function getEmail() {
        return $this->getProp( 'email' );
    }

    public function getPhone() {
        return $this->getProp( 'phone' );
    }

    public function getBirthdate() {
        return $this->getProp( 'birthdate' );
    }

    public function getSex() {
        return $this->getProp( 'sex' );
    }

    public function getAge() {
        return $this->getProp( 'age' );
    }

    public function getBirthState() {
        return $this->getProp( 'birth_state' );
    }

    public function getCurp() {
        return $this->getProp( 'curp' );
    }
    
    public function getApexCalendarId() {
        return $this->getProp( 'apex_calendar_id' );
    }

    public function getApexAppointmentId() {
        return $this->getProp( 'apex_appointment_id' );
    }

    public function getStatus() {
        return $this->getProp( 'status' );
    }

    // SETTERS

    public function setId( $value ) {
        $this->setProp( 'id', $value );
    }

    public function setProductId( $value ) {
        $this->setProp( 'product_id', $value );
    }

    public function setDatetime( $value ) {
        $this->setProp( 'datetime', $value );
    }

    public function setName( $value ) {
        $this->setProp( 'name', $value );
    }

    public function setFirstLastName( $value ) {
        $this->setProp( 'first_last_name', $value );
    }

    public function setSecondLastName( $value ) {
        $this->setProp( 'second_last_name', $value );
    }

    public function setEmail( $value ) {
        $this->setProp( 'email', $value );
    }

    public function setPhone( $value ) {
        $this->setProp( 'phone', $value );
    }

    public function setBirthdate( $value ) {
        $this->setProp( 'birthdate', $value );
    }

    public function setSex( $value ) {
        $this->setProp( 'sex', $value );
    }

    public function setAge( $value ) {
        $this->setProp( 'age', $value );
    }

    public function setBirthState( $value ) {
        $this->setProp( 'birth_state', $value );
    }

    public function setCurp( $value ) {
        $this->setProp( 'curp', $value );
    }

    public function setApexCalendarId( $value ) {
        $this->setProp( 'apex_calendar_id', $value );
    }

    public function setApexAppointmentId( $value ) {
        $this->setProp( 'apex_appointment_id', $value );
    }

    public function setStatus( $value ) {
        $this->setProp( 'status', $value );
    }

    //

    public function getKey() {
        return "Agenda {$this->getId()}";
    }

    public function getLabel() {
        return sprintf( '%s %s %s - %s', $this->getName(), $this->getFirstLastName(), $this->getSecondLastName(), $this->getDatetime() );
    }

    abstract public function save();

    abstract public function schedule_cancelation();

    abstract public function cancel();

    public function snakeToCamel( $input ) {
        return lcfirst( str_replace( ' ', '', ucwords( str_replace( '_', ' ', $input ) ) ) );
    }

}