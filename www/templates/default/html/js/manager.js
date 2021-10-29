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

Date.prototype.isValid = function () {
    // An invalid date object returns NaN for getTime() and NaN is the only
    // object not strictly equal to itself.
    return !isNaN(this.getTime());
};

// this needs to be global as it gets tapped by the page js
setRecurringOptions = function(start_elem, month_group_elem, rectypemonth) {
    if (!start_elem.val()) { return; }

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

    var start_date = new Date(start_elem.val());
    if (!start_date.isValid()) { return; }
    var start_year = start_date.getFullYear();
    var start_month = start_date.getMonth() + 1;
    var start_day = start_date.getDate();
    var start_weekday = weekdays[start_date.getDay()];

    // get week in month
    var nth = {
        "1": "First",
        "2": "Second",
        "3": "Third",
        "4": "Fourth",
        "5": "Last",
        1: "First",
        2: "Second",
        3: "Third",
        4: "Fourth",
        5: "Last"
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

    var dynamicRecurringSelected = '';
    if (nth[week] != undefined) {
        dynamicRecurringSelected = '';
        if (rectypemonth == nth[week].toLowerCase()) { dynamicRecurringSelected = ' selected="selected" '}
        month_group_elem.prepend("<option class='dynamicRecurring' " + dynamicRecurringSelected + "value='" + nth[week].toLowerCase() + "'>" + nth[week] + " " + start_weekday + " of every month</option>")
    }

    if (week == 4 && total_weeks == 4) {
        dynamicRecurringSelected = '';
        if (rectypemonth == 'last') { dynamicRecurringSelected = ' selected="selected" '}
        month_group_elem.prepend("<option class='dynamicRecurring' " + dynamicRecurringSelected + "value='last'>" + "Last " + start_weekday + " of every month</option>")
    }

    if (days_in_month == start_day) {
      dynamicRecurringSelected = '';
      if (rectypemonth == 'lastday') { dynamicRecurringSelected = ' selected="selected" '}
      month_group_elem.prepend("<option class='dynamicRecurring' " + dynamicRecurringSelected + "value='lastday'>Last day of every month</option>");
    }

    var text = ordinal(start_day) + ' of every month';
    dynamicRecurringSelected = '';
    if (rectypemonth == 'date') { dynamicRecurringSelected = ' selected="selected" '}
    month_group_elem.prepend("<option class='dynamicRecurring' " + dynamicRecurringSelected + "value='date'>" + text + "</option>");
};

require(['jquery', 'wdn', frontend_url + 'templates/default/html/js/vendor/select2/js/select2.min.js'], function($, WDN) {
    $(document).ready(function() {
        $('form').on('change blur', 'input', function() {
            $(this).removeClass('validation-failed');
        });

        $(".use-select2").select2();

        $('#bulk-action').change(function () {
            var ids = [];

            // find ids of all events that are checked
            $('.select-event').each(function() {
                if ($(this).is(':checked')) {
                    ids.push(parseInt($(this).attr('data-id')));
                }
            });

            if (ids.length > 0) {
                $('#bulk-action-ids').val(ids.join(','));
                $('#bulk-action-action').val($(this).val());

                var confirm_string = null;
                if ($(this).val() == 'delete') {
                    confirm_string = 'Are you sure you want to delete these ' + ids.length.toString() + ' events?';
                } else if ($(this).val() == 'move-to-upcoming') {
                    confirm_string = 'Are you sure to move these ' + ids.length.toString() + ' events to "Upcoming"?';
                } else if ($(this).val() == 'move-to-pending') {
                    confirm_string = 'Are you sure to move these ' + ids.length.toString() + ' events to "Pending"?';
                } else {
                    // do nothing
                    return;
                }

                if (window.confirm(confirm_string)) {
                    $('#bulk-action-form').submit();
                }
            }
        });

        $('#checkbox-toggle').click(function (click) {
            $(".select-event").prop("checked", $(this).is(":checked"));
        });

        if ($('#recurring').is(":checked")) {
            $(".recurring-container").show();
        }

        $('#recurring').click(function (click) {
            if ($(this).is(":checked")) {
                $(".recurring-container").show();
            } else {
                $(".recurring-container").hide();
            }
        });

        $('.pending-event-tools, .upcoming-event-tools, .past-event-tools, .searched-event-tools').change(function () {
            if ($(this).val() == 'recommend') {
                // redirect to recommend URL
                window.location = $(this).attr('data-recommend-url');
            } else if ($(this).val() == 'delete') {
                if (window.confirm('Are you sure you want to delete this event?')) {
                    $('#delete-' + $(this).attr('data-id')).submit();
                }
            } else if ($(this).val() == 'move-to-upcoming') {
                $('#move-target-' + $(this).attr('data-id')).val('upcoming');
                $('#move-' + $(this).attr('data-id')).submit();
            } else if ($(this).val() == 'move-to-pending') {
                $('#move-target-' + $(this).attr('data-id')).val('pending');
                $('#move-' + $(this).attr('data-id')).submit();
            } else if ($(this).val() == 'promote') {
                $('#promote-target-' + $(this).attr('data-id')).val('promote');
                $('#promote-' + $(this).attr('data-id')).submit();
            } else if ($(this).val() == 'hide-promo') {
                $('#promote-target-' + $(this).attr('data-id')).val('hide-promo');
                $('#promote-' + $(this).attr('data-id')).submit();
            }
        });

        $('#toggle-search').click(function (click) {
            click.preventDefault();
            $('#search-form').slideToggle(400, function() {
                if ($('#search-form').is(':visible')) {
                    $('#search-form input').focus();
                }
            });
        });

        notifier = {
            mark_input_invalid: function(input) {
                input.addClass('validation-failed');
            },
            failure: function(header, message) {
                notifier.check(header, message, 'dcf-notice-danger');
                $('#notice').removeClass('dcf-notice-info').removeClass('dcf-notice-success').removeClass('dcf-notice-warning').addClass('dcf-notice-danger');
                $('#notice h2').text(header);
                $('#notice .dcf-notice-message').html(message);
                $('#notice').fadeIn();

                window.scrollTo(0,0);
            },
            info: function(header, message) {
                notifier.check(header, message, 'dcf-notice-info');
                $('#notice').addClass('dcf-notice-info').removeClass('dcf-notice-success').removeClass('dcf-notice-warning').removeClass('dcf-notice-danger');
                $('#notice h2').text(header);
                $('#notice .dcf-notice-message').html(message);
                $('#notice').fadeIn();

                window.scrollTo(0,0);
            },
            success: function(header, message) {
                notifier.check(header, message, 'dcf-notice-success');
                $('#notice').removeClass('dcf-notice-info').addClass('dcf-notice-success').removeClass('dcf-notice-warning').removeClass('dcf-notice-danger');
                $('#notice h2').text(header);
                $('#notice .dcf-notice-message').html(message);
                $('#notice').fadeIn();

                window.scrollTo(0,0);
            },
            alert: function(header, message) {
                notifier.check(header, message, 'dcf-notice-warning');
                $('#notice').removeClass('dcf-notice-info').removeClass('dcf-notice-success').addClass('dcf-notice-warning').removeClass('dcf-notice-danger');
                $('#notice h2').text(header);
                $('#notice .dcf-notice-message').html(message);
                $('#notice').fadeIn();

                window.scrollTo(0,0);
            },
            check: function(header, message, type) {
                var noticeContainer = document.getElementById('noticeContainer');
                var notice = document.getElementById('notice');
                if (noticeContainer && !notice) {
                    require(['dcf-notice'], function(DCFNoticeModule) {
                        var dummyNotice = new DCFNoticeModule.DCFNotice();
                        notice = dummyNotice.appendNotice(noticeContainer, header, message, type);
                        notice.setAttribute('id', 'notice');
                    });
                }
            }
        };
    });
});
