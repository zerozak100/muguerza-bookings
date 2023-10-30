<?php

class MGB_Ajax {
    public function __construct() {
        add_action( 'wp_ajax_mgb_show_calendar', array( $this, 'show_calendar' ) );
        add_action( 'wp_ajax_nopriv_mgb_show_calendar', array( $this, 'show_calendar' ) );
    }

    public function show_calendar() {
        $error = new WP_Error();

        if ( ! isset( $_POST['startDate'] ) || ! $_POST['startDate'] ) {
            $error->add( 'start_date_required', 'Fecha es requerido' );
        }

        if ( ! isset( $_POST['apexCalendarId'] ) || ! $_POST['apexCalendarId'] ) {
            $error->add( 'apex_calendar_id_required', 'Calendar ID es requerido' );
        }

        if ( ! isset( $_POST['timezone'] ) || ! $_POST['timezone'] ) {
            $error->add( 'timezone_required', 'Timezone es requerido' );
        }

        if ( $error->has_errors() ) {
            wp_send_json_error( $error, 414 );
        }

        $calendar = new MG_Calendar( $_POST['startDate'], $_POST['apexCalendarId'], $_POST['timezone'] );

        ob_start();
        $calendar->renderContent();
        $content = ob_get_clean();

        wp_send_json_success( array( 'message' => 'Calendario', 'payload' => $content ) );
    }
}

new MGB_Ajax();
