<?php

class MG_Admin_Page_Importer {
    const IMPORTERS = array(
        'MG_Importer_Servicios' => 'Servicios',
    );

    public function __construct() {
        include_once __DIR__ . '/abstract-class-mg-importer.php';
        include_once __DIR__ . '/class-mg-import-item-servicio.php';
        include_once __DIR__ . '/class-mg-importer-servicios.php';
        
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_init', array( $this, 'do_action' ) );
        // add_action( 'admin_init', array( $this, '' ) );
    }

    public function admin_menu() {
        add_menu_page( 'Importador', 'Importador', 'manage_options', self::class, array( $this, 'render_page' ) );

        foreach ( self::IMPORTERS as $importer_class => $label ) {
            if ( class_exists( $importer_class ) ) {
                add_submenu_page( self::class, $label, $label, 'manage_options', $importer_class, array( $importer_class, 'render_page' ) );
            }
        }
    }

    public function do_action() {
        $importer_type   = sanitize_text_field( $_POST['importer_type'] );
        $importer_action = sanitize_text_field( $_POST['importer_action'] );

        try {
            if ( $importer_type && class_exists( $importer_type ) ) {
                /**
                 * @var MG_Importer
                 */
                $importer = new $importer_type;
                $action = array( $importer, $importer_action );

                if ( is_callable( $action ) ) {
                    call_user_func( $action, $this->get_data() );
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function get_data(): array {
        $data = file_get_contents( $_FILES['importer_data']['tmp_name'] );
        return json_decode( $data, true );
    }

    public function render_page() {
        mgb_get_template( 'admin/importer/importer.php', array( 'menus' => self::IMPORTERS ) );
    }
}

new MG_Admin_Page_Importer;
