<?php

class MG_Calendar_Day {

    /**
     * @var string
     */
    public $date; // Y-m-d
    public $dayOfWeek;
    public $secondaryDate;

    /**
     * @var DateTimeZone
     */
    public $timezone;

    /**
     * @var MG_Calendar_Time[]
     */
    public $availableTimes = array();

    public function __construct( string $date, DateTimeZone $timezone ) {
        setlocale( LC_ALL, 'es_ES' );

        $this->date          = $date;
        $this->timezone      = $timezone;
        $this->dayOfWeek     = strftime( '%A', strtotime( $date ) );
        $this->secondaryDate = date( 'd M', strtotime( $date ) );
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
        $time_list = $api->get_available_time_list( new DateTime( $this->date ) );

        foreach( $time_list as $time ) {
            $datetime = DateTime::createFromFormat( 'Y-m-d H:i', "{$this->date} {$time}", $this->timezone );
            $times[]  = new MG_Calendar_Time( $datetime->getTimestamp(), $this->timezone );
        }

        return $times;
    }

    public function render() {
        mgb_get_template( 'calendar-weekday.php', array( 'day' => $this ) );
    }
}
