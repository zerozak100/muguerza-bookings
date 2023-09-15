<?php

class MG_Product extends WC_Product_Simple {
    public function is_especialidad() {
        $is_especialidad = false;

        $terms = get_the_terms( $this->get_id(), 'producto_tipo' );

        if ( ! empty( $terms ) ) {
            $is_especialidad = 'especialidad' === $terms[0]->slug;
        }

        return $is_especialidad;
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

    /**
     * Solo aplica para servicios
     */
    public function get_unidad_id() {
        $product_cat_id = $this->get_unidad_product_cat_id();
        return mg_get_product_cat_unidad_id( $product_cat_id );
    }

    /**
     * Solo aplica para servicios
     */
    public function get_unidad_product_cat_id() {
        return get_field( 'ubicacion', $this->get_id() );
    }
}
