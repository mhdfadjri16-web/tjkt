/* Tur Virtual Lab: berpindah antar foto dengan efek "berjalan". Vanilla JS. */
(function () {
  'use strict';
  var dataEl = document.getElementById('lab-data');
  if (!dataEl) return;
  var D = JSON.parse(dataEl.textContent), S = D.scenes, Z = D.zones;
  var $ = function (id) { return document.getElementById(id); };
  var stage = $('stage'), layers = $('layers'), capZone = $('cap-zone'), capTitle = $('cap-title');
  var counter = $('counter'), progress = $('progress'), intro = $('intro'), strip = $('strip');
  var bPrev = $('t-prev'), bNext = $('t-next'), bPlay = $('t-play'), bFs = $('t-fs'), sPrev = $('s-prev'), sNext = $('s-next');
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var cur = -1, timer = null, zoneName = {}, thumbs = [];
  Z.forEach(function (z) { zoneName[z.id] = z.judul; });

  var ICON_NAV = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>';
  var ICON_INFO = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 11v6M12 7.5v.01"/></svg>';

  function fit(layer, s) {
    var W = stage.clientWidth, H = stage.clientHeight, ar = s.w / s.h, w, h;
    if (W / H > ar) { h = H; w = h * ar; } else { w = W; h = w / ar; }
    var f = layer.querySelector('.tour-frame');
    f.style.width = w + 'px'; f.style.height = h + 'px';
  }

  function clearTips() { stage.querySelectorAll('.hot-card').forEach(function (c) { c.remove(); }); }

  function showTip(frame, h) {
    clearTips();
    var c = document.createElement('div');
    c.className = 'hot-card' + (h.y < 30 ? ' low' : (h.x > 70 ? ' l' : (h.x < 30 ? ' r' : '')));
    c.style.left = h.x + '%'; c.style.top = h.y + '%';
    var b = document.createElement('b'); b.textContent = h.judul;
    var t = document.createElement('span'); t.textContent = h.teks;
    c.appendChild(b); c.appendChild(t);
    c.addEventListener('click', function (e) { e.stopPropagation(); });
    frame.appendChild(c);
  }

  function hotEl(h, frame) {
    var b = document.createElement('button');
    b.type = 'button';
    var nav = h.tipe === 'pindah';
    b.className = 'hot ' + (nav ? 'hot-nav' : 'hot-info');
    b.style.left = h.x + '%'; b.style.top = h.y + '%';
    b.setAttribute('aria-label', nav ? h.label : h.judul);
    b.innerHTML = nav ? ICON_NAV : ICON_INFO;
    if (nav) {
      var l = document.createElement('span'); l.className = 'hot-label'; l.textContent = h.label; b.appendChild(l);
      b.addEventListener('click', function (e) { e.stopPropagation(); go(h.ke - 1, 'fwd'); });
    } else {
      b.addEventListener('click', function (e) { e.stopPropagation(); showTip(frame, h); });
    }
    return b;
  }

  function build(i) {
    var s = S[i], layer = document.createElement('div');
    layer.className = 'tour-layer'; layer.dataset.i = i;
    var bg = document.createElement('div'); bg.className = 'tour-bgi'; bg.style.backgroundImage = 'url("' + s.src + '")';
    var f = document.createElement('div'); f.className = 'tour-frame';
    var img = new Image(); img.src = s.src; img.alt = s.judul; img.draggable = false; img.className = 'tour-img';
    f.appendChild(img);
    (s.hot || []).forEach(function (h) { f.appendChild(hotEl(h, f)); });
    layer.appendChild(bg); layer.appendChild(f);
    return layer;
  }

  function preload() {
    [cur + 1, cur + 2, cur - 1].forEach(function (k) { if (S[k]) { var im = new Image(); im.src = S[k].src; } });
  }

  function update() {
    var s = S[cur];
    capZone.textContent = zoneName[s.zona] || '';
    capTitle.textContent = s.judul;
    counter.textContent = (cur + 1) + ' / ' + S.length;
    progress.style.width = ((cur + 1) / S.length * 100) + '%';
    bPrev.disabled = sPrev.disabled = cur <= 0;
    bNext.disabled = sNext.disabled = cur >= S.length - 1;
    thumbs.forEach(function (t, k) {
      var on = k === cur; t.classList.toggle('is-cur', on);
      if (on) t.setAttribute('aria-current', 'true'); else t.removeAttribute('aria-current');
    });
    var t = thumbs[cur];
    if (t) strip.scrollLeft = t.offsetLeft - strip.clientWidth / 2 + t.clientWidth / 2;
    try { history.replaceState(null, '', '#scene-' + (cur + 1)); } catch (e) {}
  }

  function go(i, dir) {
    if (i < 0 || i >= S.length || i === cur) return;
    dir = dir || (i > cur ? 'fwd' : 'back');
    clearTips();
    var old = layers.querySelector('.tour-layer.on');
    var layer = build(i);
    fit(layer, S[i]);
    layer.classList.add('pre-' + dir);
    layers.appendChild(layer);
    void layer.offsetWidth;
    layer.classList.remove('pre-' + dir);
    layer.classList.add('on');
    if (old) {
      old.classList.remove('on'); old.classList.add('leave-' + dir);
      setTimeout(function () { old.remove(); }, 1000);
    }
    cur = i; update(); preload();
  }

  function next() { go(cur + 1, 'fwd'); }
  function prev() { go(cur - 1, 'back'); }

  function stop() {
    if (timer) { clearInterval(timer); timer = null; }
    bPlay.classList.remove('is-on'); bPlay.setAttribute('aria-pressed', 'false');
    bPlay.setAttribute('aria-label', 'Jalan otomatis');
  }
  function play() {
    if (timer) return;
    if (cur >= S.length - 1) go(0, 'back');
    bPlay.classList.add('is-on'); bPlay.setAttribute('aria-pressed', 'true');
    bPlay.setAttribute('aria-label', 'Hentikan jalan otomatis');
    timer = setInterval(function () { if (cur >= S.length - 1) { stop(); return; } next(); }, 4800);
  }

  function start(i) {
    intro.classList.add('hide');
    go(i || 0, 'fwd');
    stage.focus({ preventScroll: true });
  }

  function toggleFs() {
    var d = document;
    if (d.fullscreenElement || d.webkitFullscreenElement) { (d.exitFullscreen || d.webkitExitFullscreen).call(d); return; }
    var fn = stage.requestFullscreen || stage.webkitRequestFullscreen;
    if (fn) fn.call(stage);
  }

  /* Filmstrip dan tombol zona */
  Z.forEach(function (z) {
    var g = document.createElement('div'); g.className = 'strip-zone';
    var lab = document.createElement('span'); lab.className = 'strip-label'; lab.textContent = z.judul; g.appendChild(lab);
    S.forEach(function (s, k) {
      if (s.zona !== z.id) return;
      var b = document.createElement('button'); b.type = 'button'; b.className = 'strip-thumb';
      b.setAttribute('aria-label', (k + 1) + '. ' + s.judul);
      b.innerHTML = '<img loading="lazy" alt="" src="' + s.thumb + '"><i>' + (k + 1) + '</i>';
      b.addEventListener('click', function () { if (cur < 0) start(k); else go(k); });
      thumbs[k] = b; g.appendChild(b);
    });
    strip.appendChild(g);
  });
  [$('zones'), $('intro-zones')].forEach(function (box) {
    if (!box) return;
    Z.forEach(function (z) {
      var first = S.findIndex(function (s) { return s.zona === z.id; });
      var b = document.createElement('button'); b.type = 'button'; b.className = 'btn btn-sm'; b.textContent = z.judul;
      b.addEventListener('click', function () { if (cur < 0) start(first); else go(first); });
      box.appendChild(b);
    });
  });

  /* Kontrol */
  bNext.addEventListener('click', function () { stop(); next(); });
  sNext.addEventListener('click', function () { stop(); next(); });
  bPrev.addEventListener('click', function () { stop(); prev(); });
  sPrev.addEventListener('click', function () { stop(); prev(); });
  bPlay.addEventListener('click', function () { timer ? stop() : play(); });
  bFs.addEventListener('click', toggleFs);
  $('start').addEventListener('click', function () { start(0); });
  stage.addEventListener('click', clearTips);

  var tour = $('tour');
  tour.addEventListener('keydown', function (e) {
    if (cur < 0 || e.target.matches('input, textarea')) return;
    var k = e.key;
    if (k === 'ArrowRight' || k === 'ArrowUp') { e.preventDefault(); stop(); next(); }
    else if (k === 'ArrowLeft' || k === 'ArrowDown') { e.preventDefault(); stop(); prev(); }
    else if (k === ' ' && !e.target.matches('button')) { e.preventDefault(); timer ? stop() : play(); }
    else if (k === 'f' || k === 'F') { toggleFs(); }
    else if (k === 'Home') { e.preventDefault(); stop(); go(0, 'back'); }
    else if (k === 'End') { e.preventDefault(); stop(); go(S.length - 1, 'fwd'); }
    else if (k === 'Escape') { clearTips(); }
  });

  /* Geser jari di layar sentuh */
  var sx = null;
  stage.addEventListener('pointerdown', function (e) {
    if (e.pointerType === 'mouse' || e.target.closest('button, .hot-card')) return;
    sx = e.clientX;
  });
  stage.addEventListener('pointerup', function (e) {
    if (sx === null) return;
    var dx = e.clientX - sx; sx = null;
    if (Math.abs(dx) > 50) { stop(); dx < 0 ? next() : prev(); }
  });
  stage.addEventListener('pointercancel', function () { sx = null; });

  /* Efek geser halus mengikuti kursor (hanya mouse) */
  if (!reduce) {
    stage.addEventListener('pointermove', function (e) {
      if (e.pointerType !== 'mouse') return;
      var r = stage.getBoundingClientRect(), nx = (e.clientX - r.left) / r.width - .5, ny = (e.clientY - r.top) / r.height - .5;
      var f = layers.querySelector('.tour-layer.on .tour-frame');
      if (f) { f.style.setProperty('--px', (-nx * 16) + 'px'); f.style.setProperty('--py', (-ny * 10) + 'px'); }
    });
    stage.addEventListener('pointerleave', function () {
      var f = layers.querySelector('.tour-layer.on .tour-frame');
      if (f) { f.style.setProperty('--px', '0px'); f.style.setProperty('--py', '0px'); }
    });
  }

  function refit() { layers.querySelectorAll('.tour-layer').forEach(function (l) { fit(l, S[l.dataset.i]); }); }
  if (window.ResizeObserver) new ResizeObserver(refit).observe(stage); else window.addEventListener('resize', refit);
  document.addEventListener('fullscreenchange', function () { bFs.classList.toggle('is-on', !!document.fullscreenElement); });

  /* Tautan langsung ke scene tertentu: pagina#scene-7 */
  var m = /^#scene-(\d+)$/.exec(location.hash);
  if (m && S[+m[1] - 1]) start(+m[1] - 1);
})();
