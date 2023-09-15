<?php

/**
 * @var \MG_Calendar_Day $day
 */
$day;

?>

<fieldset class="date  activeday">
    <legend class="date-heading">
        <div class="date-head-text">&nbsp;</div>
        <div class="day-of-week babel-ignore"><?php echo $day->getDayOfWeek(); ?></div>
        <div class="date-secondary babel-ignore"><?php echo $day->getSecondaryDate(); ?></div>
    </legend>
    <div class="choose-time" data-testid="choose-time">
        <div class="form-inline">
            <!-- <input type="radio" class="time-selection" name="time[]" data-testid="select-day-11-slot-0" data-readable-date="2023-07-11" value="2023-07-11 07:00" id="appt1689080400" aria-labelledby="lbl_appt1689080400">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689080400" for="appt1689080400" aria-label="07:00" aria-checked="false">07:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-11-slot-1" data-readable-date="2023-07-11" value="2023-07-11 08:00" id="appt1689084000" aria-labelledby="lbl_appt1689084000">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689084000" for="appt1689084000" aria-label="08:00" aria-checked="false">08:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-11-slot-2" data-readable-date="2023-07-11" value="2023-07-11 09:00" id="appt1689087600" aria-labelledby="lbl_appt1689087600">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689087600" for="appt1689087600" aria-label="09:00" aria-checked="false">09:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-11-slot-3" data-readable-date="2023-07-11" value="2023-07-11 10:00" id="appt1689091200" aria-labelledby="lbl_appt1689091200">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689091200" for="appt1689091200" aria-label="10:00" aria-checked="false">10:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-11-slot-4" data-readable-date="2023-07-11" value="2023-07-11 11:00" id="appt1689094800" aria-labelledby="lbl_appt1689094800">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689094800" for="appt1689094800" aria-label="11:00" aria-checked="false">11:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-11-slot-5" data-readable-date="2023-07-11" value="2023-07-11 12:00" id="appt1689098400" aria-labelledby="lbl_appt1689098400">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689098400" for="appt1689098400" aria-label="12:00" aria-checked="false">12:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-11-slot-6" data-readable-date="2023-07-11" value="2023-07-11 13:00" id="appt1689102000" aria-labelledby="lbl_appt1689102000">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689102000" for="appt1689102000" aria-label="13:00" aria-checked="false">13:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-11-slot-7" data-readable-date="2023-07-11" value="2023-07-11 14:00" id="appt1689105600" aria-labelledby="lbl_appt1689105600">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689105600" for="appt1689105600" aria-label="14:00" aria-checked="false">14:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-11-slot-8" data-readable-date="2023-07-11" value="2023-07-11 15:00" id="appt1689109200" aria-labelledby="lbl_appt1689109200">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689109200" for="appt1689109200" aria-label="15:00" aria-checked="false">15:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-11-slot-9" data-readable-date="2023-07-11" value="2023-07-11 16:00" id="appt1689112800" aria-labelledby="lbl_appt1689112800">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689112800" for="appt1689112800" aria-label="16:00" aria-checked="false">16:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-11-slot-10" data-readable-date="2023-07-11" value="2023-07-11 17:00" id="appt1689116400" aria-labelledby="lbl_appt1689116400">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689116400" for="appt1689116400" aria-label="17:00" aria-checked="false">17:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-11-slot-11" data-readable-date="2023-07-11" value="2023-07-11 18:00" id="appt1689120000" aria-labelledby="lbl_appt1689120000">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689120000" for="appt1689120000" aria-label="18:00" aria-checked="false">18:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-11-slot-12" data-readable-date="2023-07-11" value="2023-07-11 19:00" id="appt1689123600" aria-labelledby="lbl_appt1689123600">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689123600" for="appt1689123600" aria-label="19:00" aria-checked="false">19:00</label>
            <br> -->

            <?php foreach ( $day->getAvailableTimes() as $time ): ?>
                <!-- <input type="radio" class="time-selection" name="time[]" data-readable-date="2023-07-11" value="2023-07-11 13:00" id="appt1689102000" aria-labelledby="lbl_appt1689102000">
                <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689102000" for="appt1689102000" aria-label="13:00" aria-checked="false">13:00</label> -->
                <?php $time->renderInput(); ?>
                <?php $time->renderLabel(); ?>
                <br>
            <?php endforeach; ?>
        </div>
        <!-- <a href="#calendar-top" class="more-times-available">
            <i class="fa fa-angle-up"></i><br>Horas anteriores <br> Disponible
        </a> -->
    </div>
</fieldset>