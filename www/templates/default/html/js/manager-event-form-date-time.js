//   _                     _   _               ____        _   _                  
//  | |                   | | (_)             |  _ \      | | | |                 
//  | |     ___   ___ __ _| |_ _  ___  _ __   | |_) |_   _| |_| |_ ___  _ __  ___ 
//  | |    / _ \ / __/ _` | __| |/ _ \| '_ \  |  _ <| | | | __| __/ _ \| '_ \/ __|
//  | |___| (_) | (_| (_| | |_| | (_) | | | | | |_) | |_| | |_| || (_) | | | \__ \
//  |______\___/ \___\__,_|\__|_|\___/|_| |_| |____/ \__,_|\__|\__\___/|_| |_|___/
// Handle Location buttons being clicked
const location_buttons = document.querySelectorAll('.events-btn-location');
location_buttons.forEach((location_button) => {
    location_button.addEventListener('click', () => {
        if (location_button.dataset.add === 'true') {
            location_button.dataset.add = 'false';
            location_button.innerHTML = location_button.innerHTML.replace('Add', 'Remove');
            location_button.classList.remove('dcf-btn-primary');
            location_button.classList.add('dcf-btn-secondary');
    
            document.querySelector(`input[type='hidden'][name='${location_button.dataset.controls}_check']`).value = "1";
    
            const location_template = document.getElementById(`${location_button.dataset.controls}_template`);
            const location_form_group = location_template.content.cloneNode('true');
            document.getElementById(`${location_button.dataset.controls}_container`).append(location_form_group);
    
            setUpLocationListeners(location_button.dataset.controls);
    
        } else {
            location_button.dataset.add = 'true';
            location_button.innerHTML = location_button.innerHTML.replace('Remove', 'Add');
            location_button.classList.remove('dcf-btn-secondary');
            location_button.classList.add('dcf-btn-primary');
    
            document.querySelector(`input[type='hidden'][name='${location_button.dataset.controls}_check']`).value = "0";
    
            document.getElementById(`${location_button.dataset.controls}_container`).innerHTML = "";
        }
    });

    const init_val = document.querySelector(`input[type='hidden'][name='${location_button.dataset.controls}_check']`).value;
    if (init_val == '1') {
        location_button.click();
    }
});

// When new form bits come up we need to set up the event listeners
function setUpLocationListeners(location_id) {
    if (location_id == "physical_location") {
        const location_input = document.getElementById('location');
        const location_fieldset = document.getElementById('new-location-fields');

        location_input.addEventListener('change', () => {
            if (location_input.value == 'new') {
                location_fieldset.style.display = "block";
            } else {
                location_fieldset.style.display = "none";
            }
        });

        if (location_input.value == 'new') {
            location_fieldset.style.display = "block";
        } else {
            location_fieldset.style.display = "none";
        }

        require(['dcf-popup'], (DCFPopup) => {
            // Get all the popups on the dom
            const popups = location_fieldset.querySelectorAll('.dcf-popup');
            
            // Initialize the popup theme
            const popupTheme = new DCFPopup.DCFPopupTheme();
            
            // Initialize the popup with the modified theme
            const popupObj = new DCFPopup.DCFPopup(popups, popupTheme);
            popupObj.initialize();
        });
    } else if (location_id == "virtual_location") {
        const location_input = document.getElementById('v-location');
        const location_fieldset = document.getElementById('new-v-location-fields');

        location_input.addEventListener('change', () => {
            if (location_input.value == 'new') {
                location_fieldset.style.display = "block";
            } else {
                location_fieldset.style.display = "none";
            }
        });

        if (location_input.value == 'new') {
            location_fieldset.style.display = "block";
        } else {
            location_fieldset.style.display = "none";
        }

        require(['dcf-popup'], (DCFPopup) => {
            // Get all the popups on the dom
            const popups = location_fieldset.querySelectorAll('.dcf-popup');
            
            // Initialize the popup theme
            const popupTheme = new DCFPopup.DCFPopupTheme();
            
            // Any changes to the theme would go here
            
            // Initialize the popup with the modified theme
            const popupObj = new DCFPopup.DCFPopup(popups, popupTheme);
            popupObj.initialize();
        });
    }
}


//   _____                           _               _____        _       
//  |  __ \                         (_)             |  __ \      | |      
//  | |__) |___  ___ _   _ _ __ _ __ _ _ __   __ _  | |  | | __ _| |_ ___ 
//  |  _  // _ \/ __| | | | '__| '__| | '_ \ / _` | | |  | |/ _` | __/ _ \
//  | | \ \  __/ (__| |_| | |  | |  | | | | | (_| | | |__| | (_| | ||  __/
//  |_|  \_\___|\___|\__,_|_|  |_|  |_|_| |_|\__, | |_____/ \__,_|\__\___|
//                                            __/ |                       
//                                           |___/                        
const ordinal = function(number) {
    const mod = number % 100;
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
const setRecurringOptions = function(start_elem, month_group_elem, rectypemonth) {
    if (!start_elem.val()) { return; }

    // get startdate info
    const weekdays = [
        "Sunday",
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday"
    ];

    let start_date = new Date(start_elem.val());
    if (!start_date.isValid()) { return; }
    let start_year = start_date.getFullYear();
    let start_month = start_date.getMonth() + 1;
    let start_day = start_date.getDate();
    let start_weekday = weekdays[start_date.getDay()];

    // get week in month
    const nth = {
        1: "First",
        2: "Second",
        3: "Third",
        4: "Fourth",
        5: "Last",
    };

    // get number of days (28, 29, 30, 31) in month
    let days_in_month = 28;
    let d = new Date(start_year, start_month - 1, 28);
    while (days_in_month == d.getDate()) {
        d = new Date(start_year, start_month - 1, ++days_in_month);
    }
    days_in_month--;

    let week = 0; // the week of the start day
    let total_weeks = 0; // total weeks in the month
    for (let i = 1; i <= days_in_month; i++) {
        let d = new Date(start_year, start_month - 1, i);
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

    let dynamicRecurringSelected = '';
    if (nth[week] != undefined) {
        if (rectypemonth == nth[week].toLowerCase()) { dynamicRecurringSelected = ' selected="selected" '}
        month_group_elem.prepend("<option class='dynamicRecurring' " + dynamicRecurringSelected + "value='" + nth[week].toLowerCase() + "'>" + nth[week] + " " + start_weekday + " of every month</option>")
    }

    if (week == 4 && total_weeks == 4) {
        if (rectypemonth == 'last') { dynamicRecurringSelected = ' selected="selected" '}
        month_group_elem.prepend("<option class='dynamicRecurring' " + dynamicRecurringSelected + "value='last'>" + "Last " + start_weekday + " of every month</option>")
    }

    if (days_in_month == start_day) {
        if (rectypemonth == 'lastday') { dynamicRecurringSelected = ' selected="selected" '}
        month_group_elem.prepend("<option class='dynamicRecurring' " + dynamicRecurringSelected + "value='lastday'>Last day of every month</option>");
    }

    let text = ordinal(start_day) + ' of every month';
    if (rectypemonth == 'date') { dynamicRecurringSelected = ' selected="selected" '}
    month_group_elem.prepend("<option class='dynamicRecurring' " + dynamicRecurringSelected + "value='date'>" + text + "</option>");
};

require(['jquery', 'wdn'], function ($, WDN) {

    // DCF Date Picker
    WDN.initializePlugin('datepickers');

    $('#recurring').change(function () {
        if (this.checked) {
            setRecurringOptions($('#start-date'), $('#monthly-group'), recurringType);
        }
    });

    if (recurringMonth == '') {
        $('#start-date').change(function () {
            setRecurringOptions($(this), $('#monthly-group'), recurringType);
        });
        setRecurringOptions($('#start-date'), $('#monthly-group'));
        $('#recurring-type').val(recurringType);
    } else {
        $('#start-date').change(function () {
            setRecurringOptions($(this), $('#monthly-group'), recurringMonth);
        });
        setRecurringOptions($('#start-date'), $('#monthly-group'), recurringMonth);
    }
});