<?php 
$contactInfo = array(); 
foreach (array('listingcontactname', 'listingcontactphone') as $property) {
    if (isset($context->event->$property)) {
        $contactInfo[] = $context->event->$property;
    }
}
if (isset($context->event->listingcontactemail)) {
    $contactInfo[] = '<a href="mailto:' . $context->event->listingcontactemail . '">' . $context->event->listingcontactemail . '</a>';
}
?>
<?php if (!empty($contactInfo)): ?>
<div class="contact">
    <svg class="dcf-h-4 dcf-w-4 dcf-fill-current" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
        <path d="M23.5 3H.5a.5.5 0 00-.5.5v17a.5.5 0 00.5.5h4a.5.5 0 00.5-.5V19c0-.689.561-1.25 1.25-1.25S7.5 18.311 7.5 19v1.5a.5.5 0 00.5.5h8a.5.5 0 00.5-.5V19c0-.689.561-1.25 1.25-1.25S19 18.311 19 19v1.5a.5.5 0 00.5.5h4a.5.5 0 00.5-.5v-17a.5.5 0 00-.5-.5zM23 20h-3v-1c0-1.241-1.009-2.25-2.25-2.25S15.5 17.759 15.5 19v1h-7v-1c0-1.241-1.009-2.25-2.25-2.25S4 17.759 4 19v1H1V4h22v16z"></path>
        <path d="M13 13h8v1h-8zm0-2h8v1h-8zm0-2h8v1h-8zm0-2h4v1h-4zM2.5 15h9a.5.5 0 00.5-.5v-2a.504.504 0 00-.146-.354c-.062-.062-1.43-1.409-3.354-1.619v-.264c.376-.298 1-.986 1-2.263 0-.996 0-2.5-2.5-2.5S4.5 7.004 4.5 8c0 1.277.624 1.965 1 2.263v.264c-1.923.21-3.292 1.557-3.354 1.619A.504.504 0 002 12.5v2a.5.5 0 00.5.5zm.5-2.279c.397-.341 1.563-1.221 3-1.221a.5.5 0 00.5-.5v-1a.51.51 0 00-.27-.443C6.2 9.54 5.5 9.149 5.5 8c0-.998 0-1.5 1.5-1.5s1.5.502 1.5 1.5c0 1.149-.7 1.54-.724 1.553A.5.5 0 007.5 10v1a.5.5 0 00.5.5c1.427 0 2.6.881 3 1.222V14H3v-1.279z"></path>
    </svg>
    <span class="dcf-sr-only">Contact:</span>
    <div>
        <?php echo implode(', ', $contactInfo) ?>
    </div>
</div>
<?php endif; ?>
