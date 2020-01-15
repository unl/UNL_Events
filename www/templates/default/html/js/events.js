require(['jquery', 'wdn'], function($, WDN) {
  "use strict";

  var $progress = $('<progress>'),
    lastLocation = window.location;

  $(function() {
    var homeUrl = $('link[rel=home]'),
      mainScript = $('#script_main'),
      $monthWidget = $('.wp-calendar'),
      $sidebarCal,
      initRoute = 'day',
      widgetDate, nowActive, progressTimeout;

    if (homeUrl.length) {
      homeUrl = homeUrl[0].href;
    } else {
      homeUrl = '/';
    }

    if (mainScript.length) {
      mainScript = WDN.toAbs('./', mainScript[0].src);
    } else {
      mainScript = '/templates/default/html/js/';
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

      widgetDate = new Date($monthWidget.data('datetime'));

      var now = new Date(),
        month = widgetDate.getMonth(),
        year = widgetDate.getFullYear(),
        $dates = $('td', $monthWidget);

      $dates.removeClass('today active');
      if (year === now.getFullYear() && month === now.getMonth()) {
        $dates.not('.prev,.next').each(function() {
          var dateText = $.trim($(this).text());
          if (dateText == now.getDate()) {
            $(this).addClass('today');
            return false;
          }
        });
      }

      if (year == nowActive.getFullYear() && month == nowActive.getMonth()) {
        $dates.not('.prev,.next').each(function() {
          var dateText = $.trim($(this).text());
          if (dateText == nowActive.getDate()) {
            $(this).addClass('active');
            return false;
          }
        });
      };
    }

    function scheduleProgress($loadTo) {
      cancelProgress();
      $progress.hide();
      progressTimeout = setTimeout(function() {
        $progress.prependTo($loadTo);
        $progress.fadeIn(200);
      }, 1000)
    }

    function cancelProgress() {
      clearTimeout(progressTimeout);
    }

    function determineActiveDay() {
      var headingDate = $('h1').data('datetime');
      if (headingDate) {
        nowActive = new Date(headingDate);
      } else {
        nowActive = new Date();
      }
    }

    function loadMonthWidget(datetime) {
      var url, $loadTo = $('aside .calendar');
      if (datetime instanceof Date) {
        url = homeUrl + datetime.getFullYear() + '/' + (datetime.getMonth() + 1) + '/';
      } else {
        url = datetime;
      }

      scheduleProgress($loadTo);
      $.get(url + '?monthwidget&format=hcalendar', function(data) {
        cancelProgress();
        $loadTo.html(data);
        $monthWidget = $('.wp-calendar');
        addMonthWidgetStates();
      });
    }

    function changeDay(datetime) {
      var now = new Date();
      var url, $loadTo = $('#updatecontent');
      if (datetime instanceof Date) {
        url = homeUrl + datetime.getFullYear() + '/' + (datetime.getMonth() + 1) + '/' + datetime.getDate() + '/';
      } else {
        url = datetime;
      }

      // Save the URL in history for the day before we append the hcal format
      pushState(url, 'day');

      // Append the hcal format for ajax loading (partial content)
      var format = 'format=hcalendar';
      if (url.charAt(url.length - 1) === '?') {
        url = url + format;
      } else if (url.indexOf('?') > 0) {
        url = url + '&' + format;
      } else {
        url = url + '?' + format;
      }

      // Get the goods
      scheduleProgress($loadTo);
      $.get(url, function(data) {
        cancelProgress();
        $loadTo.html(data);
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
          $('#events-promo-bar').hide();
        }
      });
    }

    function loadEventInstance(href) {
      var $loadTo = $('#updatecontent');
      pushState(href, 'event');
      scheduleProgress($loadTo);
      $.get(href + '?format=hcalendar', function(data) {
        cancelProgress();
        $loadTo.html(data);
      });
    }

    function loadSearch(href) {
      var $loadTo = $('#updatecontent');
      pushState(href, 'search');
      scheduleProgress($loadTo);
      $.get(href + (href.indexOf('?') == -1 ? '?' : '&') + 'format=hcalendar', function(data) {
        cancelProgress();
        $loadTo.html(data);
      });
    }

    function getOffsetMonth(fromDate, offset) {
      var day = new Date(fromDate);
      day.setMonth(day.getMonth() + offset);
      if (day.getDate() < fromDate.getDate()) {
        day.setDate(0);
      }
      return day;
    }

    $sidebarCal = $('aside .calendar');
    if ($sidebarCal.length) {
      determineActiveDay();
      addMonthWidgetStates();

      $sidebarCal.on('click', 'td a', function(e) {
        e.preventDefault();
        changeDay(this.href);
      });

      $sidebarCal.on('click', 'a.next, a.prev', function(e) {
        e.preventDefault();
        loadMonthWidget(this.href);
      });
    }

    if ($('.view-unl_ucbcn_frontend_eventinstance').length) {
      initRoute = 'event';
    } else if ($('.view-unl_ucbcn_frontend_search').length) {
      initRoute = 'search';
    }

    // Set up arrow navigation
    $(document).on('keyup', function(e) {
      if ($(e.target).is('input, select, textarea, button')) {
        return;
      }

      var $dayNav = $('.day-nav'), day;

      if (!$dayNav.length) {
        return;
      }

      switch (e.which) {
        case 39:
          if (e.altKey) {
            day = getOffsetMonth(nowActive, 1)
          } else {
            day = $('.next', $dayNav).attr('href');
          }
          changeDay(day);
          break;
        case 37:
          if (e.altKey) {
            day = getOffsetMonth(nowActive, -1);
          } else {
            day = $('.prev', $dayNav).attr('href');
        }
        changeDay(day);
        break;
      }
    });

    $('#updatecontent').on('click', '.vevent a.summary', function(e) {
      e.preventDefault();
      loadEventInstance(this.href);
    });

    $(window).on('popstate', function(e) {
      // This fires on navigation back/forward
      var route = (e.originalEvent.state && e.originalEvent.state.route) || initRoute,
        url = window.location.href;

      if (e.originalEvent.state === null) {
        // State will be null on hash change, which is likely an in-page link
        event.preventDefault();
        return false;
      }

      switch (route) {
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
});
