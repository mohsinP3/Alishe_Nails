document.addEventListener('DOMContentLoaded', function () {
  var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  function showToast(message, type) {
    var toast = document.createElement('div');
    toast.className = 'site-toast' + (type === 'error' ? ' site-toast--error' : '');
    toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
    toast.textContent = message;
    document.body.appendChild(toast);
    requestAnimationFrame(function () { toast.classList.add('is-visible'); });
    setTimeout(function () {
      toast.classList.remove('is-visible');
      setTimeout(function () { toast.remove(); }, 250);
    }, 3200);
  }

  function updateCartCount(count) {
    document.querySelectorAll('.navbar__cart-count').forEach(function (element) {
      element.textContent = count;
    });
  }

  function bindImageFallbacks() {
    document.querySelectorAll('[data-image-fallback]').forEach(function (image) {
      if (image.dataset.fallbackBound) return;
      image.dataset.fallbackBound = 'true';
      image.addEventListener('error', function () {
        var placeholder = document.createElement('div');
        placeholder.className = 'img-placeholder';
        placeholder.innerHTML = 'Alishe Nails<br>Image unavailable';
        image.replaceWith(placeholder);
      });
    });
  }

  function bindWishlist() {
    document.querySelectorAll('[data-wishlist-id]').forEach(function (button) {
      if (button.dataset.wishlistBound) return;
      button.dataset.wishlistBound = 'true';
      button.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        var id = button.dataset.wishlistId;
        var wishlist = JSON.parse(localStorage.getItem('alishe_wishlist') || '[]');
        var index = wishlist.indexOf(id);
        var added = index === -1;

        if (added) wishlist.push(id);
        else wishlist.splice(index, 1);

        localStorage.setItem('alishe_wishlist', JSON.stringify(wishlist));
        button.setAttribute('aria-pressed', added ? 'true' : 'false');
        button.setAttribute('aria-label', (added ? 'Remove ' : 'Add ') + 'item ' + (added ? 'from' : 'to') + ' wishlist');
        var icon = button.querySelector('i');
        if (icon) icon.className = added ? 'fa-solid fa-heart' : 'fa-regular fa-heart';
        showToast(added ? 'Added to your wishlist.' : 'Removed from your wishlist.');
      });

      var saved = JSON.parse(localStorage.getItem('alishe_wishlist') || '[]');
      var isSaved = saved.indexOf(button.dataset.wishlistId) !== -1;
      button.setAttribute('aria-pressed', isSaved ? 'true' : 'false');
      var icon = button.querySelector('i');
      if (icon && isSaved) icon.className = 'fa-solid fa-heart';
    });
  }

  function bindShopAjax() {
    var shopLayout = document.querySelector('.shop-layout');
    if (!shopLayout || shopLayout.dataset.ajaxBound) return;
    shopLayout.dataset.ajaxBound = 'true';

    function loadShop(url, pushHistory) {
      shopLayout.classList.add('is-loading');
      return fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (response) {
          if (!response.ok) throw new Error('Shop request failed');
          return response.text();
        })
        .then(function (html) {
          var parsed = new DOMParser().parseFromString(html, 'text/html');
          var nextLayout = parsed.querySelector('.shop-layout');
          if (!nextLayout) throw new Error('Shop content unavailable');
          shopLayout.innerHTML = nextLayout.innerHTML;
          if (pushHistory) window.history.pushState({}, '', url);
          bindShopAjax();
          bindWishlist();
          bindImageFallbacks();
        })
        .catch(function () { showToast('Unable to update the collection. Please try again.', 'error'); })
        .finally(function () { shopLayout.classList.remove('is-loading'); });
    }

      shopLayout.loadShop = loadShop;

    shopLayout.addEventListener('submit', function (event) {
      var form = event.target.closest('[data-shop-form]');
      if (!form) return;
      event.preventDefault();
      loadShop(form.action + '?' + new URLSearchParams(new FormData(form)).toString(), true);
    });

    shopLayout.addEventListener('click', function (event) {
      var link = event.target.closest('.pagination a, .filter-chip__remove');
      if (!link) return;
      event.preventDefault();
      loadShop(link.href, true);
    });
  }

  document.addEventListener('submit', function (event) {
    var form = event.target.closest('form[action*="/cart/add/"]');
    if (!form || form.closest('.shop-layout') === null) return;
    event.preventDefault();

    fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken || '',
      },
    })
      .then(function (response) {
        return response.json().then(function (data) {
          if (!response.ok) throw new Error(data.message || 'Unable to add item');
          return data;
        });
      })
      .then(function (data) {
        updateCartCount(data.cart_count);
        showToast(data.message);
      })
      .catch(function (error) { showToast(error.message, 'error'); });
  });

  window.addEventListener('popstate', function () {
    var shopLayout = document.querySelector('.shop-layout');
    if (shopLayout && shopLayout.loadShop) shopLayout.loadShop(window.location.href, false);
  });

  bindImageFallbacks();
  bindWishlist();
  bindShopAjax();

  var galleryInput = document.querySelector('#gallery');
  var galleryFileLabel = document.querySelector('[data-gallery-file-label]');
  if (galleryInput && galleryFileLabel) {
    galleryInput.addEventListener('change', function () {
      var count = galleryInput.files.length;
      galleryFileLabel.textContent = count ? count + ' image' + (count === 1 ? '' : 's') + ' selected' : 'Add gallery images';
    });
  }

  // ---------- Mobile navbar toggle ----------
  var navbar = document.querySelector('.navbar');
  var navToggle = document.querySelector('.navbar__toggle');
  if (navToggle && navbar) {
    navToggle.addEventListener('click', function () {
      navbar.classList.toggle('is-open');
    });
  }

  // ---------- Shop: mobile filters toggle ----------
  var filtersToggle = document.querySelector('[data-filters-toggle]');
  var filtersPanel = document.querySelector('.filters');
  if (filtersToggle && filtersPanel) {
    filtersToggle.addEventListener('click', function () {
      filtersPanel.classList.toggle('is-open');
    });
  }

  // ---------- Product gallery ----------
  var mainImage = document.querySelector('[data-gallery-main] img');
  document.querySelectorAll('[data-gallery-thumb]').forEach(function (thumb) {
    thumb.addEventListener('click', function () {
      if (!mainImage) return;
      document.querySelectorAll('[data-gallery-thumb]').forEach(function (t) {
        t.classList.remove('is-active');
      });
      thumb.classList.add('is-active');
      mainImage.src = thumb.dataset.fullImage;
    });
  });

  var galleryMain = document.querySelector('[data-gallery-main]');
  if (galleryMain) {
    galleryMain.addEventListener('click', function () { galleryMain.classList.toggle('is-zoomed'); });
    galleryMain.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        galleryMain.classList.toggle('is-zoomed');
      }
    });
  }

  // ---------- Accessible sizing and content modals ----------
  var openModal = function (id) {
    var modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    var closeButton = modal.querySelector('.modal__close');
    if (closeButton) closeButton.focus();
  };
  var closeModal = function (modal) {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  };
  document.querySelectorAll('[data-modal-open]').forEach(function (button) {
    button.addEventListener('click', function () { openModal(button.dataset.modalOpen); });
  });
  document.querySelectorAll('[data-modal-close]').forEach(function (button) {
    button.addEventListener('click', function () { closeModal(button.closest('[data-modal]')); });
  });
  document.addEventListener('keydown', function (event) {
    if (event.key !== 'Escape') return;
    var modal = document.querySelector('[data-modal].is-open');
    if (modal) closeModal(modal);
  });

  // ---------- Option pills (shape / size) ----------
  document.querySelectorAll('[data-option-group]').forEach(function (group) {
    var pills = group.querySelectorAll('.option-pill');
    var hiddenInput = group.querySelector('input[type=hidden]');
    pills.forEach(function (pill) {
      pill.addEventListener('click', function () {
        pills.forEach(function (p) { p.classList.remove('is-selected'); });
        pill.classList.add('is-selected');
        if (hiddenInput) hiddenInput.value = pill.dataset.value;
      });
    });
  });

  // ---------- Quantity selector (product page + cart) ----------
  document.querySelectorAll('[data-qty-selector]').forEach(function (selector) {
    var input = selector.querySelector('input');
    var min = parseInt(input.min || '1', 10);
    var max = parseInt(input.max || '20', 10);

    selector.querySelectorAll('button').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var value = parseInt(input.value || '1', 10);
        value = btn.dataset.action === 'increase' ? value + 1 : value - 1;
        value = Math.max(min, Math.min(max, value));
        input.value = value;

        if (selector.dataset.autoSubmit === 'true') {
          selector.closest('form').requestSubmit();
        }
      });
    });
  });

  // ---------- Payment method selection ----------
  document.querySelectorAll('.payment-option').forEach(function (option) {
    option.addEventListener('click', function () {
      document.querySelectorAll('.payment-option').forEach(function (o) {
        o.classList.remove('is-selected');
      });
      option.classList.add('is-selected');
      option.querySelector('input[type=radio]').checked = true;
      updateTransactionReferenceField();
    });
  });

  // ---------- Checkout shipping and payment details ----------
  var checkoutCity = document.querySelector('#city');
  var checkoutArea = document.querySelector('#area');
  var shippingAmount = document.querySelector('#checkout-shipping-amount');
  var totalAmount = document.querySelector('#checkout-total-amount');

  function updateTransactionReferenceField() {
    var referenceContainer = document.querySelector('[data-transaction-reference]');
    var referenceInput = document.querySelector('#transaction_reference');
    var selectedPayment = document.querySelector('input[name="payment_method"]:checked');
    var needsReference = selectedPayment && selectedPayment.value !== 'cod';

    if (!referenceContainer || !referenceInput) return;
    referenceContainer.style.display = needsReference ? 'block' : 'none';
    referenceInput.disabled = !needsReference;
  }

  function updateCheckoutShipping() {
    if (!checkoutCity || !checkoutArea || !shippingAmount || !totalAmount) return;

    var params = new URLSearchParams({
      city: checkoutCity.value,
      area: checkoutArea.value,
    });

    fetch('/checkout/shipping-fee?' + params.toString(), {
      headers: { 'Accept': 'application/json' },
    })
      .then(function (response) { return response.json(); })
      .then(function (data) {
        shippingAmount.textContent = data.shipping == 0 ? 'Free' : 'PKR ' + Number(data.shipping).toLocaleString();
        totalAmount.textContent = 'PKR ' + Number(data.total).toLocaleString();
      })
      .catch(function () {
        // Keep the server-rendered amounts if the live estimate is unavailable.
      });
  }

  if (checkoutCity && checkoutArea) {
    checkoutCity.addEventListener('change', updateCheckoutShipping);
    checkoutArea.addEventListener('change', updateCheckoutShipping);
  }
  updateTransactionReferenceField();

  // ---------- Accordion chevrons on product page ----------
  document.querySelectorAll('.accordion-item').forEach(function (item) {
    item.addEventListener('toggle', function () {
      var icon = item.querySelector('.chevron');
      if (icon) icon.style.transform = item.open ? 'rotate(180deg)' : 'rotate(0deg)';
    });
  });

  // ---------- Navbar search toggle ----------
  var searchToggle = document.querySelectorAll('[data-search-toggle]');
  var searchForm = document.querySelector('[data-search-form]');
  var searchClose = document.querySelector('[data-search-close]');

  searchToggle.forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (!searchForm) return;
      var isOpen = searchForm.style.display !== 'none';
      searchForm.style.display = isOpen ? 'none' : 'flex';
      if (!isOpen) {
        var input = searchForm.querySelector('input[type=text]');
        if (input) input.focus();
      }
    });
  });

  if (searchClose && searchForm) {
    searchClose.addEventListener('click', function () {
      searchForm.style.display = 'none';
    });
  }

  // ---------- Navbar account menu dropdown ----------
  var accountToggle = document.querySelector('[data-account-toggle]');
  var accountMenu = document.querySelector('[data-account-menu]');
  if (accountToggle && accountMenu) {
    accountToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      accountMenu.style.display = accountMenu.style.display === 'none' ? 'block' : 'none';
    });
    document.addEventListener('click', function (e) {
      if (!accountMenu.contains(e.target) && e.target !== accountToggle) {
        accountMenu.style.display = 'none';
      }
    });
  }

  // ---------- Auto-dismiss alerts ----------
  document.querySelectorAll('.alert').forEach(function (alertBox) {
    setTimeout(function () {
      alertBox.style.transition = 'opacity .4s ease';
      alertBox.style.opacity = '0';
      setTimeout(function () { alertBox.remove(); }, 400);
    }, 4000);
  });
});
