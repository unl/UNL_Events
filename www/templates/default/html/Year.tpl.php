<?php 
$lastYear = $context->getRawObject()->getDateTime()->modify('-1 year');
$nextYear = $context->getRawObject()->getDateTime()->modify('+1 year');
?>
<div class="year-nav">
    <a href="<?php echo UNL\UCBCN\Frontend\Year::generateURL($context->getRaw('calendar'), $lastYear) ?>"><span class="eventicon-angle-circled-left" aria-hidden="true"></span><?php echo $lastYear->format('Y') ?></a>
    <h1 class="year_main"><?php echo $context->year; ?></h1>
    <a href="<?php echo UNL\UCBCN\Frontend\Year::generateURL($context->getRaw('calendar'), $nextYear) ?>"><?php echo $nextYear->format('Y') ?><span class="eventicon-angle-circled-right" aria-hidden="true"></span></a>
</div>
<div class="year_cal dcf-grid-halves@sm dcf-grid-thirds@md">
<?php foreach ($context->getRaw('monthwidgets') as $widget): ?>
<div class="dcf-col-100%">
    <?php echo $savvy->render($widget) ?>
</div>
<?php endforeach; ?>
</div>
