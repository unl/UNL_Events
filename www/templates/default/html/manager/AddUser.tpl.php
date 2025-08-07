<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        $context->calendar->name => $context->calendar->getManageURL(),
        "Users & Permissions" => $context->calendar->getUsersURL(),
        $context->user == NULL ? 'Add a User' : 'Edit User Permissions' => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>

<h1><?php echo $context->user == NULL ? 'Add a User' : 'Edit User Permissions' ?></h1>

<?php if ($context->user == NULL) : ?>
    <form id="user-lookup" class="dcf-form">
        <fieldset>
            <legend>User Lookup</legend>
            <div class="dcf-form-group">
                <label for="user_lookup">Search for a user</label>
                <div class="dcf-input-group">
                    <input id="user_lookup" type="text" placeholder="Herbie Husker">
                    <input id="user_lookup_btn" class="dcf-btn dcf-btn-primary" type="submit" value="Look Up">
                </div>
            </div>
            <div class="dcf-form-group">
                <p id="user-lookup-load">Try searching for someone</p>
                <div id="user-lookup-loading" class="dcf-d-none! dcf-progress-spinner"></div>
                <p id="user-lookup-error" class="dcf-d-none! unl-bg-scarlet unl-cream dcf-rounded dcf-d-flex dcf-jc-center dcf-ai-center dcf-p-4">Error searching directory for users</p>
                <p id="user-lookup-none" class="dcf-d-none!">No Users Found</p>
                <div id="user-lookup-table" class="dcf-d-none!">
                    <table class="dcf-table dcf-table-striped">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Controls</th>
                            </tr>
                        </thead>
                        <tbody id="user-lookup-table-body">
        
                        </tbody>
                    </table>
                    <p class="dcf-mt-3 dcf-txt-sm">If the person you are searching for is not in this list please refine you search.</p>
                </div>
            </div>
        </fieldset>
        <template id="user-lookup-row">
            <tr>
                <td>
                    <img class="user-lookup-img dcf-h-8 dcf-w-8 dcf-rounded dcf-1x1" src="">
                </td>
                <td>
                    <a class="user-lookup-name"></a>
                </td>
                <td>
                    <div class="user-lookup-btn-container dcf-d-flex dcf-gap-3">
                        <button class="user-lookup-btn dcf-btn dcf-btn-primary" type="button">Select User</button>
                        <div class="user-lookup-btn-success unl-bg-green dcf-rounded unl-cream dcf-d-flex dcf-jc-center dcf-ai-center dcf-pl-3 dcf-pr-3 dcf-invisible">Selected</div>
                    </div>
                    <div class="user-lookup-not-available dcf-txt-xs dcf-d-none!">
                        This user is already on the calendar
                    </div>
                    <div class="user-lookup-no-login dcf-txt-xs dcf-d-none!">
                        This user needs to login to events before you can select them. <br>
                        Please have them navigate to <a href="http://events.unl.edu/manager">http://events.unl.edu/manager<a> and login.
                    </div>
                </td>
            </tr>
        </template>
    </form>
<?php endif; ?>
<form id="add-user" class="dcf-form" action="" method="POST">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <?php if ($context->user == NULL) { ?>
        <div class="dcf-form-group">
            <label for="user">User</label>
            <select id="user" name="user">
                <?php foreach($context->getAvailableUsers() as $user) { ?>
                    <option value="<?php echo $user->uid ?>"><?php echo $user->uid ?></option>
                <?php } ?>
            </select>
        </div>
        <p class="dcf-form-help">Note: This is a list of users who have previously logged into the system. If you do not see someone in this list, please have him or her navigate to <a href="http://events.unl.edu/manager">http://events.unl.edu/manager</a> and login. His or her username will then be present in this list.</p>
    <?php } else { ?>
        <p>Editing for User:</label><br>
        <strong><?php echo $context->user->uid ?></strong></p>
    <?php } ?>

    <fieldset>
      <legend>Permissions</legend>
      <?php foreach ($context->getAllPermissions() as $permission) { ?>
          <div class="dcf-input-checkbox">
            <input id="permission-<?php echo $permission->id ?>" name="permission_<?php echo $permission->id ?>" type="checkbox"
            <?php if (($context->user != NULL && $context->user->hasPermission($permission->id, $context->calendar->id)) ||
                ($context->user == NULL && $permission->standard)) { echo 'checked="checked"'; } ?>>
            <label for="permission-<?php echo $permission->id ?>"><?php echo $permission->description ?></label>
          </div>
      <?php } ?>
    </fieldset>
    <button class="dcf-btn dcf-btn-primary" type="submit"><?php echo $context->user == NULL ? 'Add User' : 'Update User Permissions' ?></button>
</form>

<?php if ($context->user == NULL) : ?>
    <?php
        $json_data_available = [];
        foreach($context->getAvailableUsers() as $user) {
            $json_data_available[] = $user->uid;
        }

        $json_data_all = [];
        foreach($context->getAllUsers() as $user) {
            $json_data_all[] = $user->uid;
        }
    ?>

    <script>
        window.UNL_Events = window.UNL_Events ?? {};
        window.UNL_Events.availableUsers = <?php echo json_encode($json_data_available); ?>;
        window.UNL_Events.allUsers = <?php echo json_encode($json_data_all); ?>;

        const user_lookup_form = document.getElementById('user-lookup');
        const user_lookup_table = document.getElementById('user-lookup-table');
        const user_lookup_loading_spinner = document.getElementById('user-lookup-loading');
        const user_lookup_load_message = document.getElementById('user-lookup-load');
        const user_lookup_error_message = document.getElementById('user-lookup-error');
        const user_lookup_none_message = document.getElementById('user-lookup-none');
        const user_lookup_table_body = document.getElementById('user-lookup-table-body');
        const user_lookup_row_template = document.getElementById('user-lookup-row');


        const directory_lookup_url = new URL('https://directory.unl.edu/service.php?q=&format=json&method=getExactMatches')
        user_lookup_form.addEventListener('submit', async(e) => {
            e.preventDefault();
            fetch_directory_lookup();
        });

        let timeout_id = null;
        let interval_id = null;
        user_lookup_table.addEventListener('click', (e) => {
            if (!e.target.classList.contains('user-lookup-btn')) {
                return;
            }

            const success_message = e.target.parentElement.querySelector('.user-lookup-btn-success');
            success_message.classList.remove('dcf-invisible');
            success_message.style.opacity = 1;
            let current_opacity = 1;
            clearTimeout(timeout_id);
            clearInterval(interval_id);
            timeout_id = setTimeout(() => {
                interval_id = setInterval(() => {
                    current_opacity -= 0.01;
                    success_message.style.opacity = current_opacity;
                    if (current_opacity <= 0) {
                        success_message.classList.add('dcf-invisible');
                        success_message.style.opacity = 1;
                        clearInterval(interval_id);
                    }
                }, 10);
            }, 500);

            document.getElementById('user').value = e.target.dataset.uid;
        }, true);

        async function fetch_directory_lookup() {
            user_lookup_loading_spinner.classList.remove('dcf-d-none!');
            user_lookup_load_message.classList.add('dcf-d-none!');
            user_lookup_error_message.classList.add('dcf-d-none!');
            user_lookup_table.classList.add('dcf-d-none!');
            user_lookup_none_message.classList.add('dcf-d-none!');
            user_lookup_table_body.innerHTML = '';

            directory_lookup_url.searchParams.set('q', document.getElementById('user_lookup').value);
            let response = null;
            try {
                response = await fetch(directory_lookup_url.toString());
            } catch (err) {
                user_lookup_loading_spinner.classList.add('dcf-d-none!');
                user_lookup_error_message.classList.remove('dcf-d-none!');
                throw new Error(err);
            }
            if (response === null) {
                user_lookup_loading_spinner.classList.add('dcf-d-none!');
                user_lookup_error_message.classList.remove('dcf-d-none!');
                throw new Error('No response from directory');
            }
            if (!response.ok) {
                user_lookup_loading_spinner.classList.add('dcf-d-none!');
                user_lookup_error_message.classList.remove('dcf-d-none!');
                throw new Error(`Bad response: ${response.status}`);
            }
            const json_text = await response.json();
            if (!Array.isArray(json_text)) {
                user_lookup_loading_spinner.classList.add('dcf-d-none!');
                user_lookup_error_message.classList.remove('dcf-d-none!');
                throw new Error('Invalid json data');
            }

            let found_user = false;
            json_text.forEach((single_user) => {
                found_user = true;
                const new_row = user_lookup_row_template.content.cloneNode(true);

                new_row.querySelector('.user-lookup-img').setAttribute('src', single_user.imageURL);
                new_row.querySelector('.user-lookup-name').innerText = `${single_user.displayName} (${single_user.cn[0]})`;
                new_row.querySelector('.user-lookup-name').setAttribute('href', `https://directory.unl.edu/people/${single_user.cn[0]}`);
                new_row.querySelector('.user-lookup-btn').dataset.uid = single_user.cn[0];

                if (!window.UNL_Events.allUsers.includes(single_user.cn[0])) {
                    new_row.querySelector('.user-lookup-btn-container').classList.add('dcf-d-none!');
                    new_row.querySelector('.user-lookup-no-login').classList.remove('dcf-d-none!');
                } else if (!window.UNL_Events.availableUsers.includes(single_user.cn[0])) {
                    new_row.querySelector('.user-lookup-btn-container').classList.add('dcf-d-none!');
                    new_row.querySelector('.user-lookup-not-available').classList.remove('dcf-d-none!');
                }

                user_lookup_table_body.append(new_row);
            });

            if (!found_user) {
                user_lookup_none_message.classList.remove('dcf-d-none!');
            } else {
                user_lookup_table.classList.remove('dcf-d-none!');
            }

            user_lookup_loading_spinner.classList.add('dcf-d-none!');
        }
    </script>
<?php endif; ?>