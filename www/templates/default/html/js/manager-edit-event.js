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

// This is for when we first load the page
testMicrodata(false);

function testMicrodata(modal_output=true) {
    let microdata_pass = true;
    const google_microdata_button = document.getElementById('google-microdata-button');
    const google_microdata_modal_output = document.getElementById('google-microdata-modal-output');
    let modal_error_output_html = "";

    const title_input = document.getElementById('title');

    const occurrences_that_do_not_pass = document.querySelectorAll('.occurrence[data-microdata="false"]');

    const contact_name_input = document.getElementById('contact-name');
    const contact_url_input = document.getElementById('contact-website');
    const contact_type_input = document.querySelector('input[name="contact_type"]:checked');

    if (title_input.value == "") {
        if (modal_output) {
            modal_error_output_html += "<li>Missing title</li>";
        }
        microdata_pass = false;
    }

    if (occurrences_that_do_not_pass.length > 0) {
        if (modal_output) {
            modal_error_output_html += "<li>Some event instances do not pass our microdata check</li>";
        }
        microdata_pass = false;
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

const deleteDatetimeElements = document.querySelectorAll('.delete-datetime');
deleteDatetimeElements.forEach((single_elem) => {
    single_elem.addEventListener('submit', (event) => {
        if (!window.confirm('Are you sure you want to delete this instance?')) {
            event.preventDefault();
        }
    });
});

const deleteDatetimeRecurrenceElements = document.querySelectorAll('.delete-datetime-recurrence');
deleteDatetimeRecurrenceElements.forEach((single_elem) => {
    single_elem.addEventListener('submit', (event) => {
        if (!window.confirm('Are you sure you want to delete this occurrence of your recurring instance? The rest of the recurrences will remain.')) {
            event.preventDefault();
        }
    });
});

const editRecurringEdtElements = document.querySelectorAll('.edit-recurring-edt');
editRecurringEdtElements.forEach((single_elem) => {
    single_elem.addEventListener('click', (event) => {
        if (!window.confirm('You are editing a single occurrence of a recurring instance.')) {
            event.preventDefault();
        }
    });
});

const editEventForm = document.getElementById('#edit-event-form');
if (editEventForm !== null) {
    editEventForm.addEventListener('submit', (event) => {
        const title_elem = document.getElementById('title');
        if (title_elem.value === '') {
            window.UNL_Events.notifier.mark_input_invalid(title);
            window.UNL_Events.notifier.alert('Sorry! We couldn\'t edit your event', '<a href=\"#title\">Title</a> is required.');
            event.preventDefault();
        }
    });
}
