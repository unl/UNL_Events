<ul>
<?php

foreach ($context->events as $e) {
    $li = '<li>';
    if (strpos($e->eventdatetime->starttime,'00:00:00')===false) {
        $starttime = strtotime($e->eventdatetime->starttime);
        $li .= date('g',$starttime);
        if (substr($e->eventdatetime->starttime,14,2)!='00') {
            $li .= ':'.substr($e->eventdatetime->starttime,14,2);
        }
        $li .= date('',$starttime);
        if (isset($e->eventdatetime->endtime) &&
            ($e->eventdatetime->endtime != $e->eventdatetime->starttime) &&
            ($e->eventdatetime->endtime > $e->eventdatetime->starttime)) {
                $endtime = strtotime($e->eventdatetime->endtime);
                $li .= '-'.date('g',$endtime);
                if (substr($e->eventdatetime->endtime,14,2)!='00') {
                    $li .= ':'.substr($e->eventdatetime->endtime,14,2);
                }
                $li .= date('',$endtime);
        }
        $li .= ': ';
    }
    $li .= '<a href="'.$e->url.'">'.$savvy->dbStringtoHtml($e->event->title).'</a></li>';
    echo $li;
}
?>
</ul>