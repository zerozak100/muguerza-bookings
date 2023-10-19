<?php

/**
 * Plugin Name: Muguerza Bookings
 * Description: Muguerza Bookings.
 * Version: 1.0.1
 * Author: Acsyt
 * Author URI: http://acsyt.com
 * Developer: Acsyt
 * Text Domain: acsyt
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * 
 */

if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
    require __DIR__ . '/vendor/autoload.php';
}

define( 'MGB_PLUGIN_PATH', __DIR__ );
define( 'MGB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MGB_TEMPLATES_PATH', MGB_PLUGIN_PATH . '/templates//' );

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

function mgc_wc_admin_mail_order_to_unidad() {
    
}
/**
 * TODO: Cambiar todo el plugin de Muguerza_Core
 */
class Mugerza_Bookings {

    public function __construct() {
        include_once "inc/class-mg-product.php";
        include_once "inc/class-mg-order.php";
        include_once "inc/class-mg-calendar.php";
        include_once "inc/ajax.php";
        include_once "inc/class-mg-booking-form.php";
        include_once "inc/class-mg-booking-session.php";
        include_once "inc/class-mg-bookable-order-item.php";
        include_once "inc/class-mg-booking-item.php";
        include_once "inc/class-mg-booking-item-session.php";
        include_once "inc/class-mg-booking-item-order-item.php";

        include_once __DIR__ . '/inc/class-mg-api-response.php';
        include_once __DIR__ . '/inc/class-mg-api.php';
        include_once __DIR__ . '/inc/class-mg-api-apex.php';

        include_once __DIR__ . '/inc/admin/class-mg-admin-page-importer.php';

        mgb_booking_form();

        // $calendar = new MG_Calendar( date( 'Y-m-d' ) );

        // add_action( 'wp_enqueue_scripts', array( $calendar, 'scripts'  ) );
        // add_action( 'wp_footer', array( $calendar, 'display' ) );


		// add_action( 'wp_loaded', array( $this, 'handle_booking_config_save' ), 20 );

        add_action( 'woocommerce_before_single_product', array( $this, 'init' ) );
        add_action( 'wp_footer', array( $this, 'footer' ) );
    }

    public function init() {
        
    }

    public function footer() {
        // $api = MG_Api_Apex::instance();
        // $time_list = $api->get_available_time_list();
        // dd( $time_list );
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
    $category_id = false;

    foreach ( $order->get_items() as $item ) {
        if ( $item instanceof WC_Order_Item_Product ) {
            $product = $item->get_product();
            $category_id = $product->get_category_ids()[0];
            break;
        }
    }

    if ( $category_id ) {
        return $accounts[ $category_id ];
    }

    return false;
}

function mg_get_bank_accounts() {
    $accounts = array();
    $unidades = get_posts( array( 'post_type' => 'unidad', 'posts_per_page' => -1, 'fields' => 'ids' ) );
    
    foreach ( $unidades as $unidad_id ) {
        $catid = get_field( 'ubicacion', $unidad_id );
        $data = array(
          'name'                 => get_the_title( $unidad_id ),
          'catid'                => $catid,
          'track'                => get_field( 'conekta_track', $unidad_id ),
          'email'                => get_field( 'conekta_email', $unidad_id ),
          'debug'                => get_field( 'conekta_debug', $unidad_id ),
          'test_api_key'         => get_field( 'conekta_test_api_key', $unidad_id ),
          'test_publishable_key' => get_field( 'conekta_test_publishable_key', $unidad_id ),
          'live_api_key'         => get_field( 'conekta_live_api_key', $unidad_id ),
          'live_publishable_key' => get_field( 'conekta_live_publishable_key', $unidad_id ),
        );
        $accounts[ $catid ] = $data;
      }

      return $accounts;
}

function mg_format_additional_branch_track( $bank_account, $wc_order_id, $conekta_order_id ) {
    return sprintf( '%1$s - %2$s - %3$s', $bank_account['track'], $wc_order_id, $conekta_order_id );
}
