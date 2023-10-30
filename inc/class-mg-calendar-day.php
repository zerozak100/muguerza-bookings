<?php

/**
 * @property string $date Y-m-d
 * @property mixed $dayOfWeek
 * @property mixed $secondaryDate
 * @property MG_Calendar_Time[] $availableTimes
 * @property MG_Calendar $calendar
 */
class MG_Calendar_Day {

    public $date;
    public $dayOfWeek;
    public $secondaryDate;
    public $availableTimes = array();
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

    public function getAvailableTimes() {
        if ( ! $this->availableTimes ) {
            $this->availableTimes = $this->fetchAvailableTimes();
        }

        return $this->availableTimes;
    }

    /**
     * @return MG_Calendar_Time[]
     */
    public function fetchAvailableTimes() {
        $times = array();

        $api       = MG_Api_Apex::instance();
        $time_list = $api->get_available_time_list( new DateTime( $this->date ), $this->calendar->apexCalendarId );

        foreach( $time_list as $time ) {
            $datetime = DateTime::createFromFormat( 'Y-m-d H:i', "{$this->date} {$time}", $this->calendar->timezone );
            $times[]  = new MG_Calendar_Time( $datetime->getTimestamp(), $this->calendar->timezone );
        }

        return $times;
    }

    public function render() {
        mgb_get_template( 'calendar-weekday.php', array( 'day' => $this ) );
    }
}
