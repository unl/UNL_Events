<?php
	# show promo bar if main calendar and today
	if ($context->calendar->id == UNL\UCBCN::$main_calendar_id && 
		$context->options['m'] == date('m') && 
		$context->options['d'] == date('d') && 
		$context->options['y'] == date('Y')) {
		echo $savvy->render($context, 'EventsPromoBar.tpl.php');
	}
?>
<div class="wdn-grid-set">
    <aside class="bp2-wdn-col-one-third">
        <?php echo $savvy->render($context, 'sidebar.tpl.php'); ?>
    </aside>
    <section id="updatecontent" class="day_cal bp2-wdn-col-two-thirds">
        <?php echo $savvy->render($context, 'hcalendar/Day.tpl.php'); ?>
    </section>
</div>
