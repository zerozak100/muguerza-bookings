<?php

class MG_Calendar_Time {
    /**
     * @var int
     */
    public $timestamp;

    /**
     * @var DateTimeZone
     */
    public $timezone;

    public function __construct( int $timestamp, DateTimeZone $timezone ) {
        $this->timestamp = $timestamp;
        $this->timezone  = $timezone;
    }

    public function getId() {
        return "appt{$this->timestamp}";
    }

    public function getTimestamp() {
        return $this->timestamp;
    }

    public function getTimeZone() {
        return $this->timezone;
    }

    public function getDate() {
        return date( 'Y-m-d', $this->timestamp );
    }

    public function getDateTime() {
        $datetime = new DateTime( 'now', $this->timezone );
        $datetime->setTimestamp( $this->timestamp );
        return $datetime->format( 'Y-m-d H:i' );
    }

    public function getTime() {
        $datetime = new DateTime( 'now', $this->timezone );
        $datetime->setTimestamp( $this->timestamp );
        return $datetime->format( 'H:i' );
    }

    public function setTimeZone( string $timezone ) {
        $this->timezone = $timezone;
    }

    public function renderInput() {
        printf(
            '<input type="radio" class="time-selection" name="datetime" data-readable-date="%1$s" value="%2$s" id="%3$s">',
            $this->getDate(),
            $this->getDateTime(),
            $this->getId(),
        );
    }

    public function renderLabel() {
        printf(
            '<label role="radio" id="lbl_%1$s" for="%1$s" aria-label="%2$s">%2$s</label>',
            $this->getId(),
            $this->getTime(),
        );
    }
}