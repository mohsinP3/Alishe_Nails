document.addEventListener('DOMContentLoaded', function () {
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
    });
  });

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
