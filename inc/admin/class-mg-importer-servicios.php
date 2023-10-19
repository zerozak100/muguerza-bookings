<?php

class MG_Importer_Servicios extends MG_Importer {
    public static function render_page() : void {
        mgb_get_template( 'admin/importer/importer-servicios.php', array( 'importer_class' => self::class, 'importer_type' => self::get_importer_type() ) );
    }

    public static function get_importer_type() : string {
        return self::class;
    }

    public function import( array $data ) : void {
        $servicios = collect( $data );

        $servicios = $servicios->map( function( $servicio ) {
            return new MG_Import_Item_Servicio( $servicio );
        } );

        $servicios = $servicios->filter( function( MG_Import_Item_Servicio $servicio ) {
            return $servicio->category !== 'Promociones';
        });

        // $servicios = $servicios->splice( 0, 300 );

        $servicios->each( function ( MG_Import_Item_Servicio $item_servicio ) {
            $item_servicio->import_products();
        } );

        // dd( 'die' );
    }

    public function configure_agendables( array $data ) {
        $servicios = collect( $data );

        $servicios = $servicios->map( function( $servicio ) {
            return new MG_Import_Item_Servicio( $servicio );
        } );

        $servicios = $servicios->filter( function( MG_Import_Item_Servicio $servicio ) {
            return $servicio->category !== 'Promociones';
        });

        $servicios->each( function ( MG_Import_Item_Servicio $item_servicio ) {
            $item_servicio->configure_agendable();
        } );
    }

    public function delete_all() : void {
        /**
         * @var wpdb $wpdb
         */
        global $wpdb;

        // $wpdb->prepare( 'DELETE p FROM wp_posts p INNER JOIN wp_postmeta pm ON p.ID = pm.post_id WHERE pm.' );

        // return;
        $args = array(
            'post_type'   => 'product',
            'post_status' => 'any',
            'tax_query'   => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'producto_tipo',
                    'terms'    => 'servicio', // 562
                    'field'     => 'slug',
                ),
            ),
            // 'meta_query'  => array(
            //     'relation' => 'AND',
            //     array(
            //         'key'     => 'from_drupal',
            //         'value'   => '1',
            //         'compare' => '=',
            //     ),
            // ),
            'posts_per_page' => -1,
        );

        $posts = get_posts( $args );

        foreach ($posts as $post) {
            wp_delete_post( $post->ID );
        }
    }
}
