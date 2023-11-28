//TODO: Validate start-time and end-time are set if time-mode is regular and end-time are after start-time
//TODO: Remove instances of end-date
//TODO: If time-mode radio buttons are checked we need to update visibility of times

function isUrlValid(string) {
    let url;

    try {
        url = new URL(string);
    } catch (_) {
        return false;
    }

    return url.protocol === "http:" || url.protocol === "https:";
}

const google_microdata_button = document.getElementById('google-microdata-button');
document.addEventListener(`ModalOpenEvent_${google_microdata_button.dataset.togglesModal}`, () => {
    testMicrodata(true);
});

document.addEventListener('click', (e) => {
    testMicrodata(false);
});
document.addEventListener('input', (e) => {
    testMicrodata(false);
});

// We need this because the dcf-datepicker
document.getElementById('start-date').addEventListener('change', (e) => {
    testMicrodata(false);
})

// This is for when we first load the page
testMicrodata(false);

function testMicrodata(modal_output=true) {
    let microdata_pass = true;
    const google_microdata_button = document.getElementById('google-microdata-button');
    const google_microdata_modal_output = document.getElementById('google-microdata-modal-output');
    let modal_error_output_html = "";

    const title_input = document.getElementById('title');
    const start_date_input = document.getElementById('start-date');

    const location_check_input = document.getElementById('physical_location_check');
    const virtual_location_check_input = document.getElementById('virtual_location_check');

    const contact_name_input = document.getElementById('contact-name');
    const contact_url_input = document.getElementById('contact-website');
    const contact_type_input = document.querySelector('input[name="contact_type"]:checked');

    if (title_input.value == "") {
        if (modal_output) {
            modal_error_output_html += "<li>Missing title</li>";
        }
        microdata_pass = false;
    }

    if (start_date_input.value == "") {
        if (modal_output) {
            modal_error_output_html += "<li>Missing start date</li>";
        }
        microdata_pass = false;
    }

    if (location_check_input.value == "0" && virtual_location_check_input.value == "0") {
        if (modal_output) {
            modal_error_output_html += "<li>Missing a location (Could be virtual, physical, or both)</li>";
        }
        microdata_pass = false;
    }

    if (location_check_input.value == "1") {
        const location_input = document.getElementById('location');

        if (location_input.value == "new") {
            const location_name_input = document.getElementById('location-name');
            const location_address_input = document.getElementById('location-address-1');
            const location_city_input = document.getElementById('location-city');
            const location_state_input = document.getElementById('location-state');
            const location_zip_input = document.getElementById('location-zip');

            if (location_name_input.value == "") {
                if (modal_output) {
                    modal_error_output_html += "<li>Missing a location name</li>";
                }
                microdata_pass = false;
            }
            if (location_address_input.value == "") {
                if (modal_output) {
                    modal_error_output_html += "<li>Missing a location address</li>";
                }
                microdata_pass = false;
            }
            if (location_city_input.value == "") {
                if (modal_output) {
                    modal_error_output_html += "<li>Missing a location city</li>";
                }
                microdata_pass = false;
            }
            if (location_state_input.value == "") {
                if (modal_output) {
                    modal_error_output_html += "<li>Missing a location state</li>";
                }
                microdata_pass = false;
            }
            if (location_zip_input.value == "") {
                if (modal_output) {
                    modal_error_output_html += "<li>Missing a location zip</li>";
                }
                microdata_pass = false;
            }
        } else {
            const location_microdata_check = location_input.options[location_input.selectedIndex].dataset.microdata == "true";
            if (!location_microdata_check) {
                if (modal_output) {
                    modal_error_output_html += "<li>The Location you selected does not meet the requirements for microdata</li>";
                }
                microdata_pass = false;
            }
        }
    }

    if (virtual_location_check_input.value == "1") {
        const v_location_input = document.getElementById('v-location');

        if (v_location_input.value == "new") {
            const location_name_input = document.getElementById('new-v-location-name');
            const location_url_input = document.getElementById('new-v-location-url');

            if (location_name_input.value == "") {
                if (modal_output) {
                    modal_error_output_html += "<li>Missing a virtual location name</li>";
                }
                microdata_pass = false;
            }
            if (location_url_input.value == "") {
                if (modal_output) {
                    modal_error_output_html += "<li>Missing a virtual location URL</li>";
                }
                microdata_pass = false;
            }

            if (location_url_input.value != "" && !isUrlValid(location_url_input.value)) {
                if (modal_output) {
                    modal_error_output_html += "<li>Virtual location's URL is invalid</li>";
                }
                microdata_pass = false;
            }
        }
    }

    if (contact_name_input.value !== "" || contact_url_input.value !== "") {
        if (contact_type_input === null) {
            if (modal_output) {
                modal_error_output_html += "<li>If organizer contact info is used the type must be specified</li>";
            }
            microdata_pass = false;
        }
        if (
            (contact_name_input.value !== "" && contact_url_input.value === "") ||
            (contact_name_input.value === "" && contact_url_input.value !== "")
        ){
            if (modal_output) {
                modal_error_output_html += "<li>Both the organizer name and organizer website must be set if either are used</li>";
            }
            microdata_pass = false;
        }
    }

    if (!microdata_pass) {
        if (modal_output) {
            google_microdata_modal_output.innerHTML = "<span class='dcf-bold'>Your event is missing these requirements:</span> <ul>" + modal_error_output_html + "</ul>";
        }
        google_microdata_button.innerHTML = "! Your event does not reach microdata requirements !";

        google_microdata_button.classList.add('unl-bg-blue');
        google_microdata_button.classList.remove('unl-bg-green');

        google_microdata_button.classList.add('events-b-blue');
        google_microdata_button.classList.remove('events-b-green');
    }else{
        if (modal_output) {
            google_microdata_modal_output.innerHTML = "<span class='dcf-bold'>Your event is fulfilling all microdata requirements</span>";
        }
        google_microdata_button.innerHTML = "Your event does reach microdata requirements";

        google_microdata_button.classList.remove('unl-bg-blue');
        google_microdata_button.classList.add('unl-bg-green');

        google_microdata_button.classList.remove('events-b-blue');
        google_microdata_button.classList.add('events-b-green');
    }
}

require(['jquery', 'wdn'], function ($, WDN) {

    // DCF Date Picker
    WDN.initializePlugin('modals');

    $('input[type=radio][name=send_to_main]').change(function() {
        if (this.value == 'on') {
            $('.required-for-main-calendar').show();
        }
        else if (this.value == 'off') {
            $('.required-for-main-calendar').hide();
        }
    });

    $('#create-event-form').submit(function (submit) {
        let errors = [];

        // validate required fields
        if ($('#title').val() == '' || $('#type').find(':selected').val() == '' || $('#start-date').val() == '') {
            if ($('#title').val() == '') {
                notifier.mark_input_invalid($('#title'));
            }
            if ($('#start-date').val() == '') {
                notifier.mark_input_invalid($('#start-date'));
            }
            if ($('#type').find(':selected').val() == '') {
                notifier.mark_input_invalid($('#type'));
            }
            errors.push('<a href="#title">Title</a>, <a href="#type">Type</a>, and <a href="#start-date">start date</a> are required.');
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
                    let instanceStart = new Date($('#start-date').val());
                    let instanceEnd = new Date($('#end-date').val());
                    if (instanceStart && instanceEnd && instanceStart.getDate() != instanceEnd.getDate()) {
                        errors.push('A recurring event instance start and end date must be the same day. If you need multiple multi-day (ongoing) occurrences, you must define them as separate datetime instances.');
                    }
                }

                // check that the recurs until date is on or after the start date
                start.setHours(0);
                start.setMinutes(0);
                let until = new Date($('#recurs-until-date').val());

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

        // Must select whether to consider for main calendar
        if ($('input[name="send_to_main"]:checked').val() === undefined) {
            notifier.mark_input_invalid($('#send_to_main_on'));
            errors.push('<a href="#send_to_main">Consider for main calendar</a> is required.');
        } else if ($('input[name="send_to_main"]:checked').val() === 'on') {
            if ($('#description').val().trim() == '') {
                notifier.mark_input_invalid($('#description'));
                errors.push('<a href="#description">Description</a> is required when event is considered for main calendar.');
            }
            if ($('#contact-name').val().trim() == '') {
                notifier.mark_input_invalid($('#contact-name'));
                errors.push('<a href="#contact-name">Contact Name</a> is required when event is considered for main calendar.');
            }
            if ($('#cropped-image-data').val().trim() == '' && $('#imagedata').val().trim() == '') {
                notifier.mark_input_invalid($('#imagedata'));
                errors.push('<a href="#imagedata">Image</a> is required when event is considered for main calendar.');
            }
        }

        if ($('input[name="contact_type"]:checked').val() !== undefined &&
            $('input[name="contact_type"]:checked').val() !== "person" &&
            $('input[name="contact_type"]:checked').val() !== "organization") 
        {
            notifier.mark_input_invalid($('#contact-type'));
            errors.push('<a href="#contact-type">Contact Type</a> must be person or organization.');
        }

        let contactWebsiteURL = $('#contact-website').val();
        if (contactWebsiteURL != '' && !isUrlValid(contactWebsiteURL)) {
            notifier.mark_input_invalid($('#contact-website'));
            errors.push('<a href="#contact_website">Contact Website</a> is not a valid URL.');
        }

        let websiteURL = $('#website').val();
        if (websiteURL != '' && !isUrlValid(websiteURL)) {
            notifier.mark_input_invalid($('#website'));
            errors.push('<a href="#website">Event Website</a> is not a valid URL.');
        }

        if (errors.length > 0) {
            submit.preventDefault();
            notifier.alert('Sorry! We couldn\'t create your event', '<ul><li>' + errors.join('</li><li>') + '</li></ul>');
        }
    });
});