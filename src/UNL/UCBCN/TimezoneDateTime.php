<?php
namespace UNL\UCBCN;

class TimezoneDateTime
{
    private $timezone;
    const DST_START = '20070311T020000';
    const ST_START = '20071104T020000';


    public function __construct(String $timezoneString)
    {
        $this->timezone = new \DateTimeZone($timezoneString);
        $this->UTCTimezone = new \DateTimeZone($timezoneString);
    }

    public function getDateTime(String $dateTimeString) {
        return new \DateTime($dateTimeString, $this->timezone);
    }

    public function getDateTimeAddInterval(String $dateTimeString, String $addInterval) {
        $dateTime = new \DateTime($dateTimeString, $this->timezone);
        return $dateTime->add(new \DateInterval($addInterval));
    }

    public function getTimestamp(String $dateTimeString) {
        $dateTime = $this->getDateTime($dateTimeString);
        return $dateTime->format('U');
    }

    public function format(String $dateTimeString, String $format) {
        $dateTime = $this->getDateTime($dateTimeString);
        return $dateTime->format($format);
    }

    public function formatUTC(String $dateTimeString, String $format) {
        $dateTime = $this->getDateTime($dateTimeString);
        return gmdate($format, $dateTime->format('U'));
    }
}