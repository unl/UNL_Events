require(['jquery', 'wdn', 'modernizr', frontend_url + 'templates/default/html/js/vendor/select2/js/select2.min.js'], function($, WDN, Modernizr) {
    $(document).ready(function() {
        $(".use-select2").select2();
        
        $('.pending-event-tools').change(function () {
            if ($(this).val() == 'recommend') {
                // redirect to recommend URL
                window.location = $(this).attr('data-recommend-url');
            } else if ($(this).val() == 'delete') {
                if (window.confirm('Are you sure you want to delete this event?')) {
                    $('#delete-' + $(this).attr('data-id')).submit();
                }
            }
        });

        var ordinal = function(number) {
            var mod = number % 100;
            if (mod >= 11 && mod <= 13) {
                return number + 'th';
            } else if (mod % 10 == 1) {
                return number + 'st';
            } else if (mod % 10 == 2) {
                return number + 'nd';
            } else if (mod % 10 == 3) {
                return number + 'rd';
            } else {
                return number + 'th';
            }
        };

        // this needs to be global as it gets tapped by the page js
        setRecurringOptions = function(start_elem, month_group_elem) {
            // get startdate info
            var weekdays = [
                "Sunday", 
                "Monday", 
                "Tuesday",
                "Wednesday", 
                "Thursday", 
                "Friday", 
                "Saturday"
            ];

            var start_year = start_elem.val().substring(6, 10);
            var start_month = start_elem.val().substring(0, 2);
            var start_day = parseInt(start_elem.val().substring(3, 5));
            var start_date = new Date(start_year, start_month - 1, start_day);
            var start_weekday = weekdays[start_date.getDay()];

            // get week in month
            var nth = {
                "1": "First",
                "2": "Second",
                "3": "Third",
                "4": "Fourth",
                "5": "Last"
            };

            // get number of days (28, 29, 30, 31) in month
            var days_in_month = 28;
            d = new Date(start_year, start_month - 1, 28);
            while (days_in_month == d.getDate()) {
                d = new Date(start_year, start_month - 1, ++days_in_month);
            }
            days_in_month--;

            var week = 0; // the week of the start day
            var total_weeks = 0; // total weeks in the month
            for (var i = 1; i <= days_in_month; i++) {
                var d = new Date(start_year, start_month - 1, i);
                if (weekdays[d.getDay()] == start_weekday && i <= start_day) {
                    week++;
                }
                if (weekdays[d.getDay()] == start_weekday) {
                    total_weeks++;
                }
            }

            // remove options, if any
            month_group_elem.children(".dynamicRecurring").remove();
            // populate rectypemonth with appropriate options

            month_group_elem.prepend("<option class='dynamicRecurring' value='" + nth[week].toLowerCase() + "'>" + nth[week] + " " + start_weekday + " of every month</option>")

            if (week == 4 && total_weeks == 4) {
                month_group_elem.prepend("<option class='dynamicRecurring' value='last'>" + "Last " + start_weekday + " of every month</option>")
            }

            if (days_in_month == start_day) {
                month_group_elem.prepend("<option class='dynamicRecurring' value='lastday'>Last day of every month</option>");
            }

            var text = ordinal(start_day) + ' of every month';
            month_group_elem.prepend("<option class='dynamicRecurring' value='date'>" + text + "</option>");
        };
    });
});