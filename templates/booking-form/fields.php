<?php

/** @var MG_Booking_Form */
$form = $form;

?>

<div class="booking-form">
    <div class="booking-form__field booking-form__field--name required">
        <label for="">Nombre <span>*</span></label>
        <input type="text" name="name" placeholder="Nombre">
    </div>
    <div class="booking-form__field booking-form__field--email required">
        <label for="">Correo <span>*</span></label>
        <input type="text" name="email" placeholder="Correo">
    </div>
    <div class="booking-form__field booking-form__field--lastname1 required">
        <label for="">Apellido paterno <span>*</span></label>
        <input type="text" name="lastname1" placeholder="Apellidos">
    </div>
    <div class="booking-form__field booking-form__field--lastname2 required">
        <label for="">Apellido materno <span>*</span></label>
        <input type="text" name="lastname2" placeholder="Apellidos">
    </div>
    <div class="booking-form__field booking-form__field--phone required">
        <label for="">Celular <span>*</span></label>
        <input type="number" name="phone" placeholder="Celular">
    </div>
    <div class="booking-form__field booking-form__field--birthdate required">
        <label for="">Fecha de nacimiento <span>*</span></label>
        <input type="date" name="birthdate" placeholder="Fecha de nacimiento">
    </div>
    <div class="booking-form__field booking-form__field--sex">
        <label for="">Sexo</label>
        <input type="text" name="sex" placeholder="Sexo">
    </div>
    <div class="booking-form__field booking-form__field--age">
        <label for="">Edad</label>
        <input type="number" name="age" placeholder="Edad">
    </div>
    <div class="booking-form__field booking-form__field--birth_state">
        <label for="">Estado de nacimiento</label>
        <input type="text" name="birth_state" placeholder="Estado de nacimiento">
    </div>
    <div class="booking-form__field booking-form__field--curp">
        <label for="">CURP</label>
        <input type="text" name="curp" placeholder="CURP">
    </div>
</div>
