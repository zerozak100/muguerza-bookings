<?php

/**
 * Plugin Name: Muguerza Bookings
 * Description: Muguerza Bookings.
 * Version: 1.0.17 
 * Author: Acsyt
 * Author URI: http://acsyt.com
 * Developer: Acsyt
 * Text Domain: acsyt
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * 
 */

use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
    require __DIR__ . '/vendor/autoload.php';
}

define( 'MGB_PLUGIN_PATH', __DIR__ );
define( 'MGB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MGB_TEMPLATES_PATH', MGB_PLUGIN_PATH . '/templates//' );
define( 'MG_LOGS_PATH', __DIR__ . '/logs//' );

function mgb_asset_url_css( $asset ) {
    return MGB_PLUGIN_URL . '/assets/css/' . $asset;
}

function mgb_asset_url_js( $asset ) {
    return MGB_PLUGIN_URL . '/assets/js/' . $asset;
}

function mgb_booking_form() {
    return MG_Booking_Form::getInstance();
}

function mgb_get_template( $template_name, $args = array() ) {
    wc_get_template( $template_name, $args, '', MGB_TEMPLATES_PATH );
}

function mgb_user_has_membresia( $user_id = null ) {
    return true;

    if ( $user_id === null && ! is_user_logged_in() ) {
        return false;
    }

    $user_id = get_current_user_id();

    $mg_membresia_data = get_user_meta( $user_id, 'mg_membresia_data', true );

    return boolval( $mg_membresia_data );
}

function mgb_membresia_price_icon_html ( $product_id, $return = false ) {
    if ( mgb_user_has_membresia() ) {
        $price_membresia = get_post_meta( $product_id, '_price_membresia', true );
        if ( $price_membresia ) {
            $html = '<div class="membresia-price-label">Precio de membresía</div>';
            if ( $return ) {
                return $html;
            } else {
                echo $html;
            }
        }
    }
}

/**
 * TODO: Cambiar todo el plugin de Muguerza_Core
 */
class Mugerza_Bookings {

    public function __construct() {
        include_once __DIR__ . "/inc/class-mg-product.php";
        include_once __DIR__ . "/inc/class-mg-order.php";
        include_once __DIR__ . "/inc/class-mg-calendar.php";
        include_once __DIR__ . "/inc/ajax.php";
        include_once __DIR__ . "/inc/class-mg-booking-form.php";
        include_once __DIR__ . "/inc/class-mg-booking-session.php";
        include_once __DIR__ . '/inc/class-mg-frontend-scripts.php';
        include_once __DIR__ . '/inc/class-mg-booking.php';
        include_once __DIR__ . '/inc/class-mg-bookings.php';
        include_once __DIR__ . '/inc/data-stores/class-mg-booking-data-store-cpt.php';

        include_once __DIR__ . '/inc/api/class-mg-api-response.php';
        include_once __DIR__ . '/inc/api/class-mg-api-request.php';
        include_once __DIR__ . '/inc/api/class-mg-api.php';
        include_once __DIR__ . '/inc/api/class-mg-apex-appt-item.php';
        include_once __DIR__ . '/inc/api/class-mg-api-apex.php';
        include_once __DIR__ . '/inc/api/class-mg-api-membresia.php';

        include_once __DIR__ . '/inc/admin/class-mg-admin-page-importer.php';

        mgb_booking_form();

        add_filter( 'woocommerce_product_get_sale_price', array( $this, 'membresia_sale_price' ), 10, 2 );
        add_filter( 'woocommerce_product_get_regular_price', array( $this, 'membresia_regular_price' ), 10, 2 );
        add_filter( 'woocommerce_product_get_price', array( $this, 'membresia_price' ), 10, 2 );
        
        add_filter( 'woocommerce_sale_flash', array( $this, 'membresia_product_sale_flash' ), 99, 3 );

        add_action( 'wp_login', array( $this, 'save_membresia_on_login' ), 10, 2 );

        add_filter( 'woocommerce_data_stores', array( $this, 'woocommerce_data_stores' ) );

        // CPT booking table
        add_filter( 'manage_edit-booking_columns', array( $this, 'booking_custom_columns' ), 20 );
        add_action( 'manage_booking_posts_custom_column' , array( $this, 'booking_custom_columns_content' ), 20, 2 );

        add_filter( 'manage_edit-booking_columns', array( $this, 'remove_yoast_seo_cols' ) );
        add_filter( 'manage_edit-unidad_columns', array( $this, 'remove_yoast_seo_cols' ) );
        add_filter( 'manage_edit-medico_columns', array( $this, 'remove_yoast_seo_cols' ) );

        add_filter( 'woocommerce_product_related_posts_query', array( $this, 'muguerza_product_related_posts_by_unidad_query' ), 10, 3 );

        add_action( 'template_redirect', array( $this, 'set_default_unidad' ) );
    }

    public function set_default_unidad() {
        $user = MG_User::current();
        $unidad = $user->get_unidad();

        if ( ! $unidad->mg_unidad ) {
            $default_unidad = 53120233092; // CHRISTUS MUGUERZA Hospital Alta Especialidad
            $user->save_unidad( $default_unidad );
        }
    }

    /**
     * Siempre debe ser de membresía
     * 
     * @param string $price Price
     * @param WC_Product $product Product
     */
    public function membresia_sale_price( $price, $product ) {
        if ( ! mgb_user_has_membresia() ) {
            return $price;
        }

        $price_membresia = get_post_meta( $product->get_id(), '_price_membresia', true );

        if ( ! $price_membresia ) {
            return $price;
        }

        return $price_membresia;
    }

    /**
     * Menor de los precios sin contar membresía
     * 
     * @param string $price Price
     * @param WC_Product $product Product
     */
    public function membresia_regular_price( $price, $product ) {
        if ( ! mgb_user_has_membresia() ) {
            return $price;
        }

        $price_membresia = get_post_meta( $product->get_id(), '_price_membresia', true );

        if ( ! $price_membresia ) {
            return $price;
        }

        $_sale_price = get_post_meta( $product->get_id(), '_sale_price', true );

        if ( ! $_sale_price ) {
            return $price;
        }

        $sale_price      = +$_sale_price;
        $regular_price   = +$price;
        $membresia_price = +$price_membresia;

        if ( $membresia_price >= $sale_price && $membresia_price >= $regular_price ) {
            return $membresia_price;
        }

        if ( $membresia_price < $sale_price ) {
            return $sale_price;
        }

        if ( $membresia_price < $regular_price ) {
            return $regular_price;
        }

        return $price;
    }

    /**
     * @param string $price Price
     * @param WC_Product $product Product
     */
    public function membresia_price( $price, $product ) {
        if ( ! mgb_user_has_membresia() ) {
            return $price;
        }

        $price_membresia = get_post_meta( $product->get_id(), '_price_membresia', true );

        if ( $price_membresia ) {
            return $price_membresia;
        }

        return $price;
    }

    public function membresia_product_sale_flash( $html, $post, $product ) {
        if ( ! mgb_user_has_membresia() ) {
            return $html;
        }

        $price_membresia = get_post_meta( $product->get_id(), '_price_membresia', true );

        if ( $price_membresia ) {
            return '<span class="onsale onsale--membresia">' . esc_html__( 'Membresía', 'woocommerce' ) . '</span>';
        }

        return $html;
    }

    public function muguerza_product_related_posts_by_unidad_query( $query,  $product_id, $args ) {
        // $cats_array = apply_filters( 'woocommerce_product_related_posts_relate_by_category', true, $product_id ) ? apply_filters( 'woocommerce_get_related_product_cat_terms', wc_get_product_term_ids( $product_id, 'product_cat' ), $product_id ) : array();
		// $tags_array = apply_filters( 'woocommerce_product_related_posts_relate_by_tag', true, $product_id ) ? apply_filters( 'woocommerce_get_related_product_tag_terms', wc_get_product_term_ids( $product_id, 'product_tag' ), $product_id ) : array();
		global $wpdb;

        $limit = 10;

        $unidades_array       = wc_get_product_term_ids( $product_id, 'mg_unidad' );
        $tipo_servicios_array = wc_get_product_term_ids( $product_id, 'tipos_servicios' );
        $producto_tipo_array  = wc_get_product_term_ids( $product_id, 'producto_tipo' );

        // $include_term_ids   = array_merge( $unidades_array, $tipo_servicios_array, $producto_tipo_array );

        $include_unidades     = implode( ',', array_map( 'absint', $unidades_array ) );
        $include_producto_tipo = implode( ',', array_map( 'absint', $producto_tipo_array ) );
        $include_tipo_servicios = implode( ',', array_map( 'absint', $tipo_servicios_array ) );

        $where_conditions = array();

        if ( ! empty( $include_unidades ) ) {
            $where_conditions[] = "tt_unidades.term_id IN ({$include_unidades})";
        }
    
        if ( ! empty( $include_producto_tipo ) ) {
            $where_conditions[] = "tt_producto_tipo.term_id IN ({$include_producto_tipo})";
        }
    
        if ( ! empty( $include_tipo_servicios ) ) {
            $where_conditions[] = "tt_tipo_servicios.term_id IN ({$include_tipo_servicios})";
        }

        $where_clause = ! empty( $where_conditions ) ? 'AND ' . implode( ' AND ', $where_conditions ) : '';

        $query = array(
			'fields' => "
				SELECT DISTINCT ID FROM {$wpdb->posts} p
			",
			'join'   => "
                INNER JOIN {$wpdb->term_relationships} tr_unidades ON p.ID = tr_unidades.object_id
                INNER JOIN {$wpdb->term_taxonomy} tt_unidades ON tr_unidades.term_taxonomy_id = tt_unidades.term_taxonomy_id
                INNER JOIN {$wpdb->term_relationships} tr_producto_tipo ON p.ID = tr_producto_tipo.object_id
                INNER JOIN {$wpdb->term_taxonomy} tt_producto_tipo ON tr_producto_tipo.term_taxonomy_id = tt_producto_tipo.term_taxonomy_id
                INNER JOIN {$wpdb->term_relationships} tr_tipo_servicios ON p.ID = tr_tipo_servicios.object_id
                INNER JOIN {$wpdb->term_taxonomy} tt_tipo_servicios ON tr_tipo_servicios.term_taxonomy_id = tt_tipo_servicios.term_taxonomy_id
            ",
			'where'  => "
				WHERE 1=1
				AND p.post_status = 'publish'
				AND p.post_type = 'product'
                {$where_clause}
			",
			'limits' => '
				LIMIT ' . absint( $limit ) . '
			',
		);

        // $query['join'] .= " INNER JOIN ( SELECT object_id FROM {$wpdb->term_relationships} INNER JOIN {$wpdb->term_taxonomy} using( term_taxonomy_id ) WHERE term_id IN ( " . implode( ',', array_map( 'absint', $include_term_ids ) ) . ' ) ) AS include_join ON include_join.object_id = p.ID';

        return $query;
    }

    public function remove_yoast_seo_cols( $columns ) {
        unset( $columns['wpseo-score'] );
        unset( $columns['wpseo-score-readability'] );
        unset( $columns['wpseo-title'] );
        unset( $columns['wpseo-metadesc'] );
        unset( $columns['wpseo-focuskw'] );
        unset( $columns['wpseo-links'] );
        unset( $columns['wpseo-linked'] );

        return $columns;
    }

    public function booking_custom_columns( $columns ) {
        $columns['apex_status'] = 'APEX estatus';
        $columns['apex_appointment_id'] = 'APEX ID';
        $columns['order_id'] = 'Order ID';

        return $columns;
    }

    public function booking_custom_columns_content( $column, $post_id ) {
        if ( 'apex_status' === $column ) {
            $booking = new MG_Booking( $post_id );
            echo $booking->get_apex_status();
        }

        if ( 'order_id' === $column ) {
            $booking = new MG_Booking( $post_id );
            echo $booking->get_order_id();
        }

        if ( 'apex_appointment_id' === $column ) {
            $booking = new MG_Booking( $post_id );
            echo $booking->get_apex_appointment_id();
        }
    }

    /**
     * @param string $user_login User login
     * @param WP_User $user Usuario
     */
    public function save_membresia_on_login( $user_login, $user ) {
        $api = MG_API_Membresia::instance();
        $membresia_data = $api->consultar_membresia( $user->user_email );

        update_user_meta( $user->ID, 'mg_membresia_data', $membresia_data ?: '' );
    }

    public function woocommerce_data_stores( $data_stores ) {
        $data_stores['booking'] = 'MG_Booking_Data_Store_CPT';
        return $data_stores;
    }
}

add_action( 'woocommerce_loaded', 'mgb_woocommerce_loaded' );
function mgb_woocommerce_loaded() {
    new Mugerza_Bookings();
}

/**
 * @param WC_Order $order
 */
function mg_send_mail_wc_payment_notification_to_unidad( WC_Order $order, $accounts ) {

    $account = mg_get_bank_account_from_order( $order, $accounts );

    if ( ! $account ) {
        $error = 'No se encontro afiliación para esta orden.';
    }

    if ( ! $account['email'] ) {
        $error = 'Afiliación sin correo para notificar.';
    }

    if ( $error ) {
        return $order->add_order_note( $error );
    }

    add_filter( 'wp_mail_content_type', fn() => 'text/html' );

    $subject = sprintf( 'Pago por %1$s en %2$s - Estado del pago: Pago recibido', $order->get_payment_method_title(), $account['name'] );
    $message = mg_get_template_wc_payment_notification_to_unidad( $order, $account );

    $headers = array();
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: TiendaChristus <ventas@tiendachristus.com>';

    wp_mail( $account['email'], $subject, $message, $headers );
}

function mg_get_template_wc_payment_notification_to_unidad( WC_Order $order, array $unidad ) {
    $order_id = $order->get_id();

    $patient = array(
        // 'first_name'        => get_post_meta( $order_id, 'additional_px_first_name', true ),
        // 'last_name'        => get_post_meta( $order_id, 'additional_px_last_name', true ),
        // 'second_last_name' => get_post_meta( $order_id, 'additional_px_second_last_name', true ),
        // 'brithdate'        => get_post_meta( $order_id, 'additional_px_birthdate', true ),
        // 'address_1'        => get_post_meta( $order_id, 'additional_px_address_1', true ),
        // 'pmd'              => get_post_meta( $order_id, 'additional_px_pmd', true ),

        'fullname'         => get_post_meta( $order_id, 'additional_px_fullname', true ),
        'birthdate'        => get_post_meta( $order_id, 'additional_px_birthdate', true ),
        'phone'            => get_post_meta( $order_id, 'additional_px_phone', true ),
        'email'            => get_post_meta( $order_id, 'additional_px_email', true ),
    );

    $product_names = array();

    foreach ( $order->get_items() as $item ) {
        $product_names[] = $item['name'];
    }

    $data = array(
        'order'   => $order,
        'patient' => $patient,
        'unidad'  => $unidad,
        'rfc'     => get_post_meta( $order_id, 'billing_rfc', true ),
        'product_names' => implode( ', ', $product_names ),
    );

    ob_start();
    mgb_get_template( 'mail/payment-notification-unidad.php', $data );
    return ob_get_clean();
}

/**
 * Determines which bank account to use
 * 
 * @param WC_Order $order
 * @param array $accounts
 * 
 * @return bool|array
 */
function mg_get_bank_account_from_order( WC_Order $order, array $accounts ) {
    $mg_unidad_id = 0;

    foreach ( $order->get_items() as $item ) {
        if ( $item instanceof WC_Order_Item_Product ) {
            $product = new MG_Product( $item->get_product() );
            $mg_unidad_id = $product->get_mg_unidad_id();
            break; // un pedido solo puede tener productos de la misma unidad
        }
    }

    if ( $mg_unidad_id ) {
        return $accounts[ $mg_unidad_id ];
    }

    return false;
}

function mg_get_bank_accounts() {
    $accounts = array();
    $unidades = get_posts( array( 'post_type' => 'unidad', 'posts_per_page' => -1, 'fields' => 'ids' ) );
    
    foreach ( $unidades as $unidad_id ) {
        $data = array(
          'name'                 => get_the_title( $unidad_id ),
          'track'                => get_field( 'conekta_track', $unidad_id ),
          'email'                => get_field( 'conekta_email', $unidad_id ),
          'debug'                => get_field( 'conekta_debug', $unidad_id ),
          'test_api_key'         => get_field( 'conekta_test_api_key', $unidad_id ),
          'test_publishable_key' => get_field( 'conekta_test_publishable_key', $unidad_id ),
          'live_api_key'         => get_field( 'conekta_live_api_key', $unidad_id ),
          'live_publishable_key' => get_field( 'conekta_live_publishable_key', $unidad_id ),
        );
        
        $unidad = new MG_Unidad( $unidad_id );

        $accounts[ $unidad->mg_unidad->term_id ] = $data;
      }

      return $accounts;
}

function mg_format_additional_branch_track( $bank_account, $wc_order_id, $conekta_order_id ) {
    return sprintf( '%1$s - %2$s - %3$s', $bank_account['track'], $wc_order_id, $conekta_order_id );
}

function mg_product_in_unidad( $product_id, $unidad_id = null ) {
	if ( ! $unidad_id ) {
		$user        = MG_User::current();
      	$user_unidad = $user->get_unidad();
	} else {
		$user_unidad = new MG_Unidad( $unidad_id );
	}

	$product        = new MG_Product( $product_id );
    $product_unidad = $product->get_unidad();

	return $user_unidad->get_id() === $product_unidad->get_id();
}

function mg_redirect_with_error( string $url, string $error_message ) {
    $logger = new Logger( __FUNCTION__ );
    $logger->pushHandler( new StreamHandler( MG_LOGS_PATH . 'debug.log') );
    $logger->pushHandler( new BrowserConsoleHandler() );
    $logger->info( $error_message );

    wc_clear_notices();
    wc_add_notice( $error_message, 'error' );
    wp_safe_redirect( $url );
    exit;
}

function mlog( $log ) {
    $request_logger  = new Logger( "mlog" );
    $request_logger->pushHandler( new StreamHandler( MG_LOGS_PATH . 'debug.log') );
    $request_logger->pushHandler( new BrowserConsoleHandler() );

    $request_logger->info( $log );
}
