<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        "User Virtual Locations" => null
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
    $webcasts = $context->getUserWebcasts();
?>
<script>
    const LOCATIONS = [];
    LOCATIONS[0] = {
        'v-location'                            : 'new',
        'new-v-location-name'                   : '',
        'new-v-location-url'                    : '',
        'new-v-location-additional-public-info' : '',
        'user_id'                               : '',
        'calendar_id'                           : '',
        'calendar-name'                         : '',
        'can-access-calendar'                   : true,
    };
</script>

<div class="dcf-mb-5 dcf-d-flex dcf-flex-row dcf-flex-wrap dcf-jc-between dcf-ai-center">
    <h1 id="table_desc">User Virtual Locations</h1>
    <button id="new_location" class="dcf-btn dcf-btn-primary" type="button">Create A New Virtual Location</button>
</div>

<?php if (count($webcasts) === 0) :?>
    <p class="dcf-bold">You have not saved any virtual locations yet<p>
<?php else: ?>
    <table class="dcf-table dcf-table-responsive dcf-table-striped dcf-w-100% dcf-mt-5" aria-describedby="table_desc">
        <thead>
            <tr>
                <th>Virtual Location Name</th>
                <th>Saved Calendar</th>
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
                            <?php $webcast_json = $webcast->toJSON(); ?>
                            <?php $raw_webcast_json = $savvy->getRawObject($webcast_json); ?>
                            LOCATIONS[<?php echo $webcast->id; ?>] = <?php
                                echo json_encode($raw_webcast_json, JSON_UNESCAPED_SLASHES);
                            ?>;
                            LOCATIONS[<?php echo $webcast->id; ?>]['can-access-calendar'] = <?php
                                echo json_encode(
                                    $context->userHasAccessToCalendar($webcast->calendar_id ?? ""), 
                                    JSON_UNESCAPED_SLASHES
                                );
                            ?>;
                        </script>
                    </td>
                    <td>
                        <?php if (isset($webcast->calendar_id)): ?>
                            <?php $webcastCalendar = $webcast->getCalendar();?>
                            Saved to
                            <?php if ($context->userHasAccessToCalendar($webcast->calendar_id ?? "")): ?>
                                <a href='<?php echo $webcastCalendar->getManageURL(); ?>'>
                                    <?php echo $webcastCalendar->name; ?>
                                </a>
                            <?php else: ?>
                                <a href='<?php echo $webcastCalendar->getFrontendURL(); ?>'>
                                    <?php echo $webcastCalendar->name; ?>
                                </a>
                            <?php endif;?>
                            calendar
                            <script>
                                LOCATIONS[<?php echo $webcast->id; ?>]['calendar-name'] = <?php
                                    echo json_encode($webcastCalendar->name, JSON_UNESCAPED_SLASHES);
                                ?>;
                            </script>
                        <?php endif;?>
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
                                value="Un-Save"
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
            <?php $vLocation = isset($post['v_location']) ? $context->getWebcast($post['v_location']) : false; ?>
            <?php $vLocationCalendar = $vLocation !== false ? $vLocation->getCalendar() : false; ?>
            <div
                id="createOrModify-calendar"
                class="
                    <?php if (
                        (isset($post['method']) && $post['method'] === 'put')
                        && ($vLocation !== false && !$context->userHasAccessToCalendar($vLocation->calendar_id ?? ""))
                    ): ?>
                        dcf-d-none
                    <?php endif; ?>
                "
            >
                <label> Save To Calendar </label>
                <select name="calendar_id" id="calendar_id">
                    <option
                        value=""
                        <?php if (!isset($post['calendar_id'])): ?>
                            selected="selected"
                        <?php endif;?>
                    >
                        -- Not saved to any calendar --
                    </option>
                    <?php foreach($context->getUserCalendars() as $calendar): ?>
                        <?php if (!$context->userHasAccessToCalendar($calendar->id ?? "")) { continue; }?>
                        <option
                            value="<?php echo $calendar->id; ?>"
                            <?php if (isset($post['calendar_id']) && $post['calendar_id'] === $calendar->id): ?>
                                selected="selected"
                            <?php endif;?>
                        >
                            <?php echo $calendar->name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <p
                id="createOrModify-taken"
                class="
                    <?php if (
                        (isset($post['method']) && $post['method'] !== 'put')
                        || (
                            (isset($post['method']) && $post['method'] === 'put')
                            && (
                                $vLocation !== false
                                && $context->userHasAccessToCalendar($vLocation->calendar_id ?? "")
                            )
                        )
                    ): ?>
                        dcf-d-none
                    <?php endif; ?>
                "
            >
                This virtual location is already saved to
                <?php echo $vLocationCalendar !== false ? $vLocationCalendar->name : ''; ?>
            </p>
        </div>

        <div class="dcf-form-group">
            <input
                id="createOrModify-submit"
                class="dcf-btn dcf-btn-primary"
                type="submit" value="Create New Virtual Location"
            >
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

    const createOrModifyCalendar = document.getElementById('createOrModify-calendar');
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
            createOrModifyLegend.innerText = "Modify Location";
            createOrModifySubmit.value = "Modify Location";
            createOrModifyMethod.value = "put";
            fillInputs(modifyButton.dataset.locationId);
            showForm();
        });
    });

    function fillInputs(location_id) {
        const locationToModify = LOCATIONS[location_id];
        const specialProps = ['user_id', 'can-access-calendar', 'calendar-name'];

        for (const locationProp in locationToModify) {
            if (specialProps.includes(locationProp)) { continue; }

            const elementToModify = document.getElementById(locationProp);
            if (elementToModify === null) { throw new Error(`Missing Element ${locationProp}`); }

            elementToModify.value = locationToModify[locationProp] ?? "";
        }

        if (locationToModify['can-access-calendar']) {
            createOrModifyCalendar.classList.remove('dcf-d-none');
            createOrModifyTaken.classList.add('dcf-d-none');
        } else {
            createOrModifyCalendar.classList.add('dcf-d-none');
            createOrModifyTaken.classList.remove('dcf-d-none');
            createOrModifyTaken.innerText = `This virtual location is already saved to ${locationToModify['calendar-name']}`;

            const elementToModify = document.getElementById('calendar_id');
            if (elementToModify === null) { throw new Error(`Missing Element calendar_id`); }

            elementToModify.value = '';
        }
    }

    function hideForm() {
        createOrModifyForm.classList.add('dcf-d-none');
    }

    function showForm() {
        createOrModifyForm.classList.remove('dcf-d-none');
    }
</script>
