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

<div class="dcf-notice dcf-notice-warning dcf-d-none">
    <h2>Whoops! Sorry, there was an error:</h2>
    <p><?php echo $context->getMessage(); ?></p>
</div>
