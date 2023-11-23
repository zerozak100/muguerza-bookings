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