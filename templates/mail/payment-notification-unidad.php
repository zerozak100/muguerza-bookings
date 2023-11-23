<?php

/**
 * @var WC_Order
 */
$order = $order;

/**
 * @var array
 */
$patient = $patient;

/**
 * @var array
 */
$unidad = $unidad;

/**
 * @var string
 */
$rfc = $rfc;

/**
 * @var string
 */
$product_names = $product_names;

$additional_branch_track = get_post_meta( $order->get_id(), 'additional_branch_track', true );

?>

<div>

    <h4>Datos del pedido</h4>

    <div>Orden: <?php echo $order->get_id(); ?></div>
    <div>Método de pago: <?php echo $order->get_payment_method_title(); ?></div>
    <div>Código de seguimiento: <?php echo $additional_branch_track; ?></div>
    <div>Unidad: <?php echo $unidad['name']; ?></div>
    <!-- <div>Fecha: <?php echo $billing_acuityscheduling_date; ?></div>
    <div>Hora: <?php echo $billing_acuityscheduling_time; ?></div> -->
    <div>Notas del pedido: <?php echo $order->get_customer_note(); ?></div>
    <div>Productos: <?php echo $product_names; ?></div>
    <div>Total: <?php echo $order->get_formatted_order_total(); ?></div>

    <h4>Datos de facturación</h4>

    <div>Nombre: <?php echo $order->get_billing_first_name(); ?></div>
    <div>Apellidos: <?php echo $order->get_billing_last_name(); ?></div>
    <div>Correo: <?php echo $order->get_billing_email(); ?></div>
    <div>Teléfono: <?php echo $order->get_billing_phone(); ?></div>
    <div>RFC: <?php echo $rfc; ?></div>
    <div>Razón social: <?php echo $order->get_billing_company(); ?></div>
    <div>País: <?php echo $order->get_billing_country(); ?></div>
    <div>Estado: <?php echo $order->get_billing_state(); ?></div>
    <div>Ciudad: <?php echo $order->get_billing_city(); ?></div>
    <div>Código Postal: <?php echo $order->get_billing_postcode(); ?></div>
    <div>Domicilio (calle y número): <?php echo $order->get_billing_address_1(); ?></div>
    <div>Domicilio (Apartamento, habitación, etc): <?php echo $order->get_billing_address_2(); ?></div>

    <div></div>
    <h4>Agendas</h4>

    <?php $order_bookings = MG_Bookings::get_bookings_from_order( $order->get_id() ); ?>

    <?php foreach ( $order_bookings as $booking ) : ?>
        <div>Cita ID: <?php echo $booking->get_id(); ?></div>
        <div>Nombre: <?php echo $booking->get_name(); ?></div>
        <div>Apellido paterno: <?php echo $booking->get_lastname1(); ?></div>
        <div>Apellido materno: <?php echo $booking->get_lastname2(); ?></div>
        <div>Fecha y hora: <?php echo $booking->get_datetime(); ?></div>
        <div>Email: <?php echo $booking->get_email(); ?></div>
        <div>Teléfono: <?php echo $booking->get_phone(); ?></div>
        <div>Fecha de nacimiento: <?php echo $booking->get_birthdate(); ?></div>
        <div>Sexo: <?php echo $booking->get_sex(); ?></div>
        <div>Estado de nacimiento: <?php echo $booking->get_birth_state(); ?></div>
        <div>CURP: <?php echo $booking->get_curp(); ?></div>
        <hr>
    <?php endforeach; ?>

    <!-- <h4>Datos del paciente</h4> -->

    <!-- <div>Nombre: <?php echo $patien['name']; ?></div>
    <div>Apellido paterno: <?php echo $patient['last_name']; ?></div>
    <div>Apellido materno: <?php echo $patient['second_last_name']; ?></div>
    <div>Fecha de nacimiento: <?php echo $patient['birthdate']; ?></div>
    <div>Domicilio: <?php echo $patient['address_1']; ?></div>
    <div>Médico tratante: <?php echo $patient['pmd']; ?></div> -->

    <!-- <div>Nombre: <?php echo $patient['fullname']; ?></div>
    <div>Fecha de nacimiento: <?php echo $patient['birthdate']; ?></div>
    <div>Teléfono: <?php echo $patient['phone']; ?></div>
    <div>Correo electrónico: <?php echo $patient['email']; ?></div> -->
</div>