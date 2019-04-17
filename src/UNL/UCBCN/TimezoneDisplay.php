<?php
namespace UNL\UCBCN;

class TimezoneDisplay
{
    private $timezoneDateTime;

    public function __construct($timezoneString)
    {
        $this->timezoneDateTime = new \UNL\UCBCN\TimezoneDateTime($timezoneString);
    }

    public function getTimezone() {
        return $this->timezoneDateTime->getTimezone();
    }

    public function getTimezoneAbbreviation() {
        return $this->timezoneDateTime->getTimezoneAbbreviation();
    }

    public function getDateTime($dateTimeString) {
        return $this->timezoneDateTime->getDateTime($dateTimeString);
    }

    public function getDateTimeAddInterval($dateTimeString, $addInterval) {
        return $this->timezoneDateTime->getDateTimeAddInterval($dateTimeString, $addInterval);
    }

    public function formatTimestamp($timestamp, $format) {
        $eventDateTime = new \DateTime();
        $eventDateTime->setTimestamp($timestamp);
        $eventDateTime->setTimezone($this->getTimezone());
        return $eventDateTime->format($format);
    }

    public function format($DateTimeString, $eventTimezone, $format) {
        $eventTimezoneDateTime = new \UNL\UCBCN\TimezoneDateTime($eventTimezone);
        return $eventTimezoneDateTime->format($DateTimeString, $format);
    }
}