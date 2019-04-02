<?php
/**
 * This class defines a 30 day widget containing information for a given month.
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN_Frontend
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @version   CVS: $id$
 * @link      http://code.google.com/p/unl-event-publisher/
 */
namespace UNL\UCBCN\Frontend;

/**
 * Class defines a month widget, basically a table with 30 boxes representing the
 * days in the month. Days which have events will be selected.
 *
 * @category  Events
 * @package   UNL_UCBCN_Frontend
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class MonthWidget extends Month
{
    public $data = array();
    
    /**
     * Constructor for an individual day.
     *
     * @param array $options Associative array of options to apply.
     * @throws InvalidArgumentException
     */
    public function __construct($options)
    {
        parent::__construct($options);

        $this->data = $this->getEventTotals($this->getDatePeriod());
    }

    public function getIterator()
    {
        return new \IteratorIterator($this->getDatePeriod());
    }
    
    public function getDayURL(\DateTime $datetime)
    {
        return Day::generateURL($this->calendar, $datetime);
    }
    /**
     * This function finds ongoing events for the given month.
     *
     * @param $datePeriod
     * @internal param \UNL\UCBCN\Frontend\Calendar_Month $month Month to find ongoing events for.
     *
     * @return array
     */
    public function getEventTotals(\DatePeriod $datePeriod)
    {
        $results = array();
        foreach ($datePeriod as $dt) {
            $options = array(
                'calendar' => $this->calendar,
                'm' => $dt->format('m'),
                'd' => $dt->format('d'),
                'y' => $dt->format('Y')
            );
            $day = new Day($options);
            if (count($day) > 0) {
                $results[$dt->format("Y-m-d")] = count($day);
            }
        }

        return $results;
    }
}
