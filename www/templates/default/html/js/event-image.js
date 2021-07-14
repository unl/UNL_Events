require.config({
  paths: {
    "cropperjs": "/js/cropperjs"
  }
});

require(['cropperjs/cropper.min', 'css!cropperjs/cropper.min.css'], function(Cropper) {
  var cropperErrorContainer = document.getElementById('cropper-errors');
  var cropperContentContainer = document.getElementById('cropper-content');
  var image = document.getElementById('source-image');
  var croppedImageContainer = document.getElementById('cropped-image-container');
  var croppedImage = document.getElementById('cropped-image');
  var croppedImageData = document.getElementById('cropped-image-data');
  var imageData = document.getElementById('imagedata');
  var modal = document.getElementById('image-modal');
  var cancelBtn = document.getElementById('cancel-crop-btn');
  var cropBtn = document.getElementById('crop-btn');
  var modals = new DCFModal([]);
  var cropper;
  var outputType;
  var errors = [];

  var isValidFile = function(file) {
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
      var sizeInMB = file.size/1024/1024;
      errors.push('File size (' + sizeInMB.toFixed(2) + ' MB) over limit of 8 MB.');
    }

    return errors.length === 0;
  };

  imageData.addEventListener('change', function(e) {
    var files = e.target.files;
    var done = function (url) {
      image.src = url;
      modals.openModal('image-modal');
    };
    var reader;
    var file;
    var url;

    if (files && files.length > 0) {
      file = files[0];

      if (isValidFile(file)) {
        if (file.type === 'image/jpeg') {
          outputType = file.type;
        } else {
          outputType = 'image/png';
        }

        if (URL) {
          done(URL.createObjectURL(file));
        } else if (FileReader) {
          reader = new FileReader();
          reader.onload = function (e) {
            done(reader.result);
          };
          reader.readAsDataURL(file);
        }
      } else {
        cropperErrorContainer.innerHTML = '';
        for (var errorIndex = 0; errorIndex < errors.length; errorIndex++) {
          var errorListItem = document.createElement('LI');
          errorListItem.innerText = errors[errorIndex];
          cropperErrorContainer.append(errorListItem);
        }
        cropperErrorContainer.removeAttribute('hidden');
        cropperContentContainer.setAttribute('hidden', '');
        modals.openModal('image-modal');
      }
    }
  });

  cancelBtn.addEventListener('click', function(e) {
    modals.closeModal('image-modal');
  });

  cropBtn.addEventListener('click', function(e) {
    canvas = cropper.getCroppedCanvas({
      minWidth: 150,
      minHeight: 150,
      maxWidth: 4096,
      maxHeight: 4096,
      fillColor: '#fff',
      imageSmoothingEnabled: true,
      imageSmoothingQuality: 'high'
    });

    canvas.toBlob(function(blob) {
      url = URL.createObjectURL(blob);
      var reader = new FileReader();
      reader.readAsDataURL(blob);
      reader.onloadend = function() {
        var base64data = reader.result;
        croppedImage.setAttribute('src', base64data);
        croppedImageContainer.removeAttribute('hidden');
        croppedImageData.value = base64data;
        modals.closeModal('image-modal');
      };
    }, outputType, 0.9);
  });

  document.addEventListener('ModalOpenEvent_' + modal.id, function (e) {
    cropper = new Cropper(image, {
      aspectRatio: 1,
      viewMode: 3,
      preview: '.preview-image'
    });
  });

  document.addEventListener('ModalCloseEvent_' + modal.id, function (e) {
    imageData.value = '';
    cropper.destroy();
    cropper = null;
  });
});
