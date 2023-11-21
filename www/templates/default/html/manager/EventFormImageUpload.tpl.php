<h2>Image</h2>
<section class="dcf-mb-8 dcf-ml-5">
    <div class="details">
        <?php if ($context->event->imagemime != NULL): ?>
            <div class="dcf-form-group">
                <img src="<?php echo $base_frontend_url ?>images/<?php echo $context->event->id; ?>" alt="image for event <?php echo $context->event->id; ?>">
                <?php if(!$context->on_main_calendar): ?>
                    <div class="dcf-input-checkbox">
                        <input id="remove-image" name="remove_image" type="checkbox">
                        <label class="dcf-txt-sm" for="remove-image">Remove Image</label>
                    </div>
                <?php else: ?>
                    <p class="dcf-txt-xs">
                        You may not remove this image at this time
                        due to your event being sent the main calendar for approval.
                        Images are required for main calendar events.
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="dcf-form-group">
            <label for="imagedata">Image Upload <small class="required-for-main-calendar dcf-required" style="display: none">Required</small></label>
            <input id="imagedata" name="imagedata" type="file" accept="image/gif, image/jpeg, image/png, image/avif, image/webp" aria-label="Select an Image">

        </div>
        <div class="dcf-mt-4 dcf-form-help">
            <p>Do not include text on image. Any text should be included in title and description of event. Image should be descriptive of event.</p>
        </div>
        <?php
            $croppedImageHiddenAttr = 'hidden';
            $croppedImageData = '';
            $croppedImageSrc = 'data:image/gif;base64,R0lGODlhAQABAIAAAP7//wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==';
            if (!empty($context->post['cropped_image_data'])) {
                $croppedImageHiddenAttr = '';
                $croppedImageData = $context->post['cropped_image_data'];
                $croppedImageSrc = $context->post['cropped_image_data'];
            }
        ?>
        <div class="dcf-mt-4 dcf-txt-xs" id="cropped-image-container" <?php echo $croppedImageHiddenAttr; ?>>
            <fieldset>
                <legend>Pending Image</legend>
                <input id="cropped-image-data" name="cropped_image_data" type="hidden" <?php echo $croppedImageData ?>>
                <img id="cropped-image" src="<?php echo $croppedImageSrc; ?>" alt="Preview of pending cropped image upload">
            </fieldset>
        </div>
    </div>
    <hr>
</section>
<div class="dcf-modal" id="image-modal" hidden>
    <div class="dcf-modal-wrapper">
        <div class="dcf-modal-header">
            <h3>Image Crop</h3>
            <button class="dcf-btn-close-modal">Close</button>
        </div>
        <div class="dcf-modal-content">
            <ul class="dcf-mb-4" id="cropper-errors" hidden></ul>
            <div id="cropper-content">
                <div class="dcf-mb-4">
                    <p class="dcf-txt-sm">To crop your image, position and size the blue square to the area to be cropped and press the <strong>Crop</strong> button.</p>
                </div>
                <div class="img-container">
                    <div class="dcf-grid dcf-col-gap-vw dcf-row-gap-4">
                        <div class="dcf-col-100% dcf-col-75%-start@md">
                            <img class="source-image" id="source-image" src="data:image/gif;base64,R0lGODlhAQABAIAAAP7//wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Source image to crop.">
                        </div>
                        <div class="dcf-col-100% dcf-col-25%-end@md">
                            <div class="preview-image"></div>
                        </div>
                    </div>
                </div>
                <div class="dcf-mt-4">
                    <button class="dcf-btn dcf-btn-secondary" id="cancel-crop-btn" type="button">Cancel</button>
                    <button class="dcf-btn dcf-btn-primary" id="crop-btn" type="button">Crop</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$page->addScript($base_frontend_url .'templates/default/html/js/event-image.min.js?v='.UNL\UCBCN\Frontend\Controller::$version);
?>
