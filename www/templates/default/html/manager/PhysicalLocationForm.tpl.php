<?php //These names need to match /UNL/UCBCN/Manager/LocationUtility ?>

<div class="dcf-d-grid dcf-grid-full dcf-grid-halves@md dcf-col-gap-5">
    <div class="dcf-form-group events-col-full-width">
        <label for="location-name">Name <small class="dcf-required">Required</small></label>
        <input
            id="location-name"
            class="dcf-w-100%"
            name="new_location[name]"
            type="text"
            value="<?php echo isset($context['new_location']['name']) ? $context['new_location']['name']: ''; ?>"
        >
    </div>

    <div class="dcf-form-group">
        <label for="location-address-1">Address <small class="dcf-required">Required</small></label>
        <input
            id="location-address-1"
            class="dcf-w-100%"
            name="new_location[streetaddress1]"
            type="text"
            value="<?php
                echo isset($context['new_location']['streetaddress1']) ?
                    $context['new_location']['streetaddress1']: '';
            ?>"
        >
    </div>
    <div class="dcf-form-group">
        <label for="location-address-2">Address 2</label>
        <input
            id="location-address-2"
            class="dcf-w-100%"
            name="new_location[streetaddress2]"
            type="text"
            value="<?php
                echo isset($context['new_location']['streetaddress2']) ?
                    $context['new_location']['streetaddress2']: ''; ?>"
            >
    </div>

    <div class="dcf-form-group">
        <label for="location-city">City <small class="dcf-required">Required</small></label>
        <input
            id="location-city"
            class="dcf-w-100%"
            name="new_location[city]"
            type="text"
            value="<?php echo isset($context['new_location']['city']) ? $context['new_location']['city']: ''; ?>"
        >
    </div>
    <div class="dcf-form-group">
        <label for="location-state">State <small class="dcf-required">Required</small></label>
        <?php $states = array(
            "AL" => "Alabama"       ,
            "AK" => "Alaska"        ,
            "AZ" => "Arizona"       ,
            "AR" => "Arkansas"      ,
            "CA" => "California"    ,
            "CO" => "Colorado"      ,
            "CT" => "Connecticut"   ,
            "DE" => "Delaware"      ,
            "FL" => "Florida"       ,
            "GA" => "Georgia"       ,
            "HI" => "Hawaii"        ,
            "ID" => "Idaho"         ,
            "IL" => "Illinois"      ,
            "IN" => "Indiana"       ,
            "IA" => "Iowa"          ,
            "KS" => "Kansas"        ,
            "KY" => "Kentucky"      ,
            "LA" => "Louisiana"     ,
            "ME" => "Maine"         ,
            "MD" => "Maryland"      ,
            "MA" => "Massachusetts" ,
            "MI" => "Michigan"      ,
            "MN" => "Minnesota"     ,
            "MS" => "Mississippi"   ,
            "MO" => "Missouri"      ,
            "MT" => "Montana"       ,
            "NE" => "Nebraska"      ,
            "NV" => "Nevada"        ,
            "NH" => "New Hampshire" ,
            "NJ" => "New Jersey"    ,
            "NM" => "New Mexico"    ,
            "NY" => "New York"      ,
            "NC" => "North Carolina",
            "ND" => "North Dakota"  ,
            "OH" => "Ohio"          ,
            "OK" => "Oklahoma"      ,
            "OR" => "Oregon"        ,
            "PA" => "Pennsylvania"  ,
            "RI" => "Rhode Island"  ,
            "SC" => "South Carolina",
            "SD" => "South Dakota"  ,
            "TN" => "Tennessee"     ,
            "TX" => "Texas"         ,
            "UT" => "Utah"          ,
            "VT" => "Vermont"       ,
            "VA" => "Virginia"      ,
            "WA" => "Washington"    ,
            "WV" => "West Virginia" ,
            "WI" => "Wisconsin"     ,
            "WY" => "Wyoming"
        );
        ?>
        <select name="new_location[state]" id="location-state">
            <?php foreach($states as $abbr => $state): ?>
                <option value="<?php echo $abbr; ?>"
                    <?php
                    if (isset($context['new_location']['state']) &&
                            $context['new_location']['state'] == $abbr
                        ):
                    ?>
                        selected="selected"
                    <?php elseif (!isset($context['new_location']['state']) && $abbr == 'NE'): ?>
                        selected="selected"
                    <?php endif; ?>
                >
                    <?php echo $state; ?>
                </option>
            <?php endforeach;?>
        </select>
    </div>

    <div class="dcf-form-group">
        <label for="location-zip">
            <abbr title="Zone Improvement Plan">ZIP</abbr>
            Code
            <small class="dcf-required">Required</small>
        </label>
        <input
            id="location-zip"
            class="dcf-w-100%"
            name="new_location[zip]"
            type="text"
            maxlength="10"
            value="<?php echo isset($context['new_location']['zip']) ? $context['new_location']['zip']: ''; ?>"
        >
    </div>

    <hr class="dcf-mb-5 events-col-full-width">

    <div class="dcf-form-group">
        <label for="location-map-url">Map <abbr title="Uniform Resource Locator">URL</abbr></label>
        <input
            id="location-map-url"
            class="dcf-w-100%"
            name="new_location[mapurl]"
            type="text"
            value="<?php
                echo isset($context['new_location']['mapurl']) ?
                    $context['new_location']['mapurl']: '';
            ?>"
        >
    </div>
    <div class="dcf-form-group">
        <label for="location-webpage">Web Page</label>
        <input
            id="location-webpage"
            class="dcf-w-100%"
            name="new_location[webpageurl]"
            type="text"
            value="<?php
                echo isset($context['new_location']['webpageurl']) ? $context['new_location']['webpageurl']: '';
            ?>"
        >
    </div>

    <div class="dcf-form-group">
        <label for="location-hours">Hours</label>
        <input
            id="location-hours"
            class="dcf-w-100%"
            name="new_location[hours]"
            type="text"
            value="<?php
                echo isset($context['new_location']['hours']) ? $context['new_location']['hours']: ''; ?>"
        >
    </div>
    <div class="dcf-form-group">
        <label for="location-phone">Phone</label>
        <input
            id="location-phone"
            class="dcf-w-100%"
            name="new_location[phone]"
            type="text"
            value="<?php
                echo isset($context['new_location']['phone']) ? $context['new_location']['phone']: ''; ?>"
        >
    </div>

    <hr class="dcf-mb-5 events-col-full-width">

    <div class="dcf-form-group events-col-full-width">
        <label for="location-room">Location Default - Room</label>
        <input
            id="location-room"
            name="new_location[room]"
            type="text"
            value="<?php echo isset($context['new_location']['room']) ? $context['new_location']['room']: ''; ?>"
        >
    </div>

    <div class="dcf-form-group events-col-full-width">
        <label for="location-directions">Location Default - Directions</label>
        <textarea
            id="location-directions"
            name="new_location[directions]"
        ><?php
            echo isset($context['new_location']['directions']) ? $context['new_location']['directions']: '';
        ?></textarea>
    </div>

    <div class="dcf-form-group events-col-full-width">
        <label for="location-additional-public-info">Location Default - Additional Public Info</label>
        <textarea
            id="location-additional-public-info"
            name="new_location[additionalpublicinfo]"
        ><?php
            echo isset($context['new_location']['additionalpublicinfo']) ?
            $context['new_location']['additionalpublicinfo']: '';
        ?></textarea>
    </div>
</div>
