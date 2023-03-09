require(['jquery', 'wdn', frontend_url + 'templates/default/html/js/vendor/select2/js/select2.min.js'], function($, WDN) {
    $(document).ready(function() {
        $('form').on('change blur', 'input', function() {
            $(this).removeClass('validation-failed');
        });

        $(".use-select2").select2();

        $('#bulk-action').change(function () {
            var ids = [];

            // find ids of all events that are checked
            $('.select-event').each(function() {
                if ($(this).is(':checked')) {
                    ids.push(parseInt($(this).attr('data-id')));
                }
            });

            if (ids.length > 0) {
                $('#bulk-action-ids').val(ids.join(','));
                $('#bulk-action-action').val($(this).val());

                var confirm_string = null;
                if ($(this).val() == 'delete') {
                    confirm_string = 'Are you sure you want to delete these ' + ids.length.toString() + ' events?';
                } else if ($(this).val() == 'move-to-upcoming') {
                    confirm_string = 'Are you sure to move these ' + ids.length.toString() + ' events to "Upcoming"?';
                } else if ($(this).val() == 'move-to-pending') {
                    confirm_string = 'Are you sure to move these ' + ids.length.toString() + ' events to "Pending"?';
                } else {
                    // do nothing
                    return;
                }

                if (window.confirm(confirm_string)) {
                    $('#bulk-action-form').submit();
                }
            }
        });

        $('#checkbox-toggle').click(function (click) {
            $(".select-event").prop("checked", $(this).is(":checked"));
        });

        if ($('#recurring').is(":checked")) {
            $(".recurring-container").show();
        }

        $('#recurring').click(function (click) {
            if ($(this).is(":checked")) {
                $(".recurring-container").show();
            } else {
                $(".recurring-container").hide();
            }
        });

        $('.pending-event-tools, .upcoming-event-tools, .past-event-tools, .searched-event-tools').change(function () {
            if ($(this).val() == 'recommend') {
                // redirect to recommend URL
                window.location = $(this).attr('data-recommend-url');
            } else if ($(this).val() == 'delete') {
                if (window.confirm('Are you sure you want to delete this event?')) {
                    $('#delete-' + $(this).attr('data-id')).submit();
                }
            } else if ($(this).val() == 'move-to-upcoming') {
                $('#move-target-' + $(this).attr('data-id')).val('upcoming');
                $('#move-' + $(this).attr('data-id')).submit();
            } else if ($(this).val() == 'move-to-pending') {
                $('#move-target-' + $(this).attr('data-id')).val('pending');
                $('#move-' + $(this).attr('data-id')).submit();
            } else if ($(this).val() == 'promote') {
                $('#promote-target-' + $(this).attr('data-id')).val('promote');
                $('#promote-' + $(this).attr('data-id')).submit();
            } else if ($(this).val() == 'hide-promo') {
                $('#promote-target-' + $(this).attr('data-id')).val('hide-promo');
                $('#promote-' + $(this).attr('data-id')).submit();
            }
        });

        $('#toggle-search').click(function (click) {
            click.preventDefault();
            $('#search-form').slideToggle(400, function() {
                if ($('#search-form').is(':visible')) {
                    $('#search-form input').focus();
                }
            });
        });

        notifier = {
            mark_input_invalid: function(input) {
                input.addClass('validation-failed');
            },
            failure: function(header, message) {
                notifier.check(header, message, 'dcf-notice-danger');
                $('#notice').removeClass('dcf-notice-info').removeClass('dcf-notice-success').removeClass('dcf-notice-warning').addClass('dcf-notice-danger');
                $('#notice h2').text(header);
                $('#notice .dcf-notice-message').html(message);
                $('#notice').fadeIn();

                window.scrollTo(0,0);
            },
            info: function(header, message) {
                notifier.check(header, message, 'dcf-notice-info');
                $('#notice').addClass('dcf-notice-info').removeClass('dcf-notice-success').removeClass('dcf-notice-warning').removeClass('dcf-notice-danger');
                $('#notice h2').text(header);
                $('#notice .dcf-notice-message').html(message);
                $('#notice').fadeIn();

                window.scrollTo(0,0);
            },
            success: function(header, message) {
                notifier.check(header, message, 'dcf-notice-success');
                $('#notice').removeClass('dcf-notice-info').addClass('dcf-notice-success').removeClass('dcf-notice-warning').removeClass('dcf-notice-danger');
                $('#notice h2').text(header);
                $('#notice .dcf-notice-message').html(message);
                $('#notice').fadeIn();

                window.scrollTo(0,0);
            },
            alert: function(header, message) {
                notifier.check(header, message, 'dcf-notice-warning');
                $('#notice').removeClass('dcf-notice-info').removeClass('dcf-notice-success').addClass('dcf-notice-warning').removeClass('dcf-notice-danger');
                $('#notice h2').text(header);
                $('#notice .dcf-notice-message').html(message);
                $('#notice').fadeIn();

                window.scrollTo(0,0);
            },
            check: function(header, message, type) {
                var noticeContainer = document.getElementById('noticeContainer');
                var notice = document.getElementById('notice');
                if (noticeContainer && !notice) {
                    require(['dcf-notice'], function(DCFNoticeModule) {
                        var dummyNotice = new DCFNoticeModule.DCFNotice();
                        notice = dummyNotice.appendNotice(noticeContainer, header, message, type);
                        notice.setAttribute('id', 'notice');
                    });
                }
            }
        };
    });
});
