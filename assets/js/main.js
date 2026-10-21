/* Website Jurusan TJKT - JS publik (vanilla) */
(function () {
  'use strict';

  /* Menu mobile */
  var toggle = document.querySelector('.nav-toggle');
  var menu = document.getElementById('nav-menu');
  if (toggle && menu) {
    toggle.addEventListener('click', function () {
      var open = menu.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
    });
  }

  /* Filter galeri */
  var tabs = document.querySelectorAll('.tab[data-filter]');
  var items = document.querySelectorAll('.gallery .g-item');
  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      var f = tab.getAttribute('data-filter');
      tabs.forEach(function (t) { t.setAttribute('aria-pressed', t === tab ? 'true' : 'false'); });
      items.forEach(function (it) {
        var show = f === 'semua' || it.dataset.tipe === f || it.dataset.kategori === f;
        it.hidden = !show;
      });
    });
  });

  /* Lightbox foto dan video */
  var modal, box, closeBtn, lastFocus;
  function buildModal() {
    modal = document.createElement('div');
    modal.className = 'modal';
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('aria-label', 'Pratinjau galeri');
    modal.innerHTML = '<div class="modal-box glass"><button type="button" class="modal-close" aria-label="Tutup">&times;</button><div class="modal-media"></div><p></p></div>';
    document.body.appendChild(modal);
    box = modal.querySelector('.modal-media');
    closeBtn = modal.querySelector('.modal-close');
    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
  }
  function openModal(item) {
    if (!modal) buildModal();
    lastFocus = document.activeElement;
    box.innerHTML = '';
    var yt = item.dataset.yt;
    if (yt) {
      var f = document.createElement('iframe');
      f.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(yt) + '?autoplay=1&rel=0';
      f.allow = 'autoplay; encrypted-media; picture-in-picture';
      f.allowFullscreen = true;
      f.title = item.dataset.caption || 'Video';
      box.appendChild(f);
    } else {
      var src = item.querySelector('.art, .media');
      if (src) box.appendChild(src.cloneNode(true));
    }
    modal.querySelector('p').textContent = item.dataset.caption || '';
    modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    closeBtn.focus();
  }
  function closeModal() {
    if (!modal) return;
    modal.classList.remove('is-open');
    box.innerHTML = '';   /* hentikan video */
    document.body.style.overflow = '';
    if (lastFocus) lastFocus.focus();
  }
  document.querySelectorAll('.g-item').forEach(function (it) {
    it.addEventListener('click', function () { openModal(it); });
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      closeModal();
      if (menu && menu.classList.contains('is-open')) { toggle.click(); toggle.focus(); }
    }
  });
})();
