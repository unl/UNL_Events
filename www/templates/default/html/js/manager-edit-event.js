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
        google_microdata_button.innerHTML = "! Your event does not reach Google microdata requirements !";

        google_microdata_button.classList.add('unl-bg-blue');
        google_microdata_button.classList.remove('unl-bg-green');

        google_microdata_button.classList.add('events-b-blue');
        google_microdata_button.classList.remove('events-b-green');
    }else{
        if (modal_output) {
            google_microdata_modal_output.innerHTML = "<span class='dcf-bold'>Your event is fulfilling all Google's requirements</span>";
        }
        google_microdata_button.innerHTML = "Your event does reach google microdata requirements";

        google_microdata_button.classList.remove('unl-bg-blue');
        google_microdata_button.classList.add('unl-bg-green');

        google_microdata_button.classList.remove('events-b-blue');
        google_microdata_button.classList.add('events-b-green');
    }
}
