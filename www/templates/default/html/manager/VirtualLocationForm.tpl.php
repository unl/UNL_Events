<?php //These names need to match /UNL/UCBCN/Manager/WebcastUtility ?>

<div class="dcf-d-grid dcf-grid-full dcf-grid-halves@md">
    <div class="dcf-form-group events-col-full-width">
        <label for="new-v-location-name">Name <small class="dcf-required">Required</small></label>
        <input
            id="new-v-location-name"
            name="new_v_location[title]"
            type="text"
            class="dcf-w-100%"
            value="<?php
                echo isset($context['new_v_location']['title']) ? $context['new_v_location']['title']: '';
            ?>"
        >
    </div>
    <div class="dcf-form-group events-col-full-width">
        <label for="new-v-location-url">URL<small class="dcf-required">Required</small></label>
        <input
            id="new-v-location-url"
            name="new_v_location[url]"
            type="text"
            class="dcf-w-100%"
            value="<?php
                echo isset($context['new_v_location']['url']) ? $context['new_v_location']['url']: '';
            ?>"
        >
    </div>
    <div class="dcf-form-group events-col-full-width">
        <label for="new-v-location-additional-public-info">Location Default - Additional Public Info</label>
        <textarea
            id="new-v-location-additional-public-info"
            name="new_v_location[additionalinfo]"
        ><?php
            if (isset($context['new_v_location']['additionalinfo'])) {
                echo $context['new_v_location']['additionalinfo'];
            }
        ?></textarea>
    </div>
</div>
