<?php

/** @var MG_Calendar */
$calendar = $calendar;

?>

<div class="calendar-prev-next" id="calendar-prev-next">
    <!-- <a name="calendar-top"></a> -->
    <?php if ( $calendar->prevStartDate ): ?>
        <a class="calendar-previous" data-date="<?php echo $calendar->prevStartDate; ?>">
            Anterior
        </a>
    <?php endif; ?>
    <?php if ( $calendar->nextStartDate ): ?>
        <a class="calendar-next" data-date="<?php echo $calendar->nextStartDate; ?>">
            Más horas
        </a>
    <?php endif; ?>
</div>

<div class="clearfix calendar"></div>

<div class="calendar-days">
    <?php foreach ( $calendar->getDays() as $day ): ?>
        <?php $day->render(); ?>
    <?php endforeach; ?>
</div>

<!-- <div class="calendar-prev-next" id="calendar-prev-next">
    <a name="calendar-top"></a>

    <a href="javascript:self.showCalendar('2023-07-12', %7B%22nextprev%22%3A%7B%222023-07-12%22%3A%222023-07-06%22%2C%222023-07-17%22%3A%222023-07-12%22%7D%7D)" class="calendar-next"><span>Más horas</span> <i class="fa fa-chevron-right"></i></a>
</div>
<div class="clearfix calendar"></div>
<fieldset class="date  activeday">
    <legend class="date-heading">
        <div class="date-head-text">mañana</div>
        <div class="day-of-week babel-ignore">viernes</div>
        <div class="date-secondary babel-ignore"> 7 julio</div>
    </legend>
    <div class="choose-time" data-testid="choose-time">
        <div class="form-inline"><input type="radio" class="time-selection" name="time[]" data-testid="select-day-07-slot-0" data-readable-date="2023-07-07" value="2023-07-07 13:00" id="appt1688756400" aria-labelledby="lbl_appt1688756400">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688756400" for="appt1688756400" aria-label="13:00" aria-checked="false">13:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-07-slot-1" data-readable-date="2023-07-07" value="2023-07-07 14:00" id="appt1688760000" aria-labelledby="lbl_appt1688760000">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688760000" for="appt1688760000" aria-label="14:00" aria-checked="false">14:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-07-slot-2" data-readable-date="2023-07-07" value="2023-07-07 15:00" id="appt1688763600" aria-labelledby="lbl_appt1688763600">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688763600" for="appt1688763600" aria-label="15:00" aria-checked="false">15:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-07-slot-3" data-readable-date="2023-07-07" value="2023-07-07 16:00" id="appt1688767200" aria-labelledby="lbl_appt1688767200">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688767200" for="appt1688767200" aria-label="16:00" aria-checked="false">16:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-07-slot-4" data-readable-date="2023-07-07" value="2023-07-07 17:00" id="appt1688770800" aria-labelledby="lbl_appt1688770800">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688770800" for="appt1688770800" aria-label="17:00" aria-checked="false">17:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-07-slot-5" data-readable-date="2023-07-07" value="2023-07-07 18:00" id="appt1688774400" aria-labelledby="lbl_appt1688774400">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688774400" for="appt1688774400" aria-label="18:00" aria-checked="false">18:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-07-slot-6" data-readable-date="2023-07-07" value="2023-07-07 19:00" id="appt1688778000" aria-labelledby="lbl_appt1688778000">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688778000" for="appt1688778000" aria-label="19:00" aria-checked="false">19:00</label>
            <br>

        </div>
        <a href="#calendar-top" class="more-times-available">
            <i class="fa fa-angle-up"></i><br>Horas anteriores <br> Disponible
        </a>
    </div>
</fieldset>
<fieldset class="date  activeday">
    <legend class="date-heading">
        <div class="date-head-text">&nbsp;</div>
        <div class="day-of-week babel-ignore">sábado</div>
        <div class="date-secondary babel-ignore"> 8 julio</div>
    </legend>
    <div class="choose-time" data-testid="choose-time">
        <div class="form-inline"><input type="radio" class="time-selection" name="time[]" data-testid="select-day-08-slot-0" data-readable-date="2023-07-08" value="2023-07-08 07:00" id="appt1688821200" aria-labelledby="lbl_appt1688821200">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688821200" for="appt1688821200" aria-label="07:00" aria-checked="false">07:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-08-slot-1" data-readable-date="2023-07-08" value="2023-07-08 08:00" id="appt1688824800" aria-labelledby="lbl_appt1688824800">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688824800" for="appt1688824800" aria-label="08:00" aria-checked="false">08:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-08-slot-2" data-readable-date="2023-07-08" value="2023-07-08 09:00" id="appt1688828400" aria-labelledby="lbl_appt1688828400">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688828400" for="appt1688828400" aria-label="09:00" aria-checked="false">09:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-08-slot-3" data-readable-date="2023-07-08" value="2023-07-08 10:00" id="appt1688832000" aria-labelledby="lbl_appt1688832000">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688832000" for="appt1688832000" aria-label="10:00" aria-checked="false">10:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-08-slot-4" data-readable-date="2023-07-08" value="2023-07-08 11:00" id="appt1688835600" aria-labelledby="lbl_appt1688835600">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688835600" for="appt1688835600" aria-label="11:00" aria-checked="false">11:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-08-slot-5" data-readable-date="2023-07-08" value="2023-07-08 12:00" id="appt1688839200" aria-labelledby="lbl_appt1688839200">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688839200" for="appt1688839200" aria-label="12:00" aria-checked="false">12:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-08-slot-6" data-readable-date="2023-07-08" value="2023-07-08 13:00" id="appt1688842800" aria-labelledby="lbl_appt1688842800">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688842800" for="appt1688842800" aria-label="13:00" aria-checked="false">13:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-08-slot-7" data-readable-date="2023-07-08" value="2023-07-08 14:00" id="appt1688846400" aria-labelledby="lbl_appt1688846400">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688846400" for="appt1688846400" aria-label="14:00" aria-checked="false">14:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-08-slot-8" data-readable-date="2023-07-08" value="2023-07-08 15:00" id="appt1688850000" aria-labelledby="lbl_appt1688850000">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688850000" for="appt1688850000" aria-label="15:00" aria-checked="false">15:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-08-slot-9" data-readable-date="2023-07-08" value="2023-07-08 16:00" id="appt1688853600" aria-labelledby="lbl_appt1688853600">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688853600" for="appt1688853600" aria-label="16:00" aria-checked="false">16:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-08-slot-10" data-readable-date="2023-07-08" value="2023-07-08 17:00" id="appt1688857200" aria-labelledby="lbl_appt1688857200">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688857200" for="appt1688857200" aria-label="17:00" aria-checked="false">17:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-08-slot-11" data-readable-date="2023-07-08" value="2023-07-08 18:00" id="appt1688860800" aria-labelledby="lbl_appt1688860800">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688860800" for="appt1688860800" aria-label="18:00" aria-checked="false">18:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-08-slot-12" data-readable-date="2023-07-08" value="2023-07-08 19:00" id="appt1688864400" aria-labelledby="lbl_appt1688864400">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688864400" for="appt1688864400" aria-label="19:00" aria-checked="false">19:00</label>
            <br>

        </div>
        <a href="#calendar-top" class="more-times-available">
            <i class="fa fa-angle-up"></i><br>Horas anteriores <br> Disponible
        </a>
    </div>
</fieldset>
<fieldset class="date  activeday">
    <legend class="date-heading">
        <div class="date-head-text">La próxima semana</div>
        <div class="day-of-week babel-ignore">domingo</div>
        <div class="date-secondary babel-ignore"> 9 julio</div>
    </legend>
    <div class="choose-time" data-testid="choose-time">
        <div class="form-inline"><input type="radio" class="time-selection" name="time[]" data-testid="select-day-09-slot-0" data-readable-date="2023-07-09" value="2023-07-09 07:00" id="appt1688907600" aria-labelledby="lbl_appt1688907600">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688907600" for="appt1688907600" aria-label="07:00" aria-checked="false">07:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-09-slot-1" data-readable-date="2023-07-09" value="2023-07-09 08:00" id="appt1688911200" aria-labelledby="lbl_appt1688911200">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688911200" for="appt1688911200" aria-label="08:00" aria-checked="false">08:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-09-slot-2" data-readable-date="2023-07-09" value="2023-07-09 09:00" id="appt1688914800" aria-labelledby="lbl_appt1688914800">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688914800" for="appt1688914800" aria-label="09:00" aria-checked="false">09:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-09-slot-3" data-readable-date="2023-07-09" value="2023-07-09 10:00" id="appt1688918400" aria-labelledby="lbl_appt1688918400">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688918400" for="appt1688918400" aria-label="10:00" aria-checked="false">10:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-09-slot-4" data-readable-date="2023-07-09" value="2023-07-09 11:00" id="appt1688922000" aria-labelledby="lbl_appt1688922000">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688922000" for="appt1688922000" aria-label="11:00" aria-checked="false">11:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-09-slot-5" data-readable-date="2023-07-09" value="2023-07-09 12:00" id="appt1688925600" aria-labelledby="lbl_appt1688925600">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688925600" for="appt1688925600" aria-label="12:00" aria-checked="false">12:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-09-slot-6" data-readable-date="2023-07-09" value="2023-07-09 13:00" id="appt1688929200" aria-labelledby="lbl_appt1688929200">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688929200" for="appt1688929200" aria-label="13:00" aria-checked="false">13:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-09-slot-7" data-readable-date="2023-07-09" value="2023-07-09 14:00" id="appt1688932800" aria-labelledby="lbl_appt1688932800">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688932800" for="appt1688932800" aria-label="14:00" aria-checked="false">14:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-09-slot-8" data-readable-date="2023-07-09" value="2023-07-09 15:00" id="appt1688936400" aria-labelledby="lbl_appt1688936400">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688936400" for="appt1688936400" aria-label="15:00" aria-checked="false">15:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-09-slot-9" data-readable-date="2023-07-09" value="2023-07-09 16:00" id="appt1688940000" aria-labelledby="lbl_appt1688940000">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688940000" for="appt1688940000" aria-label="16:00" aria-checked="false">16:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-09-slot-10" data-readable-date="2023-07-09" value="2023-07-09 17:00" id="appt1688943600" aria-labelledby="lbl_appt1688943600">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688943600" for="appt1688943600" aria-label="17:00" aria-checked="false">17:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-09-slot-11" data-readable-date="2023-07-09" value="2023-07-09 18:00" id="appt1688947200" aria-labelledby="lbl_appt1688947200">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688947200" for="appt1688947200" aria-label="18:00" aria-checked="false">18:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-09-slot-12" data-readable-date="2023-07-09" value="2023-07-09 19:00" id="appt1688950800" aria-labelledby="lbl_appt1688950800">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688950800" for="appt1688950800" aria-label="19:00" aria-checked="false">19:00</label>
            <br>

        </div>
        <a href="#calendar-top" class="more-times-available">
            <i class="fa fa-angle-up"></i><br>Horas anteriores <br> Disponible
        </a>
    </div>
</fieldset>
<fieldset class="date  activeday">
    <legend class="date-heading">
        <div class="date-head-text">&nbsp;</div>
        <div class="day-of-week babel-ignore">lunes</div>
        <div class="date-secondary babel-ignore">10 julio</div>
    </legend>
    <div class="choose-time" data-testid="choose-time">
        <div class="form-inline"><input type="radio" class="time-selection" name="time[]" data-testid="select-day-10-slot-0" data-readable-date="2023-07-10" value="2023-07-10 07:00" id="appt1688994000" aria-labelledby="lbl_appt1688994000">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688994000" for="appt1688994000" aria-label="07:00" aria-checked="false">07:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-10-slot-1" data-readable-date="2023-07-10" value="2023-07-10 08:00" id="appt1688997600" aria-labelledby="lbl_appt1688997600">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1688997600" for="appt1688997600" aria-label="08:00" aria-checked="false">08:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-10-slot-2" data-readable-date="2023-07-10" value="2023-07-10 09:00" id="appt1689001200" aria-labelledby="lbl_appt1689001200">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689001200" for="appt1689001200" aria-label="09:00" aria-checked="false">09:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-10-slot-3" data-readable-date="2023-07-10" value="2023-07-10 10:00" id="appt1689004800" aria-labelledby="lbl_appt1689004800">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689004800" for="appt1689004800" aria-label="10:00" aria-checked="false">10:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-10-slot-4" data-readable-date="2023-07-10" value="2023-07-10 11:00" id="appt1689008400" aria-labelledby="lbl_appt1689008400">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689008400" for="appt1689008400" aria-label="11:00" aria-checked="false">11:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-10-slot-5" data-readable-date="2023-07-10" value="2023-07-10 12:00" id="appt1689012000" aria-labelledby="lbl_appt1689012000">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689012000" for="appt1689012000" aria-label="12:00" aria-checked="false">12:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-10-slot-6" data-readable-date="2023-07-10" value="2023-07-10 13:00" id="appt1689015600" aria-labelledby="lbl_appt1689015600">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689015600" for="appt1689015600" aria-label="13:00" aria-checked="false">13:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-10-slot-7" data-readable-date="2023-07-10" value="2023-07-10 14:00" id="appt1689019200" aria-labelledby="lbl_appt1689019200">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689019200" for="appt1689019200" aria-label="14:00" aria-checked="false">14:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-10-slot-8" data-readable-date="2023-07-10" value="2023-07-10 15:00" id="appt1689022800" aria-labelledby="lbl_appt1689022800">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689022800" for="appt1689022800" aria-label="15:00" aria-checked="false">15:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-10-slot-9" data-readable-date="2023-07-10" value="2023-07-10 16:00" id="appt1689026400" aria-labelledby="lbl_appt1689026400">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689026400" for="appt1689026400" aria-label="16:00" aria-checked="false">16:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-10-slot-10" data-readable-date="2023-07-10" value="2023-07-10 17:00" id="appt1689030000" aria-labelledby="lbl_appt1689030000">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689030000" for="appt1689030000" aria-label="17:00" aria-checked="false">17:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-10-slot-11" data-readable-date="2023-07-10" value="2023-07-10 18:00" id="appt1689033600" aria-labelledby="lbl_appt1689033600">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689033600" for="appt1689033600" aria-label="18:00" aria-checked="false">18:00</label>
            <br>

            <input type="radio" class="time-selection" name="time[]" data-testid="select-day-10-slot-12" data-readable-date="2023-07-10" value="2023-07-10 19:00" id="appt1689037200" aria-labelledby="lbl_appt1689037200">
            <label role="radio" data-testid="select_appt" tabindex="0" id="lbl_appt1689037200" for="appt1689037200" aria-label="19:00" aria-checked="false">19:00</label>
            <br>

        </div>
        <a href="#calendar-top" class="more-times-available">
            <i class="fa fa-angle-up"></i><br>Horas anteriores <br> Disponible
        </a>
    </div>
</fieldset>
<fieldset class="date  activeday">
    <legend class="date-heading">
        <div class="date-head-text">&nbsp;</div>
        <div class="day-of-week babel-ignore">martes</div>
        <div class="date-secondary babel-ignore">11 julio</div>
    </legend>
    <div class="choose-time" data-testid="choose-time">
        <div class="form-inline"><input type="radio" class="time-selection" name="time[]" data-testid="select-day-11-slot-0" data-readable-date="2023-07-11" value="2023-07-11 07:00" id="appt1689080400" aria-labelledby="lbl_appt1689080400">
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
            <br>

        </div>
        <a href="#calendar-top" class="more-times-available">
            <i class="fa fa-angle-up"></i><br>Horas anteriores <br> Disponible
        </a>
    </div>
</fieldset> -->
