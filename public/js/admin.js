document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.querySelector('[data-sidebar-toggle]');
  var sidebar = document.querySelector('.admin-sidebar');

  if (toggle && sidebar) {
    toggle.addEventListener('click', function () {
      sidebar.classList.toggle('is-open');
    });
  }

  // ---------- Product media manager (ordered mixed images + videos) ----------
  var manager = document.querySelector('[data-media-manager]');
  if (!manager) return;

  var list = manager.querySelector('[data-media-list]');
  var maxItems = parseInt(manager.dataset.maxItems || '10', 10);
  var LIMITS = {
    image: { maxBytes: 4 * 1024 * 1024, extensions: ['jpg', 'jpeg', 'png', 'webp'] },
    video: { maxBytes: 20 * 1024 * 1024, extensions: ['mp4', 'webm'] },
  };

  // Ordered gallery state: { type: 'image'|'video', path?: already-uploaded
  // filename, file?: newly picked File, url?: local preview blob URL }.
  var items = [];
  var dragIndex = -1;

  try {
    var bootstrap = manager.querySelector('[data-media-existing]');
    JSON.parse(bootstrap ? bootstrap.textContent : '[]').forEach(function (item) {
      if (item && (item.type === 'image' || item.type === 'video') && item.path) {
        items.push({ type: item.type, path: item.path });
      }
    });
  } catch (error) {
    items = [];
  }

  function fileUrl(item) {
    if (item.url) return item.url;
    return manager.dataset.assetBase + (item.type === 'video' ? '/videos/products/' : '/images/products/') + item.path;
  }

  function extensionOf(name) {
    var parts = String(name).split('.');
    return parts.length > 1 ? parts.pop().toLowerCase() : '';
  }

  function dataTransferFiles(file) {
    var transfer = new DataTransfer();
    transfer.items.add(file);
    return transfer.files;
  }

  function orderButton(index, delta, symbol, label) {
    var button = document.createElement('button');
    button.type = 'button';
    button.innerHTML = symbol;
    button.setAttribute('aria-label', label);
    if ((delta === -1 && index === 0) || (delta === 1 && index === items.length - 1)) {
      button.disabled = true;
    }
    button.addEventListener('click', function () {
      var target = index + delta;
      if (target < 0 || target >= items.length || target === index) return;
      var moved = items.splice(index, 1)[0];
      items.splice(target, 0, moved);
      render();
    });
    return button;
  }

  function render() {
    list.innerHTML = '';

    if (!items.length) {
      var empty = document.createElement('div');
      empty.className = 'media-manager__empty';
      empty.textContent = 'No media yet — use “Add Image” or “Add Video” above.';
      list.appendChild(empty);
      return;
    }

    items.forEach(function (item, index) {
      var card = document.createElement('div');
      card.className = 'media-manager__item' + (dragIndex === index ? ' is-dragging' : '');
      card.draggable = true;
      card.dataset.index = String(index);

      var preview = document.createElement('div');
      preview.className = 'media-manager__preview';
      if (item.type === 'video') {
        var video = document.createElement('video');
        video.src = fileUrl(item);
        video.muted = true;
        video.preload = 'metadata';
        video.playsInline = true;
        preview.appendChild(video);
      } else {
        var image = document.createElement('img');
        image.src = fileUrl(item);
        image.alt = 'Media item ' + (index + 1);
        preview.appendChild(image);
      }
      card.appendChild(preview);

      var badge = document.createElement('span');
      badge.className = 'media-manager__badge';
      badge.textContent = String(index + 1);
      card.appendChild(badge);

      if (item.type === 'video') {
        var play = document.createElement('span');
        play.className = 'media-manager__play';
        play.innerHTML = '<i class="fa-solid fa-play"></i>';
        card.appendChild(play);
      }

      var remove = document.createElement('button');
      remove.type = 'button';
      remove.className = 'media-manager__remove';
      remove.innerHTML = '&times;';
      remove.setAttribute('aria-label', 'Remove media item ' + (index + 1));
      remove.addEventListener('click', function () {
        if (item.url) URL.revokeObjectURL(item.url);
        items.splice(index, 1);
        render();
      });
      card.appendChild(remove);

      var order = document.createElement('div');
      order.className = 'media-manager__order';
      order.appendChild(orderButton(index, -1, '&uarr;', 'Move media item ' + (index + 1) + ' earlier'));
      order.appendChild(orderButton(index, 1, '&darr;', 'Move media item ' + (index + 1) + ' later'));
      card.appendChild(order);

      // Hidden inputs submit the ordered gallery — array keys carry position.
      if (item.path) {
        var existing = document.createElement('input');
        existing.type = 'hidden';
        existing.name = 'existing_media[' + index + ']';
        existing.value = item.type + '|' + item.path;
        card.appendChild(existing);
      } else if (item.file) {
        var fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.name = 'media_files[' + index + ']';
        fileInput.className = 'media-manager__field-input';
        try { fileInput.files = dataTransferFiles(item.file); } catch (error) { /* old browser */ }
        card.appendChild(fileInput);

        var typeInput = document.createElement('input');
        typeInput.type = 'hidden';
        typeInput.name = 'media_types[' + index + ']';
        typeInput.value = item.type;
        card.appendChild(typeInput);
      }

      card.addEventListener('dragstart', function (event) {
        dragIndex = index;
        card.classList.add('is-dragging');
        event.dataTransfer.effectAllowed = 'move';
      });
      card.addEventListener('dragend', function () {
        dragIndex = -1;
        render();
      });
      card.addEventListener('dragover', function (event) {
        event.preventDefault();
        card.classList.add('is-drop-target');
      });
      card.addEventListener('dragleave', function () {
        card.classList.remove('is-drop-target');
      });
      card.addEventListener('drop', function (event) {
        event.preventDefault();
        card.classList.remove('is-drop-target');
        if (dragIndex === -1 || dragIndex === index) return;
        var moved = items.splice(dragIndex, 1)[0];
        items.splice(index, 0, moved);
        dragIndex = -1;
        render();
      });

      list.appendChild(card);
    });
  }

  ['image', 'video'].forEach(function (type) {
    var button = manager.querySelector('[data-media-add="' + type + '"]');
    var picker = manager.querySelector('[data-media-picker="' + type + '"]');
    if (!button || !picker) return;

    button.addEventListener('click', function () {
      if (items.length >= maxItems) {
        alert('A product can have at most ' + maxItems + ' media items.');
        return;
      }
      picker.value = '';
      picker.click();
    });

    picker.addEventListener('change', function () {
      var file = picker.files && picker.files[0];
      if (!file) return;

      var limits = LIMITS[type];
      var extension = extensionOf(file.name);

      if (limits.extensions.indexOf(extension) === -1) {
        alert(type === 'video'
          ? 'Videos must be MP4 or WebM files.'
          : 'Images must be JPG, PNG or WebP files.');
        return;
      }
      if (file.size > limits.maxBytes) {
        alert(type === 'video'
          ? 'Videos may not be larger than 20MB.'
          : 'Images may not be larger than 4MB.');
        return;
      }
      if (items.length >= maxItems) {
        alert('A product can have at most ' + maxItems + ' media items.');
        return;
      }

      items.push({ type: type, file: file, url: URL.createObjectURL(file) });
      render();
    });
  });

  render();
})();
