<?php
/**
 * @property DateTimeImmutable $startDate // Y-m-d
 * @property DateTimeZone $timezone
 * @property DateTimeImmutable|null $nextStartDate
 * @property DateTimeImmutable|null $prevStartDate
 * @property mixed $apexCalendarId
 * @property MG_Calendar_Day[] $days
 * @property MG_Calendar_Time[] $availableHours
 */
class MG_Calendar {

    public $startDate;
    public $timezone;
    public $nextStartDate = null;
    public $prevStartDate = null;
    public $apexCalendarId;
    public $days = array();
    public $availableHours = array();

    /**
     * @param string $startDate Y-m-d
     */
    public function __construct( string $startDate, $apexCalendarId, string $timezone = 'America/Monterrey' ) {

        include_once MGB_PLUGIN_PATH . "/inc/class-mg-calendar-day.php";
        include_once MGB_PLUGIN_PATH . "/inc/class-mg-calendar-time.php";

        $this->startDate = new DateTimeImmutable( $startDate );
        $this->apexCalendarId = $apexCalendarId;
        $this->timezone = new DateTimeZone( $timezone );
        $this->availableHours = $this->fetchAvailableHours();
        
        // dd( $this->getDayHours( '2023-11-29' )->renderInput() );
        // dd( $this->availableHours );

        $this->setDays();
        $this->setCalendarNext();
        $this->setCalendarPrev();
    }

    /**
     * @return MG_Calendar_Time[]
     */
    public function fetchAvailableHours() {
        $available_hours = array();

        $api       = MG_Api_Apex::instance();
        $time_list = $api->get_available_time_list( $this->startDate, $this->apexCalendarId );

        // dd( $time_list, $this->startDate );
        // $time_list = [ '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00' ];

        foreach( $time_list as $date => $hours ) {
            foreach ( $hours as $h ) {
                $datetime = DateTime::createFromFormat( 'Y-m-d H:i', "{$date} {$h}", $this->timezone );
                $available_hours[ $date ][]  = new MG_Calendar_Time( $datetime->getTimestamp(), $this->timezone );
            }
        }

        return $available_hours;
    }

    public function setDays() {
        for ( $i = 1; $i <= 5; $i++ ) {
            $day = $this->startDate->modify( "+$i day" );
            $this->days[] = new MG_Calendar_Day( $day->format( 'Y-m-d' ), $this );
        }
    }

    public function setCalendarNext() {
        $now = new DateTimeImmutable();
        $nextStartDate = $this->startDate->modify( '+5 days' );
        $interval = $nextStartDate->diff( $now );
        $diff_days = ( int ) $interval->format( '%a' );

        // dump( $diff_days );

        if ( $diff_days < 14 ) {
            $this->nextStartDate = $this->startDate->modify( '+5 days' )->format( 'Y-m-d' );
        }
    }

    public function setCalendarPrev() {
        $now = new DateTime();
        $prevStartDate = $this->startDate->modify( '-5 days' );

        if ( $now < $this->startDate ) {
            $this->prevStartDate = $prevStartDate->format( 'Y-m-d' );
        }
    }

    public function getDays() {
        return $this->days;
    }

    /**
     * @param string $date_key Date key Y-m-d
     */
    public function getDayHours( $date_key ) {
        if ( isset( $this->availableHours[ $date_key ] ) ) {
            return $this->availableHours[ $date_key ];
        }
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
