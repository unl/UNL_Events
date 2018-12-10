<?php
	# show promo bar if main calendar and today
	if ($context->calendar->id == UNL\UCBCN::$main_calendar_id && 
		$context->options['m'] == date('m') && 
		$context->options['d'] == date('d') && 
		$context->options['y'] == date('Y')) {
		echo $savvy->render($context, 'EventsPromoBar.tpl.php');
	}
?>
<div class="dcf-grid dcf-col-gap-4">
    <aside class="dcf-col-100% dcf-col-33%-start@md">
        <?php echo $savvy->render($context, 'sidebar.tpl.php'); ?>
    </aside>
    <section id="updatecontent" class="day_cal dcf-col-100% dcf-col-67%-end@md">
        <?php echo $savvy->render($context, 'hcalendar/Day.tpl.php'); ?>
    </section>
</div>
