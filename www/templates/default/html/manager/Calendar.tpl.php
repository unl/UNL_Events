<?php
    $events = $context->getCategorizedEvents();
?>
<ul class="wdn_tabs">
    <li><a href="#pending">Pending (<?php echo count($events['pending']); ?>)</a></li>
    <li><a href="#upcoming">Upcoming (<?php echo count($events['posted']); ?>)</a></li>
    <li><a href="#past">Past (<?php echo count($events['archived']); ?>)</a></li>
</ul>
<div class="wdn_tabs_content">
    <div id="pending">
        <?php if (count($events['pending']) == 0): ?>
            There are no pending events.
        <?php else: ?>
            <?php echo $savvy->render($events['pending'], 'Events/event-list.tpl.php') ?>
        <?php endif; ?>
    </div>
    <div id="upcoming">
        <?php if (count($events['posted']) == 0): ?>
            There are no upcoming events.
        <?php else: ?>
            <?php echo $savvy->render($events['posted'], 'Events/event-list.tpl.php') ?>
        <?php endif; ?>
    </div>
    <div id="past">
        <?php if (count($events['archived']) == 0): ?>
            There are no past events.
        <?php else: ?>
            <?php echo $savvy->render($events['archived'], 'Events/event-list.tpl.php') ?>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
require(['jquery'], function($) {
    var current_page = 1;
    var total_pages = new Map();
    total_pages.set('pending', parseInt(<?php echo ceil(count($events['pending'])/10); ?>));
    total_pages.set('upcoming', parseInt(<?php echo ceil(count($events['posted'])/10); ?>));
    total_pages.set('past', parseInt(<?php echo ceil(count($events['archived'])/10); ?>));


    var switchToPage = function(tab, new_page) {
        var before_ellipsis_shown = false;
        var after_ellipsis_shown = false;

        // show the appropriate page
        $('#' + tab + ' .event-page[data-page-num=' + current_page + ']').hide();
        $('#' + tab + ' .event-page[data-page-num=' + new_page + ']').show();

        // show just the span with the page number for the new page
        $('#' + tab + '-pagination li[data-page=' + new_page + ']').show();
        $('#' + tab + '-pagination li[data-page=' + new_page + ']').addClass('selected');
        $('#' + tab + '-pagination li[data-page=' + new_page + '] .link').hide();
        $('#' + tab + '-pagination li[data-page=' + new_page + '] .number-text').show();
        $('#' + tab + '-pagination li[data-page=' + new_page + '] .ellipsis').hide();

        // hide and show the appropriate pagination links
        for (var i = 1; i <= total_pages.get(tab); i++) {
            // we will always show 1-3 and total-2 - total
            // we will show 2 to each side of the new page
            if (i == new_page) {
                 // already processed, do nothing
            } else if ((i >= new_page - 2 && i <= new_page - 1) || (i >= new_page + 1 && i <= new_page + 2) ||
                    i <= 3 || i >= total_pages.get(tab) - 2) {
                // this is a shown page link
                $('#' + tab + '-pagination li[data-page=' + i + ']').show();
                $('#' + tab + '-pagination li[data-page=' + i + ']').removeClass('selected');
                $('#' + tab + '-pagination li[data-page=' + i + '] .link').show();
                $('#' + tab + '-pagination li[data-page=' + i + '] .number-text').hide();
                $('#' + tab + '-pagination li[data-page=' + i + '] .ellipsis').hide();
            } else if (i < new_page && !before_ellipsis_shown) {
                // show the ellipsis here
                $('#' + tab + '-pagination li[data-page=' + i + ']').show();
                $('#' + tab + '-pagination li[data-page=' + i + ']').remove('selected');
                $('#' + tab + '-pagination li[data-page=' + i + '] .link').hide();
                $('#' + tab + '-pagination li[data-page=' + i + '] .number-text').hide();
                $('#' + tab + '-pagination li[data-page=' + i + '] .ellipsis').show();
                before_ellipsis_shown = true;
            } else if (i > new_page && !after_ellipsis_shown) {
                // show the ellipsis here
                $('#' + tab + '-pagination li[data-page=' + i + ']').show();
                $('#' + tab + '-pagination li[data-page=' + i + ']').remove('selected');
                $('#' + tab + '-pagination li[data-page=' + i + '] .link').hide();
                $('#' + tab + '-pagination li[data-page=' + i + '] .number-text').hide();
                $('#' + tab + '-pagination li[data-page=' + i + '] .ellipsis').show();
                after_ellipsis_shown = true;
            } else {
                // do not show anything
                // show the ellipsis here
                $('#' + tab + '-pagination li[data-page=' + i + ']').hide();
            }
        }

        current_page = new_page;
    };

    $('.wdn_pagination .link').click(function (click) {
        click.preventDefault();
        var new_page = parseInt($(this).attr('data-page'));
        var tab = $(this).closest('.wdn_pagination').attr('data-tab');

        switchToPage(tab, new_page);
    });

    $('.wdn_pagination .prev a').click(function (click) {
        click.preventDefault();
        var tab = $(this).closest('.wdn_pagination').attr('data-tab');
        var new_page = current_page != 1 ? current_page - 1 : total_pages.get(tab);

        switchToPage(tab, new_page);
    });

    $('.wdn_pagination .next a').click(function (click) {
        click.preventDefault();
        var tab = $(this).closest('.wdn_pagination').attr('data-tab');
        var new_page = current_page != total_pages.get(tab) ? current_page + 1 : 1;

        switchToPage(tab, new_page);
    });

    switchToPage('pending', 1);
    switchToPage('upcoming', 1);
    switchToPage('past', 1);
});
</script>
