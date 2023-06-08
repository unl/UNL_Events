function isUrlValid(string) {
    let url;

    try {
        url = new URL(string);
    } catch (_) {
        return false;  
    }

    return url.protocol === "http:" || url.protocol === "https:";
}

require(['jquery'], function ($) {
    $('#add-datetime-form').submit(function (submit) {
        let errors = [];

        // validate required fields
        if ($('#start-date').val() == '') {
            if ($('#start-date').val() == '') {
                notifier.mark_input_invalid($('#start-date'));
            }
            errors.push('<a href="#location">Location</a> and <a href="#start-date">start date</a> are required.');
        }

        let start = new Date($('#start-date').val());
        if ($('#start-date').val() != '') {
            // validate end date is after start date and the time is afterward accordingly
            if ($('#end-date').val() != '') {
                let end = new Date($('#end-date').val());

                // translate times from inputs. Blank hour = 12, blank minute = 0, blank am/pm = am
                let start_am_pm = $('#start-time-am-pm-pm').is(':checked') ? 'pm' : 'am';
                let start_hour = $('#start-time-hour').val() != '' ? parseInt($('#start-time-hour').val()) % 12 : 0;
                start_hour = start_am_pm == 'pm' ? start_hour + 12 : start_hour;
                let start_minute = $('#start-time-minute').val() != '' ? parseInt($('#start-time-minute').val()) : 0;
                start.setHours(start_hour);
                start.setMinutes(start_minute);

                let end_am_pm = $('#end-time-am-pm-pm').is(':checked') ? 'pm' : 'am';
                let end_hour = $('#end-time-hour').val() != '' ? parseInt($('#end-time-hour').val()) % 12 : 0;
                end_hour = end_am_pm == 'pm' ? end_hour + 12 : end_hour;
                let end_minute = $('#end-time-minute').val() != '' ? parseInt($('#end-time-minute').val()) : 0;
                end.setHours(end_hour);
                end.setMinutes(end_minute);

                if (start > end) {
                    notifier.mark_input_invalid($('#end-date'));
                    errors.push('Your <a href="#end-date">end date/time</a> must be on or after the <a href="#start-date">start date/time</a>.');
                }
            }
        }

        // if recurring is checked, there must be a recurring type and the recurs_until date must be on
        // or after the start date
        if ($('#start-date').val() != '') {
            if ($('#recurring').is(':checked')) {
                if ($('#recurring-type').val() == '' || $('#recurs-until-date').val() == '') {
                    if ($('#recurring-type').val() == '') {
                        notifier.mark_input_invalid($('#recurring-type'));
                    }
                    if ($('#recurs-until-date').val() == '') {
                        notifier.mark_input_invalid($('#recurs-until-date'));
                    }
                    errors.push('Recurring events require a <a href="#recurring-type">recurring type</a> and <a href="#recurs-until-date">date</a> that they recur until.');
                }

                if ($('#end-date').val() != '') {
                    var instanceStart = new Date($('#start-date').val());
                    var instanceEnd = new Date($('#end-date').val());
                    if (instanceStart && instanceEnd && instanceStart.getDate() != instanceEnd.getDate()) {
                        errors.push('A recurring event instance start and end date must be the same day. If you need multiple multi-day (ongoing) occurrences, you must define them as separate datetime instances.');
                    }
                }

                // check that the recurs until date is on or after the start date
                start.setHours(0);
                start.setMinutes(0);
                var until = new Date($('#recurs-until-date').val());

                if (start > until) {
                    notifier.mark_input_invalid($('#recurs-until-date'));
                    errors.push('The <a href="#recurs-until-date">"recurs until date"</a> must be on or after the start date.');
                }
            }
        }

        // new locations must have a name, address, city, state, and zip
        if ($('#physical_location_check').val() == '1') {
            if ($('#location').val() == 'new' && $('#location-name').val() == '') {
                notifier.mark_input_invalid($('#location-name'));
                errors.push('You must give your new location a <a href="#location-name">name</a>.');
            }

            if ($('#location').val() == 'new' && $('#location-address-1').val() == '') {
                notifier.mark_input_invalid($('#location-address-1'));
                errors.push('You must give your new location a <a href="#location-address-1">address</a>.');
            }

            if ($('#location').val() == 'new' && $('#location-city').val() == '') {
                notifier.mark_input_invalid($('#location-city'));
                errors.push('You must give your new location a <a href="#location-city">city</a>.');
            }

            if ($('#location').val() == 'new' && $('#location-state').val() == '') {
                notifier.mark_input_invalid($('#location-state'));
                errors.push('You must give your new location a <a href="#location-state">state</a>.');
            }

            if ($('#location').val() == 'new' && $('#location-zip').val() == '') {
                notifier.mark_input_invalid($('#location-zip'));
                errors.push('You must give your new location a <a href="#location-zip">zip</a>.');
            }

            if ($('#location').val() == 'new' && $('#location-webpage').val() !== '' && !isUrlValid($('#location-webpage').val())) {
                notifier.mark_input_invalid($('#location-webpage'));
                errors.push('<a href="#location-webpage"> Location URL</a> is not a valid URL.');
            }
        }

        // new virtual locations must have a name, URL
        if ($('#virtual_location_check').val() == '1') {
            if ($('#v-location').val() == 'new' && $('#new-v-location-name').val() == '') {
                notifier.mark_input_invalid($('#new-v-location-name'));
                errors.push('You must give your new virtual location a <a href="#new-v-location-name">name</a>.');
            }

            if ($('#v-location').val() == 'new' && $('#new-v-location-url').val() == '') {
                notifier.mark_input_invalid($('#new-v-location-url'));
                errors.push('You must give your new virtual location a <a href="#new-v-location-url">URL</a>.');
            } else if ($('#v-location').val() == 'new' && $('#new-v-location-url').val() !== '' && !isUrlValid($('#new-v-location-url').val())) {
                notifier.mark_input_invalid($('#new-v-location-url'));
                errors.push('<a href="#new-v-location-url">Virtual Location URL</a> is not a valid URL.');
            }
        }

        if (errors.length > 0) {
            submit.preventDefault();
            notifier.alert('Sorry! We couldn\'t create your event', '<ul><li>' + errors.join('</li><li>') + '</li></ul>');
        }
    });
});