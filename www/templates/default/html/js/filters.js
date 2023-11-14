// Set up values
const filter_form = document.getElementById('filter_form');
const filter_reset = document.getElementById('filter_reset');
const audience_filter = document.getElementById('audience_filter');
const type_filter = document.getElementById('type_filter');

// Set up values for hidden inputs
const filter_hidden_q = document.getElementById('filter_hidden_q');
const filter_hidden_audience = document.getElementById('filter_hidden_audience');
const filter_hidden_type = document.getElementById('filter_hidden_type');

// Amount of time to sleep before fetching new events
// This helps user experience since they can ready and see the loading info
// If we did not have this people might think they are missing something
let filter_fetch_sleep_time = 500;

// Once the collapsible fieldset loads set up event listeners
audience_filter.addEventListener('ready', () => {
    // Get checkboxes and if they change/input try to submit the change
    const audience_checkboxes = document.querySelectorAll('.audience_filter_checkbox');
    audience_checkboxes.forEach((single_checkbox) => {
        single_checkbox.addEventListener('input', () => {
            // Filter out any unchecked checkboxes, get there value, and join them with comma delimiter
            filter_hidden_audience.value = Array.from(audience_checkboxes)
                .filter((checkbox) => checkbox.checked)
                .map((checkbox) => checkbox.value)
                .join(', ');
            cleanInputsAndSubmit().catch((err) => console.error(err));
        });
    });
});

// Once the collapsible fieldset loads set up event listeners
type_filter.addEventListener('ready', () => {
    // Get checkboxes and if they change/input try to submit the change
    const type_checkboxes = document.querySelectorAll('.type_filter_checkbox');
    type_checkboxes.forEach((single_checkbox) => {
        single_checkbox.addEventListener('input', () => {
            // Filter out any unchecked checkboxes, get there value, and join them with comma delimiter
            filter_hidden_type.value = Array.from(type_checkboxes)
                .filter((checkbox) => checkbox.checked)
                .map((checkbox) => checkbox.value)
                .join(', ');
            cleanInputsAndSubmit().catch((err) => console.error(err));
        });
    });
});

// Set inputs to empty and submit if reset button is clicked
filter_reset.addEventListener('click', (e) => {
    document.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
        checkbox.checked = false;
    });

    cleanInputsAndSubmit().catch((err) => console.error(err));
});


/**
 * This is the order of operations when it comes to updating the list of events
 * - This will set loading
 * - Then creates a new state and replaces current one
 * - Then Fetches new events
 * - Finally updates dom
 * - On error it will set error
 *
 * @async
 * @returns {Promise<void>}
 */
async function cleanInputsAndSubmit() {
    // Might be worth doing some sort of fall back to regular form submit on error
    try {
        set_loading();
        await sleep(filter_fetch_sleep_time);
        const new_state = get_new_state();
        const new_dom = await fetch_new_events(new_state);
        update_events(new_dom);
    } catch (err) {
        console.error('Error submitting', err);
        set_error();
    }
}

/**
 * Uses inputs and formulates a new state to be saved using History API
 *
 * @returns {{q: string, type: string, audience: string }}
 */
function get_new_state() {
    // Filter out any unchecked checkboxes, get there value, and join them with comma delimiter
    const type_checkboxes = document.querySelectorAll('.type_filter_checkbox');
    const type_to_string = Array.from(type_checkboxes)
        .filter((checkbox) => checkbox.checked)
        .map((checkbox) => checkbox.value)
        .join(', ');

    // Filter out any unchecked checkboxes, get there value, and join them with comma delimiter
    const audience_checkboxes = document.querySelectorAll('.audience_filter_checkbox');
    const audience_to_string = filter_hidden_audience.value = Array.from(audience_checkboxes)
        .filter((checkbox) => checkbox.checked)
        .map((checkbox) => checkbox.value)
        .join(', ');

    const query_to_string = filter_hidden_q.value;

    const new_state = {
        "q": query_to_string,
        "type": type_to_string,
        "audience": audience_to_string,
    }

    return new_state;
}

/**
 * Fetches new partial and create a dom from it
 *
 * @async
 * @param {{q: string, type: string, audience: string }} state - state to be saved
 * @returns {Promise<Document>} dom of partial from fetch request
 * @throws Will throw error on not OK responses
 */
async function fetch_new_events(state) {
    // Creates new URL object and adds search params if they are present
    let partial_url = new URL(location.origin + location.pathname);
    if ("q" in state && state.q !== "") {
        partial_url.searchParams.append('q', state.q);
    }
    if ("audience" in state && state.audience !== "") {
        partial_url.searchParams.append('audience', state.audience);
    }
    if ("type" in state && state.type !== "") {
        partial_url.searchParams.append('type', state.type);
    }

    // We do not want to push since it would lead to a lot of unnecessary stuff in browser history
    // It also does not really matter since it is pretty easy to recreate the filters
    history.replaceState(state, "", partial_url.toString());

    // Adds partial since we do not want that in the history
    partial_url.searchParams.append('format', 'partial');

    // Fetches partial and throws error if not ok
    const response = await fetch(partial_url.toString());
    if (!response.ok) {
        throw new Error('Error getting new events');
    }

    // Parses dom from response text and returns it
    const response_text = await response.text();
    const response_dom = new DOMParser().parseFromString(response_text, 'text/html');
    return response_dom;
}

/**
 * Uses document and gets content to update, it will then replace current content
 *
 * @param {Document} updated_dom
 * @return {void}
 */
function update_events(updated_dom) {
    const new_data = updated_dom.getElementById('updatecontent');
    if (new_data == null) {
        throw new Error('Missing updated content section');
    }

    document.getElementById('updatecontent').innerHTML = new_data.innerHTML;
}

/**
 * Uses the loadingContent template on the page
 *
 * @returns {void}
 */
function set_loading() {
    const loading_template = document.getElementById('loadingContent');
    if (loading_template == null) {
        throw new Error('Missing Loading Content Template');
    }
    document.getElementById('updatecontent').innerHTML = loading_template.innerHTML;
}

/**
 * Uses the errorContent template on the page
 *
 * @returns {void}
 */
function set_error() {
    const error_template = document.getElementById('errorContent');
    if (error_template == null) {
        throw new Error('Missing Error Content Template');
    }
    document.getElementById('updatecontent').innerHTML = error_template.innerHTML;
}

/**
 * Sleeps for any amount of time
 * - Best to use as `await sleep(50);`
 * 
 * @param {number} ms Number of milliseconds to sleep 
 * @returns {Promise<void>}
 */
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

WDN.initializePlugin('collapsible-fieldsets');