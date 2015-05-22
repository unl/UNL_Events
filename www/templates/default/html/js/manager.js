require(['jquery', 'wdn', 'modernizr', frontend_url + 'templates/default/html/js/vendor/select2/js/select2.min.js'], function($, WDN, Modernizr) {
	$(document).ready(function() {
		$(".use-select2").select2();
		
		$('.pending-event-tools').change(function () {
			if ($(this).val() == 'recommend') {
				// redirect to recommend URL
				window.location = $(this).attr('data-recommend-url');
			}
		});
	});
});