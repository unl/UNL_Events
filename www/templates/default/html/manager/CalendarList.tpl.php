<?php
    $calendars = $context->getCalendars();
?>
<section class="wdn-grid-set"> 
    <div class="wdn-col-one-half">
        <h3>My Calendars</h3>
        <?php foreach ($calendars as $calendar) { ?>
            <a href="<?php echo $calendar->getManageURL() ?>"><?php echo $calendar->name ?></a><br>
        <?php } ?>
        <a href="#" class="wdn-button wdn-button-brand">+ New Calendar</a>
    </div>
</section>
