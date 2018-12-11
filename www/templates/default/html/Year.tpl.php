<?php 
$lastYear = $context->getRawObject()->getDateTime()->modify('-1 year');
$nextYear = $context->getRawObject()->getDateTime()->modify('+1 year');
?>
<div class="year-nav">
    <a href="<?php echo UNL\UCBCN\Frontend\Year::generateURL($context->getRaw('calendar'), $lastYear) ?>"><span class="eventicon-angle-circled-left" aria-hidden="true"></span><?php echo $lastYear->format('Y') ?></a>
    <h2 class="year_main dcf-d-inline-block"><?php echo $context->year; ?></h2>
    <a href="<?php echo UNL\UCBCN\Frontend\Year::generateURL($context->getRaw('calendar'), $nextYear) ?>"><?php echo $nextYear->format('Y') ?><span class="eventicon-angle-circled-right" aria-hidden="true"></span></a>
</div>
<div class="year_cal dcf-grid-halves@sm dcf-grid-thirds@md dcf-col-gap-3">
<?php foreach ($context->getRaw('monthwidgets') as $widget): ?>
<div>
    <?php echo $savvy->render($widget) ?>
</div>
<?php endforeach; ?>
</div>
