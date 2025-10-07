import { loadStyleSheet } from "/wdn/templates_6.0/js/lib/unl-utility.js";

import "/js/cropperjs/cropper.min.js";

loadStyleSheet('/js/cropperjs/cropper.min.css');

const cropperErrorContainer = document.getElementById('cropper-errors');
const cropperContentContainer = document.getElementById('cropper-content');
const image = document.getElementById('source-image');
const croppedImageContainer = document.getElementById('cropped-image-container');
const croppedImage = document.getElementById('cropped-image');
const croppedImageData = document.getElementById('cropped-image-data');
const imageData = document.getElementById('imagedata');
const modal = document.getElementById('image-modal');
const cancelBtn = document.getElementById('cancel-crop-btn');
const cropBtn = document.getElementById('crop-btn');
let modalClassInstance = null;
let cropper;
let outputType;
let errors = [];

modal.addEventListener('dialogReady', (e) => {
  modalClassInstance = e.detail.classInstance;
});

const isValidFile = function(file) {
  errors = [];
  switch(file.type) {
    case 'image/png':
    case 'image/jpeg':
    case 'image/gif':
    case 'image/avif':
    case 'image/webp':
      break;
    default:
      errors.push('Invalid file type (' + file.type + ').  Allowed types: avif, gif, jpeg, png and webp.');
  }

  if (file.size > 8388608) {
    const sizeInMB = file.size/1024/1024;
    errors.push('File size (' + sizeInMB.toFixed(2) + ' MB) over limit of 8 MB.');
  }

  return errors.length === 0;
};

const processCrop = function(file) {

  const done = function (imageUrl) {
    image.src = imageUrl;
    modalClassInstance.open();
  };
  let reader;

  if (file.type === 'image/jpeg') {
    outputType = file.type;
  } else {
    outputType = 'image/png';
  }

  if (FileReader) {
    reader = new FileReader();
    reader.onload = function (readerOnloadEvent) {
      done(reader.result);
    };
    reader.readAsDataURL(file);
  }
};

imageData.addEventListener('change', function(imageDataChangeEvent) {
  const files = imageDataChangeEvent.target.files;

  if (files && files.length > 0) {
    const file = files[0];

    if (isValidFile(file)) {
      processCrop(file);
    } else {
      // Set and display errors in modal
      cropperErrorContainer.innerHTML = '';
      errors.forEach(function(error){
        const errorListItem = document.createElement('LI');
        errorListItem.innerText = error;
        cropperErrorContainer.append(errorListItem);
      });
      cropperErrorContainer.removeAttribute('hidden');
      cropperContentContainer.setAttribute('hidden', '');
      modalClassInstance.open();
    }
  }
});

cancelBtn.addEventListener('click', function(e) {
  modalClassInstance.close();
});

cropBtn.addEventListener('click', function(e) {
  const canvas = cropper.getCroppedCanvas({
    minWidth: 150,
    minHeight: 150,
    maxWidth: 4096,
    maxHeight: 4096,
    fillColor: '#fff',
    imageSmoothingEnabled: true,
    imageSmoothingQuality: 'high'
  });

  canvas.toBlob(function(blob) {
    const reader = new FileReader();
    reader.readAsDataURL(blob);
    reader.onloadend = function() {
      const base64data = reader.result;
      croppedImage.setAttribute('src', base64data);
      croppedImageContainer.removeAttribute('hidden');
      croppedImageData.value = base64data;
      modalClassInstance.close();
    };
  }, outputType, 0.9);
});

modal.addEventListener('dialogPreOpen', () => {
  cropper = new window.Cropper(image, {
    aspectRatio: 1,
    viewMode: 2,
    autoCropArea: 1,
    preview: '.preview-image'
  });
});

modal.addEventListener('dialogPostClose', () => {
  imageData.value = '';
  cropper.destroy();
  cropper = null;
});
