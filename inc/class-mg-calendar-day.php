<?php

/**
 * @property string $date Y-m-d
 * @property mixed $dayOfWeek
 * @property mixed $secondaryDate
 * @property MG_Calendar $calendar
 */
class MG_Calendar_Day {

    public $date;
    public $dayOfWeek;
    public $secondaryDate;
    public $calendar;

    public function __construct( string $date, MG_Calendar $calendar ) {
        setlocale( LC_ALL, 'es_ES' );

        $this->date          = $date;
        $this->dayOfWeek     = strftime( '%A', strtotime( $date ) );
        $this->secondaryDate = date( 'd M', strtotime( $date ) );

        $this->calendar      = $calendar;
    }

    public function getDayOfWeek() {
        return $this->dayOfWeek;
    }

    public function getSecondaryDate() {
        return $this->secondaryDate;
    }

    /**
     * @return MG_Calendar_Time[] $availableTimes
     */
    public function getAvailableHours() {
        return $this->calendar->getDayHours( $this->date );
    }

    public function render() {
        mgb_get_template( 'calendar-weekday.php', array( 'day' => $this ) );
    }
}
