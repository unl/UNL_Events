const progress = document.createElement('progress');
let lastLocation = window.location;

document.addEventListener('DOMContentLoaded', () => {
    let homeUrl = document.querySelector('link[rel=home]');
    let monthWidget = document.querySelector('.wp-calendar');
    let sidebarCal = null;
    let initRoute = 'day';
    let widgetDate = null;
    let nowActive = null;
    let progressTimeout = null;

    let heading_date_element = document.getElementById('heading-date');

    if (homeUrl) {
        homeUrl = homeUrl.href;
    } else {
        homeUrl = '/';
    }

    function pushState(url, route) {

        if (!window.history.pushState) {
        return;
        }

        if (window.location.href === url) {
        return;
        }

        window.history.pushState({route: route}, '', url);
    }

    function addMonthWidgetStates() {

        widgetDate = new Date(monthWidget.dataset.datetime);

        var now = new Date(),
        month = widgetDate.getMonth(),
        year = widgetDate.getFullYear(),
        dates = monthWidget.querySelectorAll('td');

        dates.forEach((date_element) => {
            date_element.classList.remove('today', 'active');
        });
        if (year === now.getFullYear() && month === now.getMonth()) {
            dates.forEach((date_element) => {
                if (date_element.classList.contains('prev') || date_element.classList.contains('next')) { return; }
                const dateText = date_element.innerText.trim();
                if (dateText == now.getDate()) {
                    date_element.classList.add('today');
                }
            });
        }

        if (year == nowActive.getFullYear() && month == nowActive.getMonth()) {
            dates.forEach((date_element) => {
                if (date_element.classList.contains('prev') || date_element.classList.contains('next')) { return; }
                const dateText = date_element.innerText.trim();
                if (dateText == nowActive.getDate()) {
                    date_element.classList.add('active');
                }
            });
        };
    }

    function scheduleProgress(loadTo) {
        cancelProgress();
        progress.classList.remove('dcf-d-none!');
        progressTimeout = setTimeout(function() {
            loadTo.prepend(progress);
            progress.classList.remove('dcf-d-none!');
        }, 1000)
    }

    function cancelProgress() {
        clearTimeout(progressTimeout);
    }

    function determineActiveDay() {
        heading_date_element = document.getElementById('heading-date');
        if (heading_date_element !== null) {
            const headingDate = heading_date_element.dataset.datetime;
            nowActive = new Date(headingDate);
        } else {
            nowActive = new Date();
        }
    }

    function loadMonthWidget(datetime) {
        let url = null;
        const loadTo = document.querySelector('aside .calendar');
        if (datetime instanceof Date) {
            url = homeUrl + datetime.getFullYear() + '/' + (datetime.getMonth() + 1) + '/';
        } else {
            url = datetime;
        }

        scheduleProgress(loadTo);
        fetch(`${url}?monthwidget&format=hcalendar`).then((response) => {
            return response.text();
        }).then((text) => {
            cancelProgress();
            loadTo.innerHTML = text;
            monthWidget = document.querySelector('.wp-calendar');
            addMonthWidgetStates();
        });
    }

    function changeDay(datetime) {
        const now = new Date();
        let url = null;
        const loadTo = document.getElementById('updatecontent');
        if (datetime instanceof Date) {
            url = homeUrl + datetime.getFullYear() + '/' + (datetime.getMonth() + 1) + '/' + datetime.getDate() + '/';
        } else {
            url = datetime;
        }

        // Save the URL in history for the day before we append the hcal format
        pushState(url, 'day');

        // Append the hcal format for ajax loading (partial content)
        const format = 'format=hcalendar';
        if (url.charAt(url.length - 1) === '?') {
            url = url + format;
        } else if (url.indexOf('?') > 0) {
            url = url + '&' + format;
        } else {
            url = url + '?' + format;
        }

        // Get the goods
        scheduleProgress(loadTo);
        fetch(url).then((response) => {
            return response.text();
        }).then((text) => {
            cancelProgress();
            loadTo.innerHTML = text;
            determineActiveDay();
            if (nowActive.getFullYear() == now.getFullYear() && nowActive.getMonth() == now.getMonth() && nowActive.getDate() == now.getDate()) {
                window.location = window.location;
            }
            if (widgetDate.getFullYear() !== nowActive.getFullYear() || widgetDate.getMonth() !== nowActive.getMonth()) {
                loadMonthWidget(nowActive);
            } else {
                addMonthWidgetStates();
            }

            if (nowActive.getFullYear() != now.getFullYear() && nowActive.getMonth() != now.getMonth() && nowActive.getDate() != now.getDate()) {
                document.getElementById('events-promo-bar').classList.add('dcf-d-none!');
            }
        });
    }

    function loadEventInstance(href) {
        const loadTo = document.getElementById('updatecontent');
        pushState(href, 'event');
        scheduleProgress(loadTo);
        fetch(`${href}?format=hcalendar`).then((response) => {
            return response.text();
        }).then((text) => {
            cancelProgress();
            loadTo.innerHTML = text;
        });
    }

    function loadSearch(href) {
        const loadTo = document.getElementById('updatecontent');
        pushState(href, 'search');
        scheduleProgress(loadTo);
        fetch(href + (href.indexOf('?') == -1 ? '?' : '&') + 'format=hcalendar').then((response) => {
            return response.text();
        }).then((text) => {
            cancelProgress();
            loadTo.innerHTML = text;
        });
    }

    function getOffsetMonth(fromDate, offset) {
        const day = new Date(fromDate);
        day.setMonth(day.getMonth() + offset);
        if (day.getDate() < fromDate.getDate()) {
            day.setDate(0);
        }
        return day;
    }

    sidebarCal = document.querySelector('aside .calendar');
    if (sidebarCal !== null) {
        determineActiveDay();
        addMonthWidgetStates();


        sidebarCal.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (!link) return;

            if (link.closest('td')) {
                e.preventDefault();
                changeDay(link.href);
            } else if (link.classList.contains('next') || link.classList.contains('prev')) {
                e.preventDefault();
                loadMonthWidget(link.href);
            }
        }, true);
    }

    const eventInstance = document.querySelector('.view-unl_ucbcn_frontend_eventinstance');
    const searchInstance = document.querySelector('.view-unl_ucbcn_frontend_search');
    if (eventInstance !== null) {
        initRoute = 'event';
    } else if (searchInstance !== null) {
        initRoute = 'search';
    }

    // Set up arrow navigation
    document.addEventListener('keyup', (e) => {
        if (['INPUT', 'SELECT', 'TEXTAREA', 'BUTTON'].includes(e.target.tagName)) {
            return;
        }

        const dayNav = document.querySelector('.day-nav');
        if (dayNav === null) {
            return;
        }

        let day = null;
        switch (e.code) {
        case 'ArrowRight':
            day = dayNav.querySelector('.next').href;
            if (e.altKey) {
                day = getOffsetMonth(nowActive, 1)
            }
            changeDay(day);
            break;
        case 'ArrowLeft':
            day = dayNav.querySelector('.prev').href;
            if (e.altKey) {
                day = getOffsetMonth(nowActive, -1);
            }
            changeDay(day);
            break;
        }
    });

    const updatecontent = document.getElementById('updatecontent');
    if (updatecontent !== null) {
        updatecontent.addEventListener('click', (e) => {
            const link = e.target.closest('a.summary');
            if (link !== null && link.closest('.vevent')) {
                e.preventDefault();
                loadEventInstance(link.href);
            };
        }, true);
    }

    window.addEventListener('popstate', (e) => {
        const state = e.state;
        const url = window.location.href;

        if (!state) {
            e.preventDefault();
            return;
        }

        switch (state.route || initRoute) {
        case 'event':
            loadEventInstance(url);
            break;
        case 'day':
            changeDay(url);
            break;
        case 'search':
            loadSearch(url);
            break;
        }
    });
});
