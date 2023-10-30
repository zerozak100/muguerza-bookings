<?php

class MG_Booking_Item_Session extends MG_Booking_Item {

    public function __construct( $product_id, $booking_id ) {
        $this->setId( $booking_id );
        
        $booking_data = MG_Booking_Session::getData();

        if ( isset( $booking_data[ $product_id ] ) && isset( $booking_data[ $product_id ][ $booking_id ] ) ) {
            $this->data = $booking_data[ $product_id ][ $booking_id ];
        }
    }

    public function save() {
        MG_Booking_Session::saveBookingItem( $this->getProductId(), $this->getId(), $this->data );
    }

    /**
     * @return WP_Error|MG_Booking_Item_Session
     */
    public static function createFromRequest( array $request ) {
        $data             = wc_clean( $request );
        $data['email']    = sanitize_email( $request['email'] );
        // TODO: Agregar timezone
        $data['datetime'] = date( 'c', strtotime( $data['datetime'] ) );

        // VALIDATION START

        $error = new WP_Error();

        $required_fields = array(
            'time'             => 'Fecha y hora',
            'product_id'       => 'ID del producto',
            'name'             => 'Nombre',
            'email'            => 'Correo electrónico',
            'first_last_name'   => 'Apellido paterno',
            'second_last_name' => 'Apellido materno',
            'phone'            => 'Celular',
            'birthdate'        => 'Fecha de nacimiento',
        );

        foreach ( $required_fields as $key => $label ) {
            if ( ! isset( $request[ $key ] ) || ! $request[ $key ] ) {
                $error->add( "{$key}_required", "$label es requerido" );
            }
        }

        if ( $data['product_id'] ) {
            $data['apex_calendar_id'] = get_field( 'apex_calendar_id', $data['product_id'] );
        }

        if ( ! $data['apex_calendar_id'] ) {
            $error->add( "apex_calendar_id_required", 'Producto no cuenta con Calendar ID para su agenda' );
        }

        if ( $error->has_errors() ) {
            return $error;
        }

        // VALIDATION END

        return MG_Booking_Item_Session::create( $data['product_id'], $data );
    }

    public static function create( $product_id, array $data ) {
        $booking_id = uniqid();
        $item       = new self( $product_id, $booking_id );

        foreach ( $data as $key => $value ) {
            $callable = $item->snakeToCamel( "set_$key" );
            if ( is_callable( array( $item, $callable ) ) ) {
				$item->{"$callable"}( $value );
            }
        }

        $item->save();

        return $item;
    }
}
