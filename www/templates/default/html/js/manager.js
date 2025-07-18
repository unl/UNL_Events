const select_events = document.querySelectorAll('.select-event');
const bulk_action = document.getElementById('bulk-action');
if (bulk_action !== null) {
    bulk_action.addEventListener('change', () => {
        const ids = [];

        // find ids of all events that are checked
        select_events.forEach((single_select_event) => {
            if (single_select_event.checked) {
                ids.push(single_select_event.dataset.id);
            }
        });

        if (ids.length > 0) {
            const bulk_action_ids = document.getElementById('bulk-action-ids');
            const bulk_action_action = document.getElementById('bulk-action-action');

            bulk_action_ids.value = ids.join(',');
            bulk_action_action.value = bulk_action.value;

            let confirm_string = null;
            if (bulk_action.value == 'delete') {
                confirm_string = 'Are you sure you want to delete these ' + ids.length.toString() + ' events?';
            } else if (bulk_action.value == 'move-to-upcoming') {
                confirm_string = 'Are you sure to move these ' + ids.length.toString() + ' events to "Upcoming"?';
            } else if (bulk_action.value == 'move-to-pending') {
                confirm_string = 'Are you sure to move these ' + ids.length.toString() + ' events to "Pending"?';
            } else {
                // do nothing
                return;
            }

            if (window.confirm(confirm_string)) {
                const bulk_action_form = document.getElementById('bulk-action-form');
                bulk_action_form.submit();
            }
        }
    });
}

const checkbox_toggle = document.getElementById('checkbox-toggle');
if (checkbox_toggle !== null) {
    checkbox_toggle.addEventListener('click', () => {
        if (checkbox_toggle.checked) {
            select_events.forEach((single_select_element) => {
                single_select_element.setAttribute('checked', 'checked');
            });
        } else {
            select_events.forEach((single_select_element) => {
                single_select_element.removeAttribute('checked');
            });
        }
    });
}

const recurring_checkbox = document.getElementById('recurring');
const recurring_containers = document.querySelectorAll('.recurring-container');
if (recurring_checkbox !== null) {
    recurring_checkbox.addEventListener('change', () => {
        if (recurring_checkbox.checked) {
            recurring_containers.forEach((single_container) => {
                single_container.classList.remove('dcf-d-none!');
            });
        } else {
            recurring_containers.forEach((single_container) => {
                single_container.classList.add('dcf-d-none!');
            });
        }
    });
    if (recurring_checkbox.checked) {
        recurring_containers.forEach((single_container) => {
            single_container.classList.remove('dcf-d-none!');
        });
    } else {
        recurring_containers.forEach((single_container) => {
            single_container.classList.add('dcf-d-none!');
        });
    }
}

const helper_tools = document.querySelectorAll('.pending-event-tools, .upcoming-event-tools, .past-event-tools, .searched-event-tools');
helper_tools.forEach((single_tool) => {
    single_tool.addEventListener('change', () => {
        const delete_form = document.getElementById(`delete-${single_tool.dataset.id}`);
        const move_form = document.getElementById(`move-${single_tool.dataset.id}`);
        const move_target = document.getElementById(`move-target-${single_tool.dataset.id}`);
        const promote_target = document.getElementById(`promote-target-${single_tool.dataset.id}`);
        const promote_form = document.getElementById(`promote-${single_tool.dataset.id}`);
        if (single_tool.value == 'recommend') {
            // redirect to recommend URL
            window.location = single_tool.dataset.recommendUrl;
        } else if (single_tool.value == 'delete') {
            if (window.confirm('Are you sure you want to delete this event?')) {
                delete_form.submit();
            }
        } else if (single_tool.value == 'move-to-upcoming') {
            move_target.value = 'upcoming';
            move_form.submit();
        } else if (single_tool.value == 'move-to-pending') {
            move_target.value = 'pending';
            move_form.submit();
        } else if (single_tool.value == 'promote') {
            promote_target.value = 'promote';
            promote_form.submit();
        } else if (single_tool.value == 'hide-promo') {
            promote_target.value = 'hide-promo';
            promote_form.submit();
        }
    });
});

const toggle_search = document.getElementById('toggle-search');
if (toggle_search !== null) {
    toggle_search.addEventListener('click', () => {
        const search_form = document.getElementById('search-form');
        if (search_form.classList.contains('dcf-d-none!')) {
            search_form.classList.remove('dcf-d-none!');
        } else {
            search_form.classList.add('dcf-d-none!');
        }
    });
}

const form_inputs = document.querySelectorAll('input, select, textarea');
form_inputs.forEach((single_input) => {
    single_input.addEventListener('change', () => {
        single_input.classList.remove('validation-failed');
    });
    single_input.addEventListener('blur', () => {
        single_input.classList.remove('validation-failed');
    });
});

window.UNL_Events = window.UNL_Events || {};
window.UNL_Events.notifier = {
    mark_input_invalid: function(input_element) {
        input_element.classList.add('validation-failed');
    },
    failure: function(header, message) {
        const notice = document.getElementById('notice');
        const notice_header = notice.querySelector('h2');
        const notice_message = notice.querySelector('.dcf-notice-message');

        notice.classList.remove('dcf-notice-info', 'dcf-notice-success', 'dcf-notice-warning', 'dcf-notice-danger');
        notice.classList.add('dcf-notice-danger');

        notice_header.innerText = header;
        notice_message.innerHTML = message;

        notice.classList.remove('dcf-d-none!');
        notice.scrollIntoView();
    },
    info: function(header, message) {
        const notice = document.getElementById('notice');
        const notice_header = notice.querySelector('h2');
        const notice_message = notice.querySelector('.dcf-notice-message');

        notice.classList.remove('dcf-notice-info', 'dcf-notice-success', 'dcf-notice-warning', 'dcf-notice-danger');
        notice.classList.add('dcf-notice-info');

        notice_header.innerText = header;
        notice_message.innerHTML = message;

        notice.classList.remove('dcf-d-none!');
        notice.scrollIntoView();
    },
    success: function(header, message) {
        const notice = document.getElementById('notice');
        const notice_header = notice.querySelector('h2');
        const notice_message = notice.querySelector('.dcf-notice-message');

        notice.classList.remove('dcf-notice-info', 'dcf-notice-success', 'dcf-notice-warning', 'dcf-notice-danger');
        notice.classList.add('dcf-notice-success');

        notice_header.innerText = header;
        notice_message.innerHTML = message;

        notice.classList.remove('dcf-d-none!');
        notice.scrollIntoView();
    },
    alert: function(header, message) {
        const notice = document.getElementById('notice');
        const notice_header = notice.querySelector('h2');
        const notice_message = notice.querySelector('.dcf-notice-message');

        notice.classList.remove('dcf-notice-info', 'dcf-notice-success', 'dcf-notice-warning', 'dcf-notice-danger');
        notice.classList.add('dcf-notice-warning');

        notice_header.innerText = header;
        notice_message.innerHTML = message;

        notice.classList.remove('dcf-d-none!');
        notice.scrollIntoView();
    }
};