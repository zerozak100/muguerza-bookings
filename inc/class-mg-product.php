<?php

class MG_Product extends WC_Product_Simple {
    public function is_especialidad() {
        return has_term( 'especialidad', 'producto_tipo', $this->get_id() );
    }

    public function is_servicio() {
        return has_term( 'servicio', 'producto_tipo', $this->get_id() );
    }

    public function is_vendible() {
        return get_field( 'vendible', $this->get_id() );
    }

    public function is_agendable() {
        return get_field( 'agendable', $this->get_id() );
    }

    public function is_agendable_only() {
        return get_field( 'agendable_only', $this->get_id() );
    }

    public function is_vendible_without_agenda() {
        return $this->is_vendible() && ! $this->is_agendable_only();
    }

    /**
     * Solo aplica para servicios
     */
    public function get_unidad() {
        return new MG_Unidad( $this->get_unidad_id() );
    }

    public function get_unidad_id() {
        $unidad = get_field( 'unidad', $this->get_id() );

        if ( $unidad ) {
            if ( is_array( $unidad ) && count( $unidad ) ) {
                return $unidad[0];
            } else {
                return $unidad;
            }
        }

        return 0;
    }
}
