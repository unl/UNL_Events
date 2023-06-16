<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        $context->calendar->name => $context->calendar->getManageURL(),
        "Calendar Virtual Locations" => null
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');

    $post = $context->post ?? null;

    $token_inputs = '<input ' .
        'type="hidden" '.
        'name="' . $controller->getCSRFHelper()->getTokenNameKey() . '" ' .
        'value="' . $controller->getCSRFHelper()->getTokenName() . '" ' .
    '> ' .
    '<input ' .
        'type="hidden"' .
        'name="' . $controller->getCSRFHelper()->getTokenValueKey() . '" ' .
        'value="' . $controller->getCSRFHelper()->getTokenValue() . '" ' .
    '> ';
    $webcasts = $context->getCalendarWebcasts();
?>
<script>
    const CURRENTUSER = '<?php echo $context->getCurrentUser(); ?>';
    const LOCATIONS = [];
    LOCATIONS[0] = {
        'v-location'                            : 'new',
        'new-v-location-name'                   : '',
        'new-v-location-url'                    : '',
        'new-v-location-additional-public-info' : '',
        'user_id'                               : '',
        'calendar_id'                           : '',
    };
</script>

<div class="dcf-mb-5 dcf-d-flex dcf-flex-row dcf-flex-wrap dcf-jc-between dcf-ai-center">
    <h1 id="table_desc">Calendar Virtual Locations</h1>
    <button id="new_location" class="dcf-btn dcf-btn-primary" type="button">Create A New Virtual Location</button>
</div>

<?php if (count($webcasts) === 0) :?>
    <p>Your calendar has not saved any virtual locations yet<p>
<?php else: ?>
    <table class="dcf-table dcf-table-responsive dcf-table-striped dcf-w-100%" aria-describedby="table_desc">
        <thead>
            <tr>
                <th>Virtual Location Name</th>
                <th>Attached Person</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($webcasts as $webcast): ?>
                <tr>
                    <td>
                        <a href="<?php echo $webcast->url; ?>">
                            <?php echo $webcast->title; ?>
                        </a>
                        <script>
                            <?php $virtual_location_json = $webcast->toJSON(); ?>
                            <?php $raw_virtual_location_json = $savvy->getRawObject($virtual_location_json); ?>
                            LOCATIONS[<?php echo $webcast->id; ?>] = <?php
                                echo json_encode($raw_virtual_location_json, JSON_UNESCAPED_SLASHES);
                            ?>;
                        </script>
                    </td>
                    <td>
                        <?php if (isset($webcast->user_id)): ?>
                            Virtual location attached to <?php echo $webcast->user_id; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form id="location_delete_<?php echo $webcast->id; ?>" method="post">
                            <?php echo $token_inputs; ?>

                            <input type="hidden" name="v_location" value="<?php echo $webcast->id; ?>">
                            <input type="hidden" name="method" value="delete">
                        </form>
                        <div class="dcf-btn-group">
                            <button
                                class="dcf-btn dcf-btn-primary events-modify-location"
                                type="button"
                                data-location-id="<?php echo $webcast->id; ?>"
                            >
                                Modify
                            </button>
                            <input
                                class="dcf-btn dcf-btn-secondary"
                                type="submit"
                                value="Detach"
                                form="location_delete_<?php echo $webcast->id; ?>"
                            >
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<form
    id="createOrModify"
    class="dcf-form
        dcf-mt-5
        <?php if (!isset($post)): ?>
            dcf-d-none
        <?php endif; ?>
    "
    method="post"
>
    <?php echo $token_inputs; ?>
    <input
        type="hidden"
        id="v-location"
        name="v_location"
        <?php if (isset($post['v_location'])): ?>
            value="<?php echo $post['v_location'];?>"
        <?php else: ?>
            value="new"
        <?php endif; ?>
    >
    <input
        type="hidden"
        id="createOrModify-method"
        name="method"
        <?php if (isset($post['method'])): ?>
            value="<?php echo $post['method'];?>"
        <?php else: ?>
            value="post"
        <?php endif; ?>
    >

    <fieldset class="dcf-mt-6" id="new-location-fields">
        <legend id="createOrModify-legend">Create New Virtual Location</legend>

        <?php echo $savvy->render($post, 'VirtualLocationForm.tpl.php'); ?>

        <div class="dcf-form-group">
            <input
                type="hidden"
                id="user_id"
                name="user_id"
                value="<?php echo isset($post['user_id']) ? $post['user_id']: ''; ?>"
            >
            <div 
                id="createOrModify-userCheck" 
                class="
                    dcf-input-checkbox
                    <?php if (isset($post['user_id']) && $post['user_id'] !== $context->getCurrentUser()): ?>
                        dcf-d-none
                    <?php endif; ?>
                "
            >
                <input
                    id="v-location-save"
                    name="v_location_save"
                    type="checkbox"
                    <?php
                        if (isset($post['v_location_save']) && $post['v_location_save'] == 'on') { 
                            echo CHECKED_INPUT; 
                        }
                    ?>
                >
                <label for="v-location-save">Save this virtual location for your future events</label>
            </div>
            <p 
                id="createOrModify-taken"
                class="
                    <?php if (!isset($post['user_id']) || $post['user_id'] === $context->getCurrentUser()): ?>
                        dcf-d-none
                    <?php endif; ?>
                " 
            >
                This virtual location is already saved to <?php echo $post['user_id']; ?>
            </p>
        </div>

        <div class="dcf-form-group">
            <input id="createOrModify-submit" class="dcf-btn dcf-btn-primary" type="submit" value="Create New Virtual Location">
            <button id="createOrModify-cancel" class="dcf-btn dcf-btn-secondary" type="button">Cancel</button>
        </div>
    </fieldset>
</form>


<script>
    const createButton = document.getElementById('new_location');
    const modifyButtons = document.querySelectorAll('.events-modify-location');

    const createOrModifyForm = document.getElementById('createOrModify');
    const createOrModifyLegend = document.getElementById('createOrModify-legend');
    const createOrModifySubmit = document.getElementById('createOrModify-submit');
    const createOrModifyCancel = document.getElementById('createOrModify-cancel');
    const createOrModifyMethod = document.getElementById('createOrModify-method');

    const locationSave = document.getElementById('v-location-save');
    const createOrModifyUserCheck = document.getElementById('createOrModify-userCheck');
    const createOrModifyTaken = document.getElementById('createOrModify-taken');

    createOrModifyCancel.addEventListener('click', () => {
        if (!confirm('Are you sure you want to clear out the form?')) {
            return;
        }
        hideForm();
    });

    createButton.addEventListener('click', () => {
        if (!createOrModifyForm.classList.contains('dcf-d-none')) {
            if (!confirm('Are you sure you want to clear out the form?')) {
                return;
            }
        }
        createOrModifyLegend.innerText = "Create New Virtual Location";
        createOrModifySubmit.value = "Create New Virtual Location";
        createOrModifyMethod.value = "post";
        fillInputs(0);
        showForm();
    });

    modifyButtons.forEach((modifyButton) => {
        modifyButton.addEventListener('click', () => {
            if (!createOrModifyForm.classList.contains('dcf-d-none')) {
                if (!confirm('Are you sure you want to clear out the form?')) {
                    return;
                }
            }
            createOrModifyLegend.innerText = "Modify Virtual Location";
            createOrModifySubmit.value = "Modify Virtual Location";
            createOrModifyMethod.value = "put";
            fillInputs(modifyButton.dataset.locationId);
            showForm();
        });
    });

    function fillInputs(location_id) {
        const locationToModify = LOCATIONS[location_id];
        const specialProps = ['calendar_id'];

        for (const locationProp in locationToModify) {
            if (specialProps.includes(locationProp)) { continue; }

            const elementToModify = document.getElementById(locationProp);
            if (elementToModify === null) { throw new Error(`Missing Element ${locationProp}`); }

            elementToModify.value = locationToModify[locationProp] ?? "";
        }

        if (locationToModify['user_id'] === null || locationToModify['user_id'] === '' || locationToModify['user_id'] === CURRENTUSER) {
            createOrModifyUserCheck.classList.remove('dcf-d-none');
            createOrModifyTaken.classList.add('dcf-d-none');

            if (locationToModify['user_id'] === CURRENTUSER) {
                locationSave.checked = true;
            } else {
                locationSave.checked = false;
            }
        } else {
            createOrModifyUserCheck.classList.add('dcf-d-none');
            createOrModifyTaken.classList.remove('dcf-d-none');

            createOrModifyTaken.innerText = `This virtual location is already saved to ${locationToModify['user_id']}`;
            locationSave.checked = false;
        }
    }

    function hideForm() {
        createOrModifyForm.classList.add('dcf-d-none');
    }

    function showForm() {
        createOrModifyForm.classList.remove('dcf-d-none');
    }
</script>
