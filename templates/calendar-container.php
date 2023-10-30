<?php

/** @var MG_Calendar */
$calendar = $calendar;

?>
<div class="choose-date-time" role="radiogroup" aria-labelledby="dates-and-times">
    <div id="timezone-container" title="CHRISTUS MUGUERZA time zone is America/Mexico_City" data-trust-autodetect="true">
        <!-- <span id="timezone-label" class="babel-ignore">(GMT-6:00) Central Time - Mexico City</span> <a href="#" class="change-timezone branded">Modificar</a>
        <span id="timezone-prompt">
            Tu zona horaria
        </span> -->
        <div>
            <select default="America/Monterrey" name="timezone" class="form-control inline-field babel-ignore timezone-select-inactive" id="timezone">
                <optgroup label="México">
                    <option value="America/Monterrey" selected>(GMT-6:00) Monterrey - Eastern Time - Nuevo León</option>
                    <option value="America/Tijuana">(GMT-7:00) Pacific Time - Tijuana</option>
                    <option value="America/Hermosillo">(GMT-7:00) Mountain Time - Hermosillo</option>
                    <option value="America/Mazatlan">(GMT-7:00) Mountain Time - Chihuahua, Mazatlan</option>
                    <option value="America/Mexico_City">(GMT-6:00) Central Time - Mexico City</option>
                    <option value="America/Cancun">(GMT-5:00) Cancun - Eastern Time - Quintana Roo</option>
                </optgroup>
            </select>
            <input id="apex_calendar_id" type="hidden" name="apex_calendar_id" value="<?php echo $calendar->apexCalendarId; ?>">
        </div>
    </div>

    <div id="selected-times-container" style="display: none;">
        <a name="selectedTimes"></a>
        <a href="#" class="btn btn-primary btn-next-step-top btn-next-step" style="display:none">Continuar »</a>
        <div id="selected-times"></div>
        <a href="#" class="btn btn-primary btn-next-step">Continuar »</a>
    </div>

    <div class="choose-date choose-date-times" id="dates-and-times">
        <?php $calendar->renderContent(); ?>
    </div>
    <div class="choose-time-actions" style="display:none">
        <ul>
            <li>
                <a href="#" class="btn btn-primary btn-block btn-next-step" data-testid="continue-button">Continuar »</a>
            </li>
            <li>
                <a href="#selected-times" class="btn btn-default btn-block btn-additional" data-testid="additional-time-button">Seleccionar día adicional</a>
            </li>
            <li>
                <a href="#" class="btn btn-default btn-block btn-recurring" data-testid="recurring-time-button">Se repite</a>
            </li>
        </ul>
    </div>
    <div class="clearfix"></div>
</div>
