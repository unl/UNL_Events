<div class="wdn-grid-set">
    <section class="bp2-wdn-col-one-third">
        <h1 class="results clear-top">
            <?php
            if ($dt = $context->getSearchTimestamp()) {
                echo '<span class="wdn-subhead">'.$context->count().' search results for events dated </span><a class="permalink" href="'.$context->getURL().'">'.date('F jS',$dt).'</a>';
            } else {
                echo '<span class="wdn-subhead">'.$context->count().' search results for </span><a class="permalink" href="'.$context->getURL().'">'.htmlentities($context->search_query).'</a>';
            }
            ?>
        </h1>
        <div id="subscribe">
            <span>Subscribe to this search</span>
            <ul id="droplist">
                <li id="eventrss"><a href="<?php echo $context->getURL()?>&amp;format=rss"><span class="eventicon-rss" aria-hidden="true"></span>RSS</a></li>
                <li id="eventical"><a href="<?php echo $context->getURL()?>&amp;format=ics"><span class="wdn-icon-calendar" aria-hidden="true"></span>ICS</a></li>
            </ul>
        </div>
    </section>
    <section id="updatecontent" class="day_cal bp2-wdn-col-two-thirds">
        <?php echo $savvy->render($context, 'hcalendar/Search.tpl.php'); ?>
    </section>
</div>
