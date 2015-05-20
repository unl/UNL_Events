require(['jquery', 'wdn', 'modernizr'], function($, WDN, Modernizr) {
	$(document).ready(function() {
		$('.pending-event-tools').change(function () {
			if ($(this).val() == 'recommend') {
				// redirect to recommend URL
				window.location = $(this).attr('data-recommend-url');
			}
		});
	});
});