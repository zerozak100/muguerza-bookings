<?php

abstract class MG_Booking_Item {

    protected $data = array(
        'id'               => '',
        'product_id'       => '',
        'name'             => '',
        'first_last_name'  => '',
        'second_last_name' => '',
        'datetime'         => '',
        'email'            => '',
        'phone'            => '',
        'birthdate'        => '',
        'sex'              => '',
        'age'              => '',
        'birth_state'      => '',
        'curp'             => '',
    );

    public static function loadFromSession() {

    }

    public static function loadFromOrderItem() {

    }

    public function getData() {

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

    //

    public function getKey() {
        return "Agenda {$this->getId()}";
    }

    public function getLabel() {
        return sprintf( '%s %s %s - %s', $this->getName(), $this->getFirstLastName(), $this->getSecondLastName(), $this->getDatetime() );
    }

    abstract public function save();

    public function snakeToCamel( $input ) {
        return lcfirst( str_replace( ' ', '', ucwords( str_replace( '_', ' ', $input ) ) ) );
    }

}