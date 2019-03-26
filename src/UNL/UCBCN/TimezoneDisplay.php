<?php
namespace UNL\UCBCN;

class TimezoneDisplay
{
    private $timezoneDateTime;
    private $isClientTime = FALSE;

    public function __construct(String $timezoneString, Bool $isClientTime)
    {
        $this->timezoneDateTime = new \UNL\UCBCN\TimezoneDateTime($timezoneString);
        $this->isClientTime = $isClientTime;
    }

    public function isClientTime() {
        return $this->isClientTime;
    }

    public function getTimezone() {
        return $this->timezoneDateTime->getTimezone();
    }

    public function getTimezoneAbbreviation() {
        return $this->timezoneDateTime->getTimezoneAbbreviation();
    }

    public function format(String $DateTimeString, String $eventTimezone, String $format) {
        $eventTimezoneDateTime = new \UNL\UCBCN\TimezoneDateTime($eventTimezone);

        if ($this->isClientTime === TRUE) {
            // format event datetime relative to client timezone
            $eventDateTime = new \DateTime();
            $eventDateTime->setTimestamp($eventTimezoneDateTime->getTimestamp($DateTimeString));
            $eventDateTime->setTimezone($this->getTimezone());
            return $eventDateTime->format($format);
        } else {
            // format event datetime relative to event timezone
            return $eventTimezoneDateTime->format($DateTimeString, $format);
        }
    }
}