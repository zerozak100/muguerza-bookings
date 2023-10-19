<?php

/**
 * @property string $name
 * @property string $category
 * @property string $subcategory
 * @property string $description
 * @property array $price
 * @property array $price_membresia
 * @property int $current_post_id
 * @property WC_Product[] $products
 * 
 */
class MG_Import_Item_Servicio {
    public $data;

    public $name;
    public $category;
    public $subcategory;
    public $description;
    public $price = array(
        'CMAE' => '',
        'CMSur' => '',
        'CMC' => '',
        'CMV' => '',
        'CMSN' => '',
        'CSP' => '', // CHRISTUS MUGUERZA Clinica San Pedro
        'CMS' => '',
        'CMR' => '',
        'CMDP' => '',
        'CMJ' => '',
        'CMUPAEP' => '', // CHRISTUS MUGUERZA Hospital UPAEP
        'CMB' => '',
        'CMIRA' => '',
        'CMAG' => '',
        'CMFM' => '',
        'CAM Sendero' => '',
        'CAM Cumbres Elite' => '',
        'CAM Cumbres Leones' => '',
        'CAM Santa Catarina' => '',
        'CAM Universidad' => '',
        'CAM Gonzalitos' => '',
        'CAM Chapultepec' => '',
        'CAM Vergel' => '',
        'CAM Escobedo' => '',
        'CAM Huinalá' => '',
        'CAM Plaza Cristal' => '',
        'CAM Nogalera' => '',
        'CAM Periférico' => '', // no se reconoce
    );
    public $price_membresia = array(
        'CMC'     => '',
        'CMSN'    => '',
        'CSP'     => '', // no se reconoce
        'CMR'     => '',
        'CMUPAEP' => '', // no se reconoce
        'CMB'     => '',
        'CMAG'    => '',
        'CMFM'    => '',
    );
    public $price_sale = array();

    public $unidades_codes = array();

    public $current_post_id;

    public $products = array();

    public $is_agendable = array();

    public function __construct( array $item_data ) {
        unset( $item_data['CMAE Indic/Restricc'] );
        unset( $item_data['Descripción CMAE'] );
        unset( $item_data['UNIDAD'] );
        unset( $item_data['Padecimientos'] );
        unset( $item_data['Descripción CMV'] );

        $this->data = $item_data;

        $this->name        = $item_data['Nombre del artículo'];
        $this->category    = $item_data['Categoría'];
        $this->subcategory = $item_data['Subcategoría'];
        $this->description = $item_data['Descripción del producto'];

        foreach ( $item_data as $property => $value ) {
            if ( $this->is_property_unidad( $property ) ) {
                $this->set_price( $property, $value );
            }
        }
    }

    /**
     * Create products as many prices there are available
     */
    public function import_products() {
        $this->price = collect( $this->price )->filter()->toArray();
        $this->price_membresia = collect( $this->price_membresia )->filter()->toArray();

        // dd( $this );
        collect( $this->price )->filter()->each( array( $this, 'create_product' ) );
        // $price_membresia = collect( $this->price )->filter();
        // $price->each( array( $this, 'create_product' ) );
    }

    /**
     * Create product per unidad price
     */
    public function create_product( mixed $price_val, string $unidad_code ) {
        $unidad = MG_Unidad::from_abreviatura( $unidad_code );

        if ( ! $unidad->get_id() ) {
            return;
            // throw new Exception( "Unidad [$unidad_code] not found" );
        }

        $post_title = "{$this->name} {$unidad->get_name()}";

        if ( post_exists( $post_title, '', '', 'product', 'publish' ) ) {
            return;
        }
        
        $meta_input = array(
            'mg_import'       => '1',
            'mg_base_product' => sanitize_key( $this->name ),
            'vendible'        => '1',
            // 'agendable'       => '1',
            // 'agendable_only'  => '1',
        );

        if ( isset( $this->price_sale[ $unidad_code ] ) ) {
            $meta_input['_price']         = $this->price_sale[ $unidad_code ];
            $meta_input['_sale_price']    = $this->price_sale[ $unidad_code ];
            $meta_input['_regular_price'] = $this->price[ $unidad_code ];
        } else {
            $meta_input['_price']         = $this->price[ $unidad_code ];
            $meta_input['_regular_price'] = $this->price[ $unidad_code ];
        }

        if ( isset( $this->price_membresia[ $unidad_code ] ) && $this->price_membresia[ $unidad_code ] ) {
            $meta_input['_price_membresia'] = $this->price_membresia[ $unidad_code ];
        }

        $this->current_post_id = wp_insert_post( array(
            'post_title'   => $post_title,
            'post_content' => $this->description,
            'post_status'  => 'publish',
            'post_type'    => 'product',
            'meta_input'   => $meta_input,
        ) );

        $product = new WC_Product( $this->current_post_id );
        // $product->save();

        $this->save_tipo_de_producto();
        if ( $this->category ) $this->save_tipo_de_servicio();
        $this->save_unidad( $unidad );

        $this->products[] = $product;

    }

    public function configure_agendable() {
        foreach ( $this->data as $property => $value ) {

            if ( $this->is_property_unidad( $property ) ) {
                $code = $this->get_unidad_code_from_property( $property );
                $this->is_agendable[ $code ] = $this->transliterate( $value ) === 'si';
            }
        }

        foreach ( $this->is_agendable as $unidad_code => $is_agendable ) {

            if ( ! $is_agendable ) {
                continue;
            }

            $unidad     = MG_Unidad::from_abreviatura( $unidad_code );
            $post_title = "{$this->name} {$unidad->get_name()}";

            global $wpdb;
            $id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $wpdb->posts WHERE post_title = '%s'", $post_title ) );

            if ( $id ) {
                $this->current_post_id = ( int ) $id;
                $this->uf( 'agendable', '1' );
                $this->uf( 'agendable_only', '1' );
            }
        }
    }

    private function save_tipo_de_producto() {
        $servicio_term_id = '562';
        $this->uf( 'producto_tipo', $servicio_term_id );
    }

    private function save_tipo_de_servicio() {
        $taxonomy = 'tipos_servicios';

        $category_term_id = null;
        $subcategory_term_id = null;

        if ( term_exists( $this->category, $taxonomy ) ) {
            $category_term_id = get_term_by( 'name', $this->category, $taxonomy )->term_id;
        } else {
            $category_term_id = wp_insert_term( $this->category, $taxonomy )['term_id'];
        }

        if ( $this->subcategory ) {
            if ( term_exists( $this->subcategory, $taxonomy ) ) {
                $subcategory_term_id = get_term_by( 'name', $this->subcategory, $taxonomy )->term_id;
            } else {
                $subcategory_term_id = wp_insert_term( $this->subcategory, $taxonomy, array( 'parent' => $category_term_id ) );
            }
        }

        $terms = collect( array( $category_term_id, $subcategory_term_id ) )->filter()->values()->toArray();

        wp_set_post_terms( $this->current_post_id, $terms, $taxonomy );
        $this->uf( 'tipo_servicio', $terms );

        // $category_term = get_term_by( 'name', $this->category, 'tipos_servicios' );
        // $subcategory_term = get_term_by( 'name',  );

        // $this->uf( 'tipo_servicio', "tipos_servicios_$term_id" );
    }

    private function save_unidad( MG_Unidad $unidad ) {
        $this->uf( 'unidad', array( $unidad->mg_unidad->term_id ) );
    }

    private function uf( $key, $value ) {
        update_field( $key, $value, $this->current_post_id );
    }

    private function set_price( string $property, mixed $val ) {
        $code  = $this->get_unidad_code_from_property( $property );
        $price = $this->format_price( $val );
        
        switch ( $this->get_price_type( $property ) ) {
            case 'normal':
                if ( is_array( $price ) ) {
                    $this->price[ $code ]      = $price['regular'];
                    $this->price_sale[ $code ] = $price['oferta'];
                } else {
                    $this->price[ $code ] = $price;
                }
                break;
            case 'membresia':
                $this->price_membresia[ $code ] = $price;
                break;
        }
    }

    /**
     * @return float|array
     */
    private function format_price( $price ) {
        if ( $price && gettype( $price ) === 'string' ) {
            $price = str_replace( ',', '.', $price );

            if ( str_contains( $price, 'oferta' ) ) {
                $price = explode( "\n", $price );

                $regular = ( float ) preg_replace("/[^0-9.]/", "", $price[0]);
                $oferta  = ( float ) preg_replace("/[^0-9.]/", "", $price[1]);

                // if ( $this->name === 'Cultivo de orina (urocultivo)' ) {
                //     dd( $price, $regular, $oferta );
                // }

                return array( 'regular' => $regular, 'oferta' => $oferta );
            }

            return ( float ) $price;
        }

        return $price;
    }

    private function get_price_type( string $property ): string {
        return ! str_contains( $this->transliterate( $property ), 'precio membresia' ) ? 'normal' : 'membresia';
    }

    /**
     * If property has any unidad code then it is a price
     */
    private function is_property_unidad( string $property ) {
        return boolval( $this->get_unidad_code_from_property( $property ) );
    }

    private function get_unidad_code_from_property( string $property ) {
        foreach ( $this->get_unidades_codes() as $code ) {
            $pass = str_contains( $this->transliterate( $property ), $this->transliterate( $code ) );
            if ( $pass ) return $code;
        }
        return '';
    }

    private function transliterate( string $val ) {
        return strtolower( preg_replace( "/&([a-z])[a-z]+;/i", "$1", htmlentities( $val ) ) );
    }

    private function get_unidades_codes() {
        return array_keys( $this->price );
    }
}
