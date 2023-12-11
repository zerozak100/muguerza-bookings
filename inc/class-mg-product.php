<?php

class MG_Product extends WC_Product_Simple {
    public function is_especialidad() {
        return has_term( 'especialidad', 'producto_tipo', $this->get_id() );
    }

    public function is_servicio() {
        return has_term( 'servicio', 'producto_tipo', $this->get_id() );
    }

    public function is_maternidad() {
        if ( ! $this->is_servicio() ) {
            return false;
        }

        return has_term( 'maternidad', 'tipos_servicios', $this->get_id() );
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

    public function get_unidad() {
        return MG_Unidad::from_mg_unidad_id( $this->get_mg_unidad_id() );
    }

    /**
     * Unidad ID taxonomy term
     */
    public function get_mg_unidad_id() {
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
