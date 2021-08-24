<?php
use UNL\UCBCN\Manager\Auth;
$auth = new Auth();
if ($auth->isAuthenticated() && $context->event->userCanEdit()) { ?>
<a class="dcf-mt-4 dcf-btn dcf-btn-primary" href="<?php echo $context->event->getEditURL(); ?>">Edit Event</a>
<?php } ?>
