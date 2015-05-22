require([frontend_url + "templates/default/html/js/vendor/select2/js/select2.min.js"], function(select2) {
	WDN.jQuery(".use-select2").select2();
});

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