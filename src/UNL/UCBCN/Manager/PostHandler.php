<?php
namespace UNL\UCBCN\Manager;

class PostHandler
{
    const NOTICE_LEVEL_SUCCESS = 'success';
    const NOTICE_LEVEL_INFO = 'info';
    const NOTICE_LEVEL_ERROR = 'failure';
    const NOTICE_LEVEL_ALERT = 'alert';

    public function handlePost(array $get, array $post, array $files)
    {
        return Controller::$url;
    }

    public function flashNotice($level, $header, $message)
    {
        $_SESSION['flash_notice'] = array(
            'level' => $level,
            'header' => $header,
            'messageHTML' => $message
        );
    }

    // Shared validation for recurring event
    protected function validateRecurringEvent($post_data, $start_date, $end_date) {
        # check that recurring events have recurring type and correct recurs until date
        if (array_key_exists('recurring', $post_data) && $post_data['recurring'] == 'on') {
            if (empty($post_data['recurring_type']) || empty($post_data['recurs_until_date'])) {
                throw new ValidationException('Recurring events require a <a href="#recurring-type">recurring type</a> and <a href="#recurs-until-date">date</a> that they recur until.');
            }

            $recurs_until = $this->calculateDate($post_data['recurs_until_date'], 11, 59, 'PM');
            if ($start_date > $recurs_until) {
                throw new ValidationException('The <a href="#recurs-until-date">"recurs until date"</a> must be on or after the start date.');
            }

            if (date('d', strtotime($start_date)) !== date('d', strtotime($end_date))) {
                throw new ValidationException('A recurring event instance start and end date must be the same day. If you need multiple multi-day (ongoing) occurrences, you must define them as separate datetime instances.');
            }
        }
    }

    protected function calculateDate($date, $hour, $minute, $am_or_pm)
    {
        # defaults if NULL is passed in
        $hour = $hour == NULL ? 12 : $hour;
        $minute = $minute == NULL ? 0 : $minute;
        $am_or_pm = $am_or_pm == NULL ? 'am' : $am_or_pm;

        $date = strtotime($date . ' ' . $hour . ':' . $minute . ':00 ' . $am_or_pm);
        return date('Y-m-d H:i:s', $date);
    }
}
