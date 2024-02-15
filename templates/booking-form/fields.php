<?php

/** @var MG_Booking_Form */
$form = $form;

$mexico_states = array(
    'Aguascalientes',
    'Baja California',
    'Baja California Sur',
    'Campeche',
    'Chiapas',
    'Chihuahua',
    'Coahuila',
    'Colima',
    'Durango',
    'Guanajuato',
    'Guerrero',
    'Hidalgo',
    'Jalisco',
    'Estado de México',
    'Michoacán',
    'Morelos',
    'Nayarit',
    'Nuevo León',
    'Oaxaca',
    'Puebla',
    'Querétaro',
    'Quintana Roo',
    'San Luis Potosí',
    'Sinaloa',
    'Sonora',
    'Tabasco',
    'Tamaulipas',
    'Tlaxcala',
    'Veracruz',
    'Yucatán',
    'Zacatecas'
);

?>

<div class="booking-form">
    <div class="booking-form__field booking-form__field--name required">
        <label for="">Nombre <span>*</span></label>
        <input type="text" name="name" placeholder="Nombre" maxlength="50" value="<?php echo esc_attr( $form->get_field( 'name' ) ); ?>">
    </div>
    <div class="booking-form__field booking-form__field--email required">
        <label for="">Correo <span>*</span></label>
        <input type="text" name="email" placeholder="Correo" maxlength="50" value="<?php echo esc_attr( $form->get_field( 'email' ) ); ?>">
    </div>
    <div class="booking-form__field booking-form__field--lastname1 required">
        <label for="">Apellido paterno <span>*</span></label>
        <input type="text" name="lastname1" placeholder="Apellido paterno" maxlength="50" value="<?php echo esc_attr( $form->get_field( 'lastname1' ) ); ?>">
    </div>
    <div class="booking-form__field booking-form__field--lastname2 required">
        <label for="">Apellido materno <span>*</span></label>
        <input type="text" name="lastname2" placeholder="Apellidos materno" maxlength="50" value="<?php echo esc_attr( $form->get_field( 'lastname2' ) ); ?>">
    </div>
    <div class="booking-form__field booking-form__field--phone required">
        <label for="">Celular <span>*</span></label>
        <input type="number" name="phone" placeholder="Celular" oninput="checkMaxLength(this, 11)" value="<?php echo esc_attr( $form->get_field( 'phone' ) ); ?>">
    </div>
    <div class="booking-form__field booking-form__field--birthdate required">
        <label for="">Fecha de nacimiento <span>*</span></label>
        <input type="date" name="birthdate" placeholder="Fecha de nacimiento" maxlength="50" value="<?php echo esc_attr( $form->get_field( 'birthdate' ) ); ?>">
    </div>
    <div class="booking-form__field booking-form__field--sex">
        <label for="">Sexo</label>
        <!-- <input type="text" name="sex" placeholder="Sexo" maxlength="50"> -->
        <select name="sex" value="<?php echo esc_attr( $form->get_field( 'sex' ) ); ?>">
            <option value="" disabled selected><i>--Seleccionar--</i></option>
            <option value="M" <?php selected( 'M', $form->get_field( 'sex' ) ) ?>>Masculino</option>
            <option value="F" <?php selected( 'F', $form->get_field( 'sex' ) ) ?>>Femenino</option>
        </select>
    </div>
    <div class="booking-form__field booking-form__field--age">
        <label for="">Edad</label>
        <input type="number" name="age" placeholder="Edad" max="100" oninput="checkMaxLength(this, 3)" value="<?php echo esc_attr( $form->get_field( 'age' ) ); ?>">
    </div>
    <div class="booking-form__field booking-form__field--birth_state">
        <label for="">Estado de nacimiento</label>
        <!-- <input type="text" name="birth_state" placeholder="Estado de nacimiento"> -->
        <select name="birth_state" value="<?php echo esc_attr( $form->get_field( 'birth_state' ) ); ?>">
            <option value="" disabled selected>--Seleccionar--</option>
            <?php foreach ( $mexico_states as $state ) : ?>
                <option value="<?php echo esc_attr( $state ); ?>" <?php selected( $state, $form->get_field( 'birth_state' ) ) ?>>
                    <?php echo esc_html( $state ); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="booking-form__field booking-form__field--curp">
        <label for="">CURP</label>
        <input type="text" name="curp" placeholder="CURP" value="<?php echo esc_attr( $form->get_field( 'curp' ) ); ?>">
    </div>
</div>

<script>
function checkMaxLength(input, maxLength) {
    if (input.value.length > maxLength) {
        input.value = input.value.slice(0, maxLength);
    }
}
</script>
