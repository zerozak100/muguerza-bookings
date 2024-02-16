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
        <label for="name">Nombre(s) <span>*</span></label>
        <input id="name" type="text" name="name" placeholder="Nombre(s)" maxlength="50" value="<?php echo esc_attr( $form->get_field( 'name' ) ); ?>">
    </div>
    <div class="booking-form__field booking-form__field--lastname1 required">
        <label for="lastname1">Apellido paterno <span>*</span></label>
        <input id="lastname1" type="text" name="lastname1" placeholder="Apellido paterno" maxlength="50" value="<?php echo esc_attr( $form->get_field( 'lastname1' ) ); ?>">
    </div>
    <div class="booking-form__field booking-form__field--lastname2 required">
        <label for="lastname2">Apellido materno <span>*</span></label>
        <input id="lastname2" type="text" name="lastname2" placeholder="Apellidos materno" maxlength="50" value="<?php echo esc_attr( $form->get_field( 'lastname2' ) ); ?>">
    </div>
    <div class="booking-form__field booking-form__field--birthdate required">
        <label for="birthdate">Fecha de nacimiento <span>*</span></label>
        <input id="birthdate" type="date" name="birthdate" placeholder="Fecha de nacimiento" maxlength="50" value="<?php echo esc_attr( $form->get_field( 'birthdate' ) ); ?>">
    </div>
    <div class="booking-form__field booking-form__field--sex required">
        <label for="sex">Sexo <span>*</span></label>
        <select id="sex" name="sex" value="<?php echo esc_attr( $form->get_field( 'sex' ) ); ?>">
            <option value="" disabled selected><i>--Seleccionar--</i></option>
            <option value="M" <?php selected( 'M', $form->get_field( 'sex' ) ) ?>>Masculino</option>
            <option value="F" <?php selected( 'F', $form->get_field( 'sex' ) ) ?>>Femenino</option>
            <option value="O" <?php selected( 'O', $form->get_field( 'sex' ) ) ?>>Otro</option>
        </select>
    </div>
    <div class="booking-form__field booking-form__field--email required">
        <label for="email">Correo <span>*</span></label>
        <input id="email" type="text" name="email" placeholder="Correo" maxlength="50" value="<?php echo esc_attr( $form->get_field( 'email' ) ); ?>">
    </div>
    <div class="booking-form__field booking-form__field--phone required">
        <label for="phone">Celular <span>*</span></label>
        <input id="phone" type="number" name="phone" placeholder="Celular" min="0" oninput="checkMaxLength(this, 11)" value="<?php echo esc_attr( $form->get_field( 'phone' ) ); ?>">
    </div>
    <!-- <div class="booking-form__field booking-form__field--age">
        <label for="age">Edad</label>
        <input id="age" type="number" name="age" placeholder="Edad" min="0" max="100" oninput="checkMaxLength(this, 3)" value="<?php echo esc_attr( $form->get_field( 'age' ) ); ?>">
    </div> -->
    <!-- <div class="booking-form__field booking-form__field--birth_state">
        <label for="birth_state">Estado de nacimiento</label>
        <select id="birth_state" name="birth_state" value="<?php echo esc_attr( $form->get_field( 'birth_state' ) ); ?>">
            <option value="" disabled selected>--Seleccionar--</option>
            <?php foreach ( $mexico_states as $state ) : ?>
                <option value="<?php echo esc_attr( $state ); ?>" <?php selected( $state, $form->get_field( 'birth_state' ) ) ?>>
                    <?php echo esc_html( $state ); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div> -->
    <!-- <div class="booking-form__field booking-form__field--curp">
        <label for="curp">CURP</label>
        <input id="curp" type="text" name="curp" placeholder="CURP" value="<?php echo esc_attr( $form->get_field( 'curp' ) ); ?>">
    </div> -->
</div>

<script>
function checkMaxLength(input, maxLength) {
    if (input.value.length > maxLength) {
        input.value = input.value.slice(0, maxLength);
    }

    const phoneEl = document.getElementById('phone');
    // const ageEl = document.getElementById('age');

    const onlyPositiveNumbers = function (event) {
        // Get the input value
        let inputValue = event.target.value;
    
        // Remove any non-digit characters
        inputValue = inputValue.replace(/\D/g, '');
    
        // Ensure the value is a positive number
        if (inputValue !== '' && parseInt(inputValue) >= 0) {
            // Update the input value
            event.target.value = inputValue;
        } else {
            // If not a positive number, clear the input
            event.target.value = '';
        }
    }

    phoneEl.addEventListener('input', onlyPositiveNumbers);
    // ageEl.addEventListener('input', onlyPositiveNumbers);
}
</script>
