<?php
namespace UNL\UCBCN;

class TimezoneDateTime
{
    private $timezone;

    public function __construct($timezoneString)
    {
        $this->timezone = new \DateTimeZone($timezoneString);
    }

    public function getTimezone() {
        return $this->timezone;
    }

    public function getTimezoneAbbreviation() {
        $dateTime = $this->getDateTime('now');
        return $dateTime->format('T');
    }

    public function getDateTime($dateTimeString) {
        return new \DateTime($dateTimeString, $this->timezone);
    }

    public function getDateTimeAddInterval($dateTimeString, $addInterval) {
        $dateTime = new \DateTime($dateTimeString, $this->timezone);
        $invert = false;

        if (substr($addInterval, 0, 1) == '-'){
            $invert = true;
            $addInterval = ltrim($addInterval, '-');
        }

        $interval = new \DateInterval($addInterval);
        if ($invert === true){
            $interval->invert = 1;
        }

        return $dateTime->add($interval);
    }

    public function getTimestamp($dateTimeString) {
        $dateTime = $this->getDateTime($dateTimeString);
        return $dateTime->format('U');
    }

    public function format($dateTimeString, $format) {
        $dateTime = $this->getDateTime($dateTimeString);
        return $dateTime->format($format);
    }

    public function formatUTC($dateTimeString, $format) {
        $dateTime = $this->getDateTime($dateTimeString);
        return gmdate($format, $dateTime->format('U'));
    }
}