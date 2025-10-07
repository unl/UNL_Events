<div class="dcf-d-grid dcf-grid-cols-12 dcf-col-gap-vw dcf-row-gap-6">
	<aside class="dcf-col-span-12 dcf-col-span-4@md">
		<nav>
            <a class="dcf-show-on-focus" href="#updatecontent">Skip Sidebar</a>
        </nav>
		<?php echo $savvy->render($context, 'sidebar.tpl.php'); ?>
	</aside>
	<section id="updatecontent" class="day_cal dcf-col-span-12 dcf-col-span-8@md">
		<?php echo $savvy->render($context, 'hcalendar/Featured.tpl.php'); ?>
	</section>
</div>
