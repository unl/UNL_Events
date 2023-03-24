<?php
/**
 * This is a class that can parse date ranges and dates themselves from input text
 *
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN_Frontend
 * @author    Thomas Neumann <tneumann9@unl.edu>
 * @copyright 2023 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 */
namespace UNL\UCBCN\Frontend;

Class DateStringParser
{
    public $parsed = false;
    public $single = false;
    public $start_date = false;
    public $end_date = false;

    private $word_or_dash = "\s*[\w-]+\s*";
    private $year_regex = "(\d{4})";
    private $month_regex = "(january|february|march|april|may|june|july|august|september|october|november|december" . 
        "|jan|feb|mar|apr|may|jun|jul|aug|sep|sept|oct|nov|dec)";
    private $day_regex = "(sunday|monday|tuesday|wednesday|thursday|friday|saturday)";
    private $this_regex = "(?:this|the)\s+(?:current\s+)?";
    private $next_regex = "(?:next|following|upcoming)\s+";

    private $from_regex = "(from|after|following|since)\s+";
    private $before_regex = "(before|prior to)\s+";

    public function __construct($dateString)
    {
        $input = strtolower($dateString);
        $input = trim(preg_replace('/\s+/', ' ', $input));


        if (!$this->parsed && $this->covertToWeekRange($input)) {
            $this->parsed = true;
        }
        if (!$this->parsed && $this->covertToMonthRange($input)) {
            $this->parsed = true;
        }
        if (!$this->parsed && $this->convertToYearRange($input)) {
            $this->parsed = true;
        }
        if (!$this->parsed && $this->convertToDaysRange($input)) {
            $this->parsed = true;
        }
        if (!$this->parsed && $this->convertGeneralRange($input)) {
            $this->parsed = true;
        }
        if (!$this->parsed && $this->convertRelativeDateString($input)) {
            $this->parsed = true;
            $this->single = true;
        }
        if (!$this->parsed && $this->convertToSingleDate($input)) {
            $this->parsed = true;
            $this->single = true;
        }
    }

    private function covertToWeekRange($input):bool
    {
        // this week
        if (preg_match('/^' . $this->this_regex . 'week$/i', $input)) {
            echo "This week\n";
            $currentDayOfWeek = date('w');
            $daysToSunday = $currentDayOfWeek;
            $daysToSaturday = 6 - $currentDayOfWeek;
            $this->start_date = strtotime("-{$daysToSunday} days");
            $this->end_date = strtotime("+{$daysToSaturday} days");
        }
        // Next week
        elseif (preg_match('/^' . $this->next_regex . 'week$/i', $input)) {
            echo "Next week\n";
            $currentDayOfWeek = date('w');
            $daysToSunday = $currentDayOfWeek;
            $daysToSaturday = 6 - $currentDayOfWeek;
            $this->start_date = strtotime("-{$daysToSunday} days");
            $this->end_date = strtotime("+{$daysToSaturday} days");

            $this->start_date = strtotime('+1 week', $this->start_date);
            $this->end_date = strtotime('+1 weeks', $this->end_date);
        }
        // Next x weeks
        elseif (preg_match('/' . $this->next_regex . '(\d+) weeks?$/i', $input, $matches)) {
            echo "Next X weeks\n";
            $this->start_date = strtotime("today");
            $this->end_date = strtotime(" + ". $matches[1] . " weeks", $this->start_date);
        }
        else {
            return false;
        }
        return true;
    }

    private function covertToMonthRange($input):bool
    {
        // This month
        if (preg_match('/^' . $this->this_regex . 'month$/i', $input)) {
            echo "This month\n";
            $this->start_date = strtotime('first day of this month');
            $this->end_date = strtotime('last day of this month');
        }
        // Next month
        elseif (preg_match('/^' . $this->next_regex . 'month$/i', $input)) {
            echo "Next month\n";
            $this->start_date = strtotime('first day of next month');
            $this->end_date = strtotime('last day of next month');
        }
        // Next x months
        elseif (preg_match('/' . $this->next_regex . '(\d+) months?$/i', $input, $matches)) {
            echo "Next X months\n";
            $this->start_date = strtotime("today");
            $this->end_date = strtotime(" + ". $matches[1] . " months", $this->start_date);
        }
        // Month or Month to Month
        elseif (preg_match('/^' . $this->month_regex . '(?:' . $this->word_or_dash .'' . $this->month_regex . ')?$/i', $input, $matches)) {
            $now = time();

            if (isset($matches[2])) {
                echo "month to month\n";
                $this->start_date = strtotime('first day of ' . $matches[1]);
                $this->end_date = strtotime('last day of ' . $matches[2]);
            } else {
                echo "month\n";
                $this->start_date = strtotime('first day of ' . $matches[1]);
                $this->end_date = strtotime('last day of ' . $matches[1]); 
            }

            if ($now > $this->start_date) {
                $this->start_date = strtotime('+1 year', $this->start_date);
            }
            if ($now > $this->end_date) {
                $end_date = strtotime('+1 year', $this->end_date);
            }
        }
        elseif (preg_match('/^' . $this->this_regex . '' . $this->month_regex . '(?:' . $this->word_or_dash .'' . $this->month_regex . ')?$/i', $input, $matches)) {
            if (isset($matches[2])) {
                echo "this month to month\n";
                $this->start_date = strtotime('first day of ' . $matches[1]);
                $this->end_date = strtotime('last day of ' . $matches[2]);
            } else {
                echo "this month\n";
                $this->start_date = strtotime('first day of ' . $matches[1]);
                $this->end_date = strtotime('last day of ' . $matches[1]); 
            }
        }
        elseif (preg_match('/^' . $this->next_regex . '' . $this->month_regex . '(?:' . $this->word_or_dash .'' . $this->month_regex . ')?$/i', $input, $matches)) {
            if (isset($matches[2])) {
                echo "next month to month\n";
                $this->start_date = strtotime('first day of ' . $matches[1]);
                $this->end_date = strtotime('last day of ' . $matches[2]);
            } else {
                echo "next month\n";
                $this->start_date = strtotime('first day of ' . $matches[1]);
                $this->end_date = strtotime('last day of ' . $matches[1]); 
            }

            $this->start_date = strtotime('+1 year', $this->start_date);
            $this->end_date = strtotime('+1 year', $this->end_date);
        }
        else {
            return false;
        }

        return true;
    }

    private function convertToYearRange($input):bool
    {
        // This year
        if (preg_match('/^' . $this->this_regex . 'year$/i', $input)) {
            echo "this year\n";
            $year = date('Y');
            $this->start_date = strtotime('january 1st, ' . $year);
            $this->end_date = strtotime('december 31st, ' . $year);
        }
        // Next year
        elseif (preg_match('/^' . $this->next_regex . 'year$/i', $input)) {
            echo "next year\n";
            $year = date('Y');
            $this->start_date = strtotime('january 1st, ' . $year . ' +1 year');
            $this->end_date = strtotime('december 31st, ' . $year . ' +1 year');
        }
        // Next x years
        elseif (preg_match('/^' . $this->next_regex . '(\d+) years?$/i', $input, $matches)) {
            echo "Next X years\n";
            $this->start_date = strtotime("today");
            $this->end_date = strtotime(" + ". $matches[1] . " years", $this->start_date);
        }
        // Year or Year to Year
        elseif (preg_match('/^' . $this->year_regex . '(?:' . $this->word_or_dash .'' . $this->year_regex . ')?$/i', $input, $matches)) {
            if (isset($matches[2])) {
                echo "year to year\n";
                $this->start_date = strtotime('january 1st, ' . $matches[1]);
                $this->end_date = strtotime('december 31st, ' . $matches[2]);
            } else {
                echo "year\n";
                $this->start_date = strtotime('january 1st, ' . $matches[1]);
                $this->end_date = strtotime('december 31st, ' . $matches[1]);
            }
        }
        else {
            return false;
        }
        return true;
    }

    private function convertToDaysRange($input):bool
    {
        // day to day
        if (preg_match('/^' . $this->day_regex . '' . $this->word_or_dash .'' . $this->day_regex . '$/i', $input, $matches)) {
            echo "day to day\n";
            $now = time();
            $currentDayOfWeek = date('w');
            $days_of_week = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
            $day_index = array_search($matches[1], $days_of_week);

            $this->start_date = strtotime("-{$currentDayOfWeek} days");
            $this->start_date = strtotime("+{$day_index} days", $this->start_date);

            if ($now > $this->start_date) {
                $this->start_date = strtotime('+1 week', $this->start_date);
            }
            $this->end_date = strtotime($matches[2], $this->start_date);
        }
        // this day to day
        elseif (preg_match('/^' . $this->this_regex . '' . $this->day_regex . '' . $this->word_or_dash .'' . $this->day_regex . '$/i', $input, $matches)) {
            echo "this day to day\n";
            $now = time();
            $currentDayOfWeek = date('w');
            $days_of_week = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
            $day_index = array_search($matches[1], $days_of_week);

            $this->start_date = strtotime("-{$currentDayOfWeek} days");
            $this->start_date = strtotime("+{$day_index} days", $this->start_date);

            $this->end_date = strtotime($matches[2], $this->start_date);
        }
        // next day to day
        elseif (preg_match('/^' . $this->next_regex . '' . $this->day_regex . '' . $this->word_or_dash .'' . $this->day_regex . '$/i', $input, $matches)) {
            echo "this day to day\n";
            $now = time();
            $currentDayOfWeek = date('w');
            $days_of_week = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
            $day_index = array_search($matches[1], $days_of_week);

            $this->start_date = strtotime("-{$currentDayOfWeek} days");
            $this->start_date = strtotime("+{$day_index} days", $this->start_date);

            $this->start_date = strtotime('+1 week', $this->start_date);

            $this->end_date = strtotime($matches[2], $this->start_date);
        }
        else {
            return false;
        }

        return true;
    }

    private function convertGeneralRange($input):bool
    {
        $start_date = false;
        $end_date = false;
        
        // Attempt to extract the start and end dates from the input string
        $date_range_parts = preg_split('/\s*(?:to|through)\s+/i', $input);
        if (count($date_range_parts) == 2) {
            $start_date = strtotime(trim($date_range_parts[0]));
            $end_date = strtotime(trim($date_range_parts[1]));
        } else {
            // Attempt to extract the start and end dates from the input string with different separators
            $date_range_parts = preg_split('/\s*-\s*/i', $input);
            if (count($date_range_parts) == 2) {
                $start_date = strtotime(trim($date_range_parts[0]));
                $end_date = strtotime(trim($date_range_parts[1]));
            }
        }
        
        // If the start or end date could not be parsed, return false
        if ($start_date === false || $end_date === false) {
            return false;
        }

        echo "general\n";
        $this->start_date = $start_date;
        $this->end_date = $end_date;

        return true;
    }

    private function convertToSingleDate($input)
    {
        $this->start_date = strtotime($input);
        // Check if the end date was calculated successfully
        if ($this->start_date !== false) {
            // Convert the end date to a string and return it
            echo "single date\n";
            return true;
        }
        return false;
    }

    private function convertRelativeDateString($dateString) {
        // Map time intervals to their corresponding strtotime format characters
        $intervalMap = [
            'day',
            'days',
            'week',
            'weeks',
            'month',
            'months',
            'year',
            'years',
        ];
    
        // Split the input string into words
        $words = explode(' ', $dateString);
    
        // Check if the string starts with a number
        if (is_numeric($words[0])) {
            // Get the number and time interval
            $num = $words[0];
            $interval = $words[1];

            // Check if the interval is valid
            if (in_array($interval, $intervalMap)) {

                $relative_date = implode(' ', array_slice($words, 2));

                if (preg_match('/' . $this->from_regex . '/i', $relative_date)) {
                    $relation = "+";
                } else if(preg_match('/' . $this->before_regex . '/i', $relative_date)) {
                    $relation = "-";
                } else {
                    $relation = "+";
                }

                $relative_date = preg_replace('/\s*' . $this->from_regex. '\s*/i', '', $relative_date);
                $relative_date = preg_replace('/\s*' . $this->before_regex. '\s*/i', '', $relative_date);
                $start_date = strtotime($relative_date);

                if ($start_date === false) {
                    $start_date = strtotime("now");
                }

                // Add the number of intervals to the starting date
                $endDate = strtotime($relation . $num . " " . $interval, $start_date);

                // Check if the end date was calculated successfully
                if ($endDate !== false) {
                    // Convert the end date to a string and return it
                    echo "relative end\n";
                    $this->start_date = $endDate;
                    return true;
                }
            }
        }
    
        // Return false if the input string could not be parsed
        return false;
    }
}