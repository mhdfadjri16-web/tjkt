/* Halaman Walk-in: muat simulator saat tombol ditekan, plus kontrol layar penuh. */
(function () {
  var stage = document.getElementById('walkin');
  if (!stage) return;
  var frame = document.getElementById('walkin-frame');
  var start = document.getElementById('walkin-start');
  var go = document.getElementById('walkin-go');
  var fsBtn = document.getElementById('walkin-fs');
  var src = stage.getAttribute('data-src');
  var loaded = false;

  function load() {
    if (loaded) return;
    loaded = true;
    frame.addEventListener('load', function () {
      try { frame.contentWindow.focus(); } catch (e) {}
    });
    frame.src = src;
    start.hidden = true;
    try { frame.focus(); } catch (e) {}
  }

  go.addEventListener('click', load);

  fsBtn.addEventListener('click', function () {
    load();
    var el = stage;
    var req = el.requestFullscreen || el.webkitRequestFullscreen;
    if (req) {
      var p = req.call(el);
      if (p && p.catch) p.catch(function () { window.open(src, '_blank', 'noopener'); });
      try {
        if (screen.orientation && screen.orientation.lock) screen.orientation.lock('landscape').catch(function () {});
      } catch (e) {}
    } else {
      // iPhone/iPad Safari tidak bisa layar penuh untuk elemen biasa: buka halaman simulator sendiri.
      window.open(src, '_blank', 'noopener');
    }
  });

  document.addEventListener('fullscreenchange', function () {
    if (document.fullscreenElement === stage) {
      try { frame.contentWindow.focus(); } catch (e) {}
    }
  });
})();
