<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        "Edit User Info" => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h1>Edit User Info</h1>
<div class="dcf-grid-halves@md dcf-col-gap-vw dcf-row-gap-3">
    <div>
        <p>
            This API Token is to be used in conjunction with the Events API.
            This token will allow programs to authenticate as you, so do not make this token publicly available.
            For more information please read through the
            <a href="https://wdn.unl.edu/documentation/api-library/events-api-v2-documentation">
                Events API v2 Documentation</a>.
        </p>
    </div>
    <div>
        <form class="dcf-form" action="" method="POST">
            <input 
                type="hidden" 
                name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" 
                value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" 
            />
            <input 
                type="hidden" 
                name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" 
                value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>"
            >
            <fieldset>
                <legend>API Token Generator</legend>
                <?php if (isset($context->user->token) && !empty($context->user->token)): ?>
                <div class="dcf-form-group">
                    <label for="token">Current API Token</label>
                    <div>
                        <input 
                            readonly="readonly" 
                            type="text" 
                            id="token" 
                            name="token" 
                            value="<?php echo $context->user->token; ?>" 
                        />
                        <button class="dcf-btn dcf-btn-secondary dcf-d-none" id="copyToken" type="button">
                            <svg 
                                xmlns="http://www.w3.org/2000/svg" 
                                class="dcf-h-5 dcf-w-5 dcf-fill-current" 
                                focusable="false" 
                                width="24" 
                                height="24" 
                                viewBox="0 0 24 24" 
                                aria-labelledby="filled-copy-1-basic-title"
                            >
                                <title id="filled-copy-1-basic-title">Copy Token</title>
                                <path 
                                    d="M5.5,22C5.224,22,5,21.776,5,21.5V3H3.5C3.224,3,3,3.224,3,3.5v20C3,23.776,
                                    3.224,24,3.5,24h14c0.276,0,0.5-0.224,0.5-0.5 V22H5.5z"
                                ></path>
                                <path 
                                    d="M21,6.5c0-0.133-0.053-0.26-0.146-0.353l-6-6C14.76,0.053,14.632,0,14.5,
                                    0h-8C6.224,0,6,0.224,6,0.5v20 C6,20.776,6.224,21,6.5,21h14c0.276,0,
                                    0.5-0.224,0.5-0.5V6.5z M14,7V1l6,6H14z"
                                ></path>
                                <g>
                                    <path fill="none" d="M0 0H24V24H0z"></path>
                                </g>
                            </svg>
                        </button>
                    </div>
                </div>
                <?php else: ?>
                    <p>You do not have a API Token generated yet.</p>
                <?php endif; ?>
                <input class="dcf-d-none" type="text" name="generate_api_token" value="true">
                <button class="dcf-btn dcf-btn-primary" type="submit">Generate new API Token</button>
            </fieldset>
        </form>
    </div>
</div>

<script>
    const token = document.getElementById('token');
    const copyToken = document.getElementById('copyToken');
    const tokenValue = token.value;

    token.value = token.value.slice(0, -15) + "***************";
    copyToken.classList.remove('dcf-d-none');

    copyToken.parentElement.classList.add('dcf-input-group');

    copyToken.addEventListener('click', async () => {
        await navigator.clipboard.writeText(tokenValue);
    });
</script>

