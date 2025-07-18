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


const send_to_main_inputs = document.querySelectorAll('input[type=radio][name=send_to_main]');
const hidden_required_for_main_elements = document.querySelectorAll('.required-for-main-calendar');
send_to_main_inputs.forEach((single_for_main_input) => {
    single_for_main_input.addEventListener('change', () => {
        if (single_for_main_input.value === 'on') {
            hidden_required_for_main_elements.forEach((single_hidden_element) => {
                single_hidden_element.classList.remove('dcf-d-none!');
            });
        } else if (single_for_main_input.value === 'off') {
            hidden_required_for_main_elements.forEach((single_hidden_element) => {
                single_hidden_element.classList.add('dcf-d-none!');
            });
        }
    });
    if (single_for_main_input.checked && single_for_main_input.value === 'on') {
        hidden_required_for_main_elements.forEach((single_hidden_element) => {
            single_hidden_element.classList.remove('dcf-d-none!');
        });
    } else if (single_for_main_input.checked && single_for_main_input.value === 'off') {
        hidden_required_for_main_elements.forEach((single_hidden_element) => {
            single_hidden_element.classList.add('dcf-d-none!');
        });
    }
});


const create_form = document.getElementById('create-event-form');
create_form.addEventListener('submit', (submit) => {
    submit.preventDefault();
    const title_input = document.getElementById('title');
    const type_input = document.getElementById('type');
    const start_date_input = document.getElementById('start-date');
    const start_pm_input = document.getElementById('start-time-am-pm-pm');
    const start_hour_input = document.getElementById('start-time-hour');
    const start_minute_input = document.getElementById('start-time-minute');
    const end_pm_input = document.getElementById('end-time-am-pm-pm');
    const end_hour_input = document.getElementById('end-time-hour');
    const end_minute_input = document.getElementById('end-time-minute');
    const time_mode_regular_input = document.getElementById(window.UNL_Events.time_mode_regular);
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
    const send_to_main_on_input = document.getElementById('send_to_main_on');
    const description_input = document.getElementById('description');
    const contact_name_input = document.getElementById('contact-name');
    const send_to_main_checked_input = document.querySelector('input[name="send_to_main"]:checked');
    const cropped_image_data_input = document.getElementById('cropped-image-data');
    const imagedata_input = document.getElementById('contact-name');
    const contact_type_checked_input = document.querySelector('input[name="contact_type"]:checked');
    const contact_type_input = document.getElementById('contact-type');
    const contact_website_input = document.getElementById('contact-website');
    const website_input = document.getElementById('website');

    const errors = [];

    // validate required fields
    if (title_input.value == '' || type_input.value == '' || start_date_input.value == '') {
        if (title_input.value == '') {
            window.UNL_Events.notifier.mark_input_invalid(title_input);
        }
        if (start_date_input.value == '') {
            window.UNL_Events.notifier.mark_input_invalid(start_date_input);
        }
        if (type_input.value == '') {
            window.UNL_Events.notifier.mark_input_invalid(type_input);
        }
        errors.push('<a href="#title">Title</a>, <a href="#type">Type</a>, and <a href="#start-date">start date</a> are required.');
    }

    // translate times from inputs
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
    if (start_date_input.value != '') {
        if (recurring_input.checked) {
            if (recurring_type_input.value == '' || recurs_until_date_input.value == '') {
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

    // Must select whether to consider for main calendar
    if (send_to_main_checked_input.value === undefined) {
        window.UNL_Events.notifier.mark_input_invalid(send_to_main_on_input);
        errors.push('<a href="#send_to_main">Consider for main calendar</a> is required.');

    } else if (send_to_main_checked_input.value === 'on') {
        if (description_input.value.trim() == '') {
            window.UNL_Events.notifier.mark_input_invalid(description_input);
            errors.push('<a href="#description">Description</a> is required when event is considered for main calendar.');
        }
        if (contact_name_input.value.trim() == '') {
            window.UNL_Events.notifier.mark_input_invalid(contact_name_input);
            errors.push('<a href="#contact-name">Contact Name</a> is required when event is considered for main calendar.');
        }
        if (cropped_image_data_input.value.trim() == '' && imagedata_input.value.trim() == '') {
            window.UNL_Events.notifier.mark_input_invalid(imagedata_input);
            errors.push('<a href="#imagedata">Image</a> is required when event is considered for main calendar.');
        }
    }

    if (contact_type_checked_input !== null) {
        if (contact_type_checked_input.value !== undefined &&
            contact_type_checked_input.value !== "person" &&
            contact_type_checked_input.value !== "organization")
        {
            window.UNL_Events.notifier.mark_input_invalid(contact_type_input);
            errors.push('<a href="#contact-type">Contact Type</a> must be person or organization.');
        }
    }

    let contactWebsiteURL = contact_website_input.value
    if (contactWebsiteURL != '' && !isUrlValid(contactWebsiteURL)) {
        window.UNL_Events.notifier.mark_input_invalid(contact_website_input);
        errors.push('<a href="#contact_website">Contact Website</a> is not a valid URL.');
    }

    let websiteURL = website_input.value;
    if (websiteURL != '' && !isUrlValid(websiteURL)) {
        window.UNL_Events.notifier.mark_input_invalid(website_input);
        errors.push('<a href="#website">Event Website</a> is not a valid URL.');
    }

    if (errors.length > 0) {
        window.UNL_Events.notifier.alert('Sorry! We couldn\'t create your event', '<ul><li>' + errors.join('</li><li>') + '</li></ul>');
        return false;
    } else {
        create_form.submit();
    }
});
