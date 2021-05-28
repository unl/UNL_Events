<div class="section-heading">
	<h2 class="upcoming-heading">Featured Events</h2>
	<div class="links">
		<a href="<?php echo $frontend->getCalendarURL(); ?>featured/.ics" aria-label="I C S format for featured events">
			<svg class="dcf-h-4 dcf-w-4 dcf-fill-current" focusable="false" width="24" height="24" viewBox="0 0 24 24"><path d="M23.5 2H20V.5a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5V2H8V.5a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5V2H.5a.5.5 0 00-.5.5V7h24V2.5a.5.5 0 00-.5-.5zM7 4H5V1h2v3zm12 0h-2V1h2v3zM0 23.5a.5.5 0 00.5.5h23a.5.5 0 00.5-.5V8H0v15.5zM7 15h4v-4a1 1 0 012 0v4h4a1 1 0 010 2h-4v4a1 1 0 01-2 0v-4H7a1 1 0 010-2z"></path></svg>
		</a>
	</div>
</div>

<?php echo $savvy->render($context, 'EventListing.tpl.php');?>
