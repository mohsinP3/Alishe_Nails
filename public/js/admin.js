document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.querySelector('[data-sidebar-toggle]');
  var sidebar = document.querySelector('.admin-sidebar');

  if (toggle && sidebar) {
    toggle.addEventListener('click', function () {
      sidebar.classList.toggle('is-open');
    });
  }
});
