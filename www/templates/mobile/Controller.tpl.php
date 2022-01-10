<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" ><!-- InstanceBegin template="/Templates/php.fixed.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>UNL <?php
if ($context->calendar->id != $GLOBALS['_UNL_UCBCN']['default_calendar_id']) {
    echo '| '.$context->calendar->name.' ';
}
?>| Events</title>

<link rel="alternate" type="application/rss+xml" title="<?php echo $context->calendar->name; ?> Events" href="<?php echo UNL_UCBCN_Frontend::formatURL(array('calendar'=>$context->calendar->id,'format'=>'rss')); ?>" />
<!-- InstanceEndEditable -->
</head>
<body id="mobilecal">
<form class="dcf-form" id="event_search" name="event_search" method="get" action="<?php echo UNL_UCBCN_Frontend::formatURL(array('calendar'=>$context->calendar->id,'search'=>'search')); ?>">
    <input type='text' name='q' id='searchinput' alt='Search for events' value="<?php if (isset($_GET['q'])) { echo htmlentities($_GET['q']); } ?>" />
    <input class="dcf-btn dcf-btn-primary" type='submit' name='submit' value="Search" />
    <input type='hidden' name='search' value='search' />
</form>
<ul id="frontend_view_selector" class="<?php echo $context->view; ?>">
    <li id="todayview"><a href="<?php echo UNL_UCBCN_Frontend::formatURL(array('calendar'=>$context->calendar->id)); ?>">Today</a></li>
    <li id="monthview"><a href="<?php echo UNL_UCBCN_Frontend::formatURL(array('y'=>date('Y'),
                                                                                'm'=>date('m'),
                                                                                'calendar'=>$context->calendar->id)); ?>">Month</a></li>
    <li id="yearview"><a href="<?php echo UNL_UCBCN_Frontend::formatURL(array('y'=>date('Y'),
                                                                              'calendar'=>$context->calendar->id)); ?>">Year</a></li>
    <li id="upcomingview"><a href="<?php echo UNL_UCBCN_Frontend::formatURL(array('calendar'=>$context->calendar->id,
                                                                                  'upcoming'=>'upcoming')); ?>">Upcoming</a></li>
</ul>
    
<?php if (isset($context->right)) { ?>
    <div id="updatecontent" class="three_col right">
    <?php echo $savvy->render($context->output); ?>
    </div>
    <div class="col left">
        <div id="monthwidget"><?php echo $savvy->render($context->right); ?></div>
    </div>
<?php } else {
    echo $savvy->render($context->output);
} ?>

</body>
</html>
