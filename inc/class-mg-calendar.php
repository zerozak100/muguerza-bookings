<?php

// use benhall14\phpCalendar\Calendar as Calendar;

class MG_Calendar {

    public $startDate; // Y-m-d
    /**
     * @var DateTimeZone
     */
    public $timezone;

    public $nextStartDate;
    public $prevStartDate;

    /**
     * @var \MG_Calendar_Day[] $days
     */
    public $days = array();

    public function __construct( string $startDate, string $timezone = 'America/Monterrey' ) {

        include_once MGB_PLUGIN_PATH . "/inc/class-mg-calendar-day.php";
        include_once MGB_PLUGIN_PATH . "/inc/class-mg-calendar-time.php";

        $this->startDate = $startDate;
        $this->timezone = new DateTimeZone( $timezone );

        $this->setDays();
        $this->setCalendarNext();
        $this->setCalendarPrev();
    }

    public function setDays() {
        for ( $i = 1; $i <= 5; $i++ ) {
            $day = date( 'Y-m-d', strtotime( "+$i day", strtotime( $this->startDate ) ) );
            $this->days[] = new MG_Calendar_Day( $day, $this->timezone );
        }
    }

    public function setCalendarNext() {
        $this->nextStartDate = date( 'Y-m-d', strtotime( "+5 day", strtotime( $this->startDate ) ) );
    }

    public function setCalendarPrev() {
        $startDate = DateTime::createFromFormat( 'Y-m-d', $this->startDate );
        $now = new DateTime();

        $prevStartDate = date( 'Y-m-d', strtotime( "-5 day", $startDate->getTimestamp() ) );
        $prevStartDate = DateTime::createFromFormat( 'Y-m-d', $prevStartDate );

        if ( $now < $startDate ) {
            $this->prevStartDate = $prevStartDate->format( 'Y-m-d' );
        }
    }

    public function getDays() {
        return $this->days;
    }

    public function scripts() {
        wp_enqueue_style( 'scheduler-weekly', mgb_asset_url_css( 'scheduler-weekly.css' ) );
        wp_enqueue_script( 'scheduler-weekly', mgb_asset_url_js( 'scheduler-weekly.js' ), array( 'jquery' ) );

        wp_localize_script( 'scheduler-weekly', 'AJAX', array(
            'adminUrl' => admin_url( 'admin-ajax.php' ),
        ) );
    }

    public function display() {
        mgb_get_template( 'calendar-container.php', array( 'calendar' => $this ) );
    }

    public function renderContent() {
        mgb_get_template( 'calendar-content.php', array( 'calendar' => $this ) );
    }
}
