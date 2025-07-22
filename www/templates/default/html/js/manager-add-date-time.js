function isUrlValid(string) {
    let url;

    try {
        url = new URL(string);
    } catch (_) {
        return false;
    }

    return url.protocol === "http:" || url.protocol === "https:";
}

// Sets up time input variables
const time_mode_radios = Array.from(document.querySelectorAll('input[type="radio"][name="time_mode"]'));
const time_container = document.getElementById('time-container');
const start_time_container = document.getElementById('start-time-container');
const end_time_container = document.getElementById('end-time-container');

// This will update which of the time field sets are shown when a radio button is clicked
time_mode_radios.forEach((radio) => {
    radio.addEventListener('input', () => {
        const checked_radio = time_mode_radios.filter((radio) => radio.checked)[0];
        if (checked_radio.id === window.UNL_Events.time_mode_regular) {
            time_container.classList.remove('dcf-d-none');
            start_time_container.classList.remove('dcf-d-none');
            end_time_container.classList.remove('dcf-d-none');

        } else if (checked_radio.id === window.UNL_Events.time_mode_end_time_only) {
            time_container.classList.remove('dcf-d-none');
            start_time_container.classList.add('dcf-d-none');
            end_time_container.classList.remove('dcf-d-none');

        } else if (checked_radio.id === window.UNL_Events.time_mode_start_time_only) {
            time_container.classList.remove('dcf-d-none');
            start_time_container.classList.remove('dcf-d-none');
            end_time_container.classList.add('dcf-d-none');

        } else {
            time_container.classList.add('dcf-d-none');
        }
    });
});

const add_datetime_form = document.getElementById('add-datetime-form');
add_datetime_form.addEventListener('submit', (submit) => {
    submit.preventDefault();
    let errors = [];

    const start_date_input = document.getElementById('start-date');
    const start_pm_input = document.getElementById('start-time-am-pm-pm');
    const start_hour_input = document.getElementById('start-time-hour');
    const start_minute_input = document.getElementById('start-time-minute');
    const end_pm_input = document.getElementById('end-time-am-pm-pm');
    const end_hour_input = document.getElementById('end-time-hour');
    const end_minute_input = document.getElementById('end-time-minute');
    const time_mode_regular_input = document.getElementById(window.UNL_Events.time_mode_regular);
    const end_time_container = document.getElementById('end-time-container');
    const recurring_input = document.getElementById('recurring');
    const recurring_type_input = document.getElementById('recurring-type');
    const recurs_until_date_input = document.getElementById('recurs-until-date');
    const physical_location_check_input = document.getElementById('physical_location_check');
    const location_input = document.getElementById('location');
    const location_name_input = document.getElementById('location-name');
    const location_address_1_input = document.getElementById('location-address-1');
    const location_city_input = document.getElementById('location-city');
    const location_state_input = document.getElementById('location-state');
    const location_zip_input = document.getElementById('location-zip');
    const location_webpage_input = document.getElementById('location-webpage');
    const virtual_location_check_input = document.getElementById('virtual_location_check');
    const virtual_location_input = document.getElementById('v-location');
    const virtual_location_name_input = document.getElementById('new-v-location-name');
    const virtual_location_url_input = document.getElementById('new-v-location-url');

    // validate required fields
    if (start_date_input.value == '') {
        if (start_date_input.value == '') {
            window.UNL_Events.notifier.mark_input_invalid(start_date_input);
        }
        errors.push('<a href="#start-date">Start date</a> is required.');
    }

    // Translate times from inputs
    let start_date = new Date(start_date_input.value);
    let start_am_pm = start_pm_input.checked ? 'pm' : 'am';
    let start_hour = start_hour_input.value != '' ? parseInt(start_hour_input.value) % 12 : 0;
    start_hour = start_am_pm == 'pm' ? start_hour + 12 : start_hour;
    let start_minute = start_minute_input.value != '' ? parseInt(start_minute_input.value) : 0;
    start_date.setHours(start_hour);
    start_date.setMinutes(start_minute);

    // This is setting up the end date
    let end_date = new Date(start_date_input.value);
    let end_am_pm = end_pm_input.checked ? 'pm' : 'am';
    let end_hour = end_hour_input.value != '' ? parseInt(end_hour_input.value) % 12 : 0;
    end_hour = end_am_pm == 'pm' ? end_hour + 12 : end_hour;
    let end_minute = end_minute_input.value != '' ? parseInt(end_minute_input.value) : 0;
    end_date.setHours(end_hour);
    end_date.setMinutes(end_minute);

    // Check that the start time is before the end time
    // We only care if the time mode is regular
    if (start_date_input.value != '' && time_mode_regular_input.checked && start_date > end_date) {
        window.UNL_Events.notifier.mark_input_invalid(end_time_container);
        errors.push('Your <a href="#end-time-container">end time</a> must be on or after the <a href="#start-time-container">start time</a>.');
    }

    // if recurring is checked, there must be a recurring type and the recurs_until date must be on
    // or after the start date
    if (start_date_input.value != '' && recurring_input.checked) {
        if (recurring_type_input.value== '' || recurs_until_date_input.value == '') {
            if (recurring_type_input.value == '') {
                window.UNL_Events.notifier.mark_input_invalid(recurring_type_input);
            }
            if (recurs_until_date_input.value == '') {
                window.UNL_Events.notifier.mark_input_invalid(recurs_until_date_input);
            }
            errors.push('Recurring events require a <a href="#recurring-type">recurring type</a> and <a href="#recurs-until-date">date</a> that they recur until.');
        }

        // check that the recurs until date is on or after the start date
        start_date.setHours(0);
        start_date.setMinutes(0);
        let until = new Date(recurs_until_date_input.value);

        if (start_date > until) {
            window.UNL_Events.notifier.mark_input_invalid(recurs_until_date_input);
            errors.push('The <a href="#recurs-until-date">"recurs until date"</a> must be on or after the start date.');
        }
    }

    // new locations must have a name, address, city, state, and zip
    if (physical_location_check_input.value == '1') {
        if (location_input.value == 'new' && location_name_input.value == '') {
            window.UNL_Events.notifier.mark_input_invalid(location_name_input);
            errors.push('You must give your new location a <a href="#location-name">name</a>.');
        }

        if (location_input.value == 'new' && location_address_1_input.value == '') {
            window.UNL_Events.notifier.mark_input_invalid(location_address_1_input);
            errors.push('You must give your new location a <a href="#location-address-1">address</a>.');
        }

        if (location_input.value == 'new' && location_city_input.value == '') {
            window.UNL_Events.notifier.mark_input_invalid(location_city_input);
            errors.push('You must give your new location a <a href="#location-city">city</a>.');
        }

        if (location_input.value == 'new' && location_state_input.value == '') {
            window.UNL_Events.notifier.mark_input_invalid(location_state_input);
            errors.push('You must give your new location a <a href="#location-state">state</a>.');
        }

        if (location_input.value == 'new' && location_zip_input.value == '') {
            window.UNL_Events.notifier.mark_input_invalid(location_zip_input);
            errors.push('You must give your new location a <a href="#location-zip">zip</a>.');
        }

        if (location_input.value == 'new' && location_webpage_input.value !== '' && !isUrlValid(location_webpage_input.value)) {
            window.UNL_Events.notifier.mark_input_invalid(location_webpage_input);
            errors.push('<a href="#location-webpage"> Location URL</a> is not a valid URL.');
        }
    }

    // new virtual locations must have a name, URL
    if (virtual_location_check_input.value == '1') {
        if (virtual_location_input.value == 'new' && virtual_location_name_input.value == '') {
            window.UNL_Events.notifier.mark_input_invalid(virtual_location_name_input);
            errors.push('You must give your new virtual location a <a href="#new-v-location-name">name</a>.');
        }

        if (virtual_location_input.value == 'new' && virtual_location_url_input.value == '') {
            window.UNL_Events.notifier.mark_input_invalid(virtual_location_url_input);
            errors.push('You must give your new virtual location a <a href="#new-v-location-url">URL</a>.');
        } else if (virtual_location_input.value == 'new' && virtual_location_url_input.value !== '' && !isUrlValid(virtual_location_url_input.value)) {
            window.UNL_Events.notifier.mark_input_invalid(virtual_location_url_input);
            errors.push('<a href="#new-v-location-url">Virtual Location URL</a> is not a valid URL.');
        }
    }

    if (errors.length > 0) {
        window.UNL_Events.notifier.alert('Sorry! We couldn\'t create your event', '<ul><li>' + errors.join('</li><li>') + '</li></ul>');
    } else {
        add_datetime_form.submit();
    }
});