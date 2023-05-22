const filter_form = document.getElementById('filter_form');
const filter_reset = document.getElementById('filter_reset');
const audience_filter = document.getElementById('audience_filter');
const type_filter = document.getElementById('type_filter');

const filter_hidden_q = document.getElementById('filter_hidden_q');
const filter_hidden_audience = document.getElementById('filter_hidden_audience');
const filter_hidden_type = document.getElementById('filter_hidden_type');

audience_filter.addEventListener('ready', () => {
    audience_filter.classList.remove('dcf-d-none');
    const audience_checkboxes = document.querySelectorAll('.audience_filter_checkbox');
    // Submit if select changes
    audience_checkboxes.forEach((single_checkbox) => {
        single_checkbox.addEventListener('input', () => {
            filter_hidden_audience.value = Array.from(audience_checkboxes).filter((checkbox) => checkbox.checked).map((checkbox) => checkbox.value).join(', ');
            cleanInputsAndSubmit();
        });
    });
});

type_filter.addEventListener('ready', () => {
    type_filter.classList.remove('dcf-d-none');
    const type_checkboxes = document.querySelectorAll('.type_filter_checkbox');
    // Submit if select changes
    type_checkboxes.forEach((single_checkbox) => {
        single_checkbox.addEventListener('input', () => {
            filter_hidden_type.value = Array.from(type_checkboxes).filter((checkbox) => checkbox.checked).map((checkbox) => checkbox.value).join(', ');
            cleanInputsAndSubmit();
        });
    });
});

// Set inputs to empty and submit if reset button is clicked
filter_reset.addEventListener('click', (e) => {
    filter_hidden_audience.value = '';
    filter_hidden_type.value = '';

    cleanInputsAndSubmit();
});

function cleanInputsAndSubmit() {
    if (filter_hidden_q.value == "" &&
    filter_hidden_audience.value == "" &&
    filter_hidden_type.value == "") {
        location = location.origin + location.pathname
        return
    }

    if (filter_hidden_q.value == "") {
        filter_hidden_q.remove();
    }
    if (filter_hidden_audience.value == "") {
        filter_hidden_audience.remove();
    }
    if (filter_hidden_type.value == "") {
        filter_hidden_type.remove();
    }

    filter_form.submit();
}