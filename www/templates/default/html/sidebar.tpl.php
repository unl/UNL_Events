<div class="events-sidebar">
    <div class="calendar">
        <?php echo $savvy->render($context->getMonthWidget()); ?>
    </div>
    <div class="dcf-d-flex dcf-flex-wrap dcf-ai-baseline dcf-jc-between dcf-mt-5 dcf-pt-2 dcf-bt-1 dcf-bt-solid unl-bt-light-gray unl-font-sans" id="subscribe">
        <h2 class="dcf-mb-0 dcf-mr-6 dcf-flex-shrink-0 dcf-txt-xs dcf-lh-3 dcf-regular dcf-uppercase unl-dark-gray title" id="heading-subscribe">Subscribe to this calendar</h2>
        <ul class="dcf-mb-0 dcf-flex-grow-1 dcf-list-bare dcf-list-inline" id="droplist" role="list" aria-labelledby="heading-subscribe">
            <li class="dcf-mb-0 dcf-mr-2" id="eventrss">
                <a class="dcf-d-flex dcf-flex-no-wrap dcf-ai-center dcf-txt-decor-hover" href="<?php echo $frontend->getUpcomingURL(); ?>?format=rss&amp;limit=100"><svg class="dcf-mr-1 dcf-h-3 dcf-w-3 dcf-fill-current" focusable="false" width="24" height="24" viewBox="0 0 24 24"><path d="M.012 8.5v2c7.289 0 13 5.931 13 13.5h2c0-8.691-6.59-15.5-15-15.5z"></path><path d="M.012 0v2c12.336 0 22 9.664 22 22h2c0-13.458-10.543-24-24-24z"></path><circle cx="3.012" cy="21" r="3"></circle></svg>RSS</a>
            </li>
            <li class="dcf-mb-0" id="eventical">
                <a class="dcf-d-flex dcf-flex-no-wrap dcf-ai-center dcf-txt-decor-hover" href="<?php echo $frontend->getWebcalUpcomingURL(); ?>?format=ics&amp;limit=-1"><svg class="dcf-mr-1 dcf-h-3 dcf-w-3 dcf-fill-current" focusable="false" width="24" height="24" viewBox="0 0 24 24"><path d="M23.5 2H20V.5a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5V2H8V.5a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5V2H.5a.5.5 0 00-.5.5V7h24V2.5a.5.5 0 00-.5-.5zM7 4H5V1h2v3zm12 0h-2V1h2v3zM0 23.5a.5.5 0 00.5.5h23a.5.5 0 00.5-.5V8H0v15.5zM7 15h4v-4a1 1 0 012 0v4h4a1 1 0 010 2h-4v4a1 1 0 01-2 0v-4H7a1 1 0 010-2z"></path></svg>ICS</a>
            </li>
        </ul>
    </div>
</div>
