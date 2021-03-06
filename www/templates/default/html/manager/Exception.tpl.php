<?php
/* @var $context Exception */
if (false == headers_sent()
    && $code = $context->getCode()) {
    header('HTTP/1.1 '.$code.' '.$context->getMessage());
    header('Status: '.$code.' '.$context->getMessage());
}

$savvy->setReplacementData('pagetitle', 'Sorry, an error occurred.');
$savvy->setReplacementData('sitetitle', 'Sorry, an error occurred');
$page->addScriptDeclaration("WDN.initializePlugin('notice');");
?>

<div class="wdn_notice alert">
    <div class="close">
        <a href="#" title="Close this notice">Close this notice</a>
    </div>
    <div class="message">
        <h4>Whoops! Sorry, there was an error:</h4>
        <p><?php echo $context->getMessage(); ?></p>
    </div>
    <!-- <?php echo $context; ?> -->
</div>
