<?php
$lastYear = $context->getRawObject()->getDateTime()->modify('-1 year');
$nextYear = $context->getRawObject()->getDateTime()->modify('+1 year');
?>
<div class="year-nav dcf-d-flex dcf-flex-nowrap dcf-ai-center dcf-jc-between dcf-pb-5 unl-font-sans">
    <a class="unl-prerender dcf-d-flex dcf-ai-center dcf-txt-sm dcf-txt-decor-hover" href="<?php echo UNL\UCBCN\Frontend\Year::generateURL($context->getRaw('calendar'), $lastYear) ?>"><svg class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-fill-current" focusable="false" width="24" height="24" viewBox="0 0 24 24"><path d="M12 .004c-6.617 0-12 5.383-12 12s5.383 12 12 12 12-5.383 12-12-5.384-12-12-12zm3 8.5a.5.5 0 01-.193.395l-3.993 3.105 3.993 3.106c.122.094.193.24.193.394v4a.5.5 0 01-.82.384l-9-7.5a.499.499 0 010-.768l9-7.5a.5.5 0 01.82.384v4z"></path></svg><?php echo $lastYear->format('Y') ?></a>
    <h2 class="year_main dcf-mb-0"><?php echo $context->year; ?></h2>
    <a class="unl-prerender dcf-d-flex dcf-ai-center dcf-txt-sm dcf-txt-decor-hover" href="<?php echo UNL\UCBCN\Frontend\Year::generateURL($context->getRaw('calendar'), $nextYear) ?>"><?php echo $nextYear->format('Y') ?><svg class="dcf-ml-1 dcf-h-4 dcf-w-4 dcf-fill-current" focusable="false" width="24" height="24" viewBox="0 0 24 24"><path d="M12 .004c-6.617 0-12 5.383-12 12s5.383 12 12 12 12-5.383 12-12-5.384-12-12-12zm6.82 12.384l-8.999 7.5a.498.498 0 01-.532.069.5.5 0 01-.289-.453v-4c0-.154.071-.3.193-.394l3.992-3.106-3.992-3.106A.497.497 0 019 8.504v-4a.5.5 0 01.82-.384l8.999 7.5a.499.499 0 01.001.768z"></path></svg></a>
</div>
<div class="year_cal dcf-d-grid dcf-grid-cols-1 dcf-grid-cols-2@sm dcf-grid-cols-3@md dcf-col-gap-vw dcf-row-gap-6">
<?php foreach ($context->getRaw('monthwidgets') as $widget): ?>
<div>
    <?php echo $savvy->render($widget) ?>
</div>
<?php endforeach; ?>
</div>
