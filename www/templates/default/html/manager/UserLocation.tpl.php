<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        "User Locations" => null
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
?>
<h1>User Locations</h1>

<div class="dcf-mb-5">
    <button id="new_location" class="dcf-btn dcf-btn-primary" type="button">Create A New Location</button>
</div>

<script>
    const LOCATIONS = [];
    LOCATIONS[0] = {
        'location'                        : 'new',
        'location-name'                   : '',
        'location-address-1'              : '',
        'location-address-2'              : '',
        'location-city'                   : '',
        'location-state'                  : 'NE',
        'location-zip'                    : '',
        'location-map-url'                : '',
        'location-webpage'                : '',
        'location-hours'                  : '',
        'location-phone'                  : '',
        'location-room'                   : '',
        'location-directions'             : '',
        'location-additional-public-info' : '',
        'user_id'                         : '',
        'calendar_id'                     : '',
    };
</script>

<table class="dcf-table dcf-table-responsive dcf-table-striped dcf-w-100%">
    <tbody>
        <?php foreach($context->getUserLocations() as $location): ?>
            <tr>
                <td class="dcf-bold">
                    <?php echo $location->name; ?>
                    <script>
                        <?php $location_json = $location->toJSON(); ?>
                        <?php $raw_location_json = $savvy->getRawObject($location_json); ?>
                        LOCATIONS[<?php echo $location->id; ?>] = <?php
                            echo json_encode($raw_location_json, JSON_UNESCAPED_SLASHES);
                        ?>
                    </script>
                </td>
                <td>
                    <?php if (isset($location->calendar_id)): ?>
                        <?php $locationCalendar = $location->getCalendar();?>
                        Saved to
                        <a href='<?php echo $locationCalendar->getManageURL(); ?>'>
                            <?php echo $locationCalendar->name; ?>
                        </a>
                        calendar
                    <?php else: ?>
                        Not attached to any calendar
                    <?php endif;?>
                </td>
                <td>
                    <form id="location_delete_<?php echo $location->id; ?>" method="post">
                        <?php echo $token_inputs; ?>

                        <input type="hidden" name="location" value="<?php echo $location->id; ?>">
                        <input type="hidden" name="method" value="delete">
                    </form>
                    <div class="dcf-btn-group">
                        <button
                            class="dcf-btn dcf-btn-primary events-modify-location"
                            type="button"
                            data-location-id="<?php echo $location->id; ?>"
                        >
                            Modify
                        </button>
                        <input
                            class="dcf-btn dcf-btn-secondary"
                            type="submit"
                            value="Detach"
                            form="location_delete_<?php echo $location->id; ?>"
                        >
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

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
    <input type="hidden" id="location" name="location" value="new">

    <fieldset class="dcf-mt-6" id="new-location-fields">
        <legend id="createOrModify-legend">Create New Location</legend>

        <?php echo $savvy->render($post, 'PhysicalLocationForm.tpl.php'); ?>

        <div class="dcf-form-group">
            <label> Attach To Calendar </label>
            <select name="calendar_id" id="calendar_id">
                <option
                    value=""
                    <?php if (!isset($post['calendar_id'])): ?>
                        selected="selected"
                    <?php endif;?>
                >
                    -- Not attached to any calendar --
                </option>
                <?php foreach($context->getUserCalendars() as $calendar): ?>
                    <?php if (!$context->userHasAccessToCalendar($calendar->id)) { continue; }?>
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

        <div class="dcf-form-group">
            <input id="createOrModify-submit" class="dcf-btn dcf-btn-primary" type="submit" value="Create New Location">
        </div>
    </fieldset>
</form>


<script>
    const createButton = document.getElementById('new_location');
    const modifyButtons = document.querySelectorAll('.events-modify-location');

    const createOrModifyForm = document.getElementById('createOrModify');
    const createOrModifyLegend = document.getElementById('createOrModify-legend');
    const createOrModifySubmit = document.getElementById('createOrModify-submit');

    createButton.addEventListener('click', () => {
        if (!createOrModifyForm.classList.contains('dcf-d-none')) {
            if (!confirm('Are you sure you want to clear out the form')) {
                return;
            }
        }
        createOrModifyLegend.innerText = "Create New Location";
        createOrModifySubmit.value = "Create New Location";
        fillInputs(0);
        showForm();
    });

    modifyButtons.forEach((modifyButton) => {
        modifyButton.addEventListener('click', () => {
            if (!createOrModifyForm.classList.contains('dcf-d-none')) {
                if (!confirm('Are you sure you want to clear out the form')) {
                    return;
                }
            }
            createOrModifyLegend.innerText = "Modify Location";
            createOrModifySubmit.value = "Modify Location";
            fillInputs(modifyButton.dataset.locationId);
            showForm();
        });
    });

    function fillInputs(location_id) {
        const locationToModify = LOCATIONS[location_id];
        const specialProps = ['user_id'];

        for (const locationProp in locationToModify) {
            if (specialProps.includes(locationProp)) { continue; }

            const elementToModify = document.getElementById(locationProp);
            if (elementToModify === null) { throw new Error(`Missing Element ${locationProp}`); }

            const defaultValue = locationProp === 'location-state' ? "NE" : "";
            elementToModify.value = locationToModify[locationProp] ?? defaultValue;
        }
    }

    function showForm() {
        createOrModifyForm.classList.remove('dcf-d-none');
    }
</script>
