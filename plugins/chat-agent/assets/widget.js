/* Widget chat-agent: UI saja. Jawaban datang dari api.php. */
(function () {
  'use strict';
  var root = document.getElementById('chat-agent');
  if (!root) return;
  var api = root.dataset.api, wa = root.dataset.wa;
  var started = false;

  function el(tag, cls, text) {
    var e = document.createElement(tag);
    if (cls) e.className = cls;
    if (text) e.textContent = text;
    return e;
  }

  var fab = el('button', 'ca-fab');
  fab.type = 'button';
  fab.setAttribute('aria-label', 'Buka chat bantuan');
  fab.setAttribute('aria-expanded', 'false');
  fab.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1.1-4.6A8 8 0 1 1 21 12z"/></svg>';

  var panel = el('div', 'ca-panel glass');
  panel.setAttribute('role', 'dialog');
  panel.setAttribute('aria-label', 'Chat bantuan');
  var head = el('div', 'ca-head');
  var title = el('div'); title.innerHTML = '<strong>Tanya TJKT</strong><small>Jawaban otomatis</small>';
  var close = el('button', 'ca-x', '\u00d7'); close.type = 'button'; close.setAttribute('aria-label', 'Tutup chat');
  head.appendChild(title); head.appendChild(close);
  var log = el('div', 'ca-log'); log.setAttribute('aria-live', 'polite');
  var chips = el('div', 'ca-chips');
  var form = el('form', 'ca-form');
  var input = el('input'); input.type = 'text'; input.maxLength = 300; input.placeholder = 'Tulis pertanyaan...'; input.setAttribute('aria-label', 'Pertanyaan');
  var send = el('button', 'ca-send', 'Kirim'); send.type = 'submit';
  form.appendChild(input); form.appendChild(send);
  var waLink = el('a', 'ca-wa', 'Hubungi admin lewat WhatsApp'); waLink.href = wa; waLink.target = '_blank'; waLink.rel = 'noopener';
  panel.appendChild(head); panel.appendChild(log); panel.appendChild(chips); panel.appendChild(form); panel.appendChild(waLink);
  root.appendChild(fab); root.appendChild(panel);

  function add(text, who) { var m = el('div', 'ca-msg ' + (who === 'me' ? 'ca-me' : 'ca-bot'), text); log.appendChild(m); log.scrollTop = log.scrollHeight; return m; }
  function setChips(list) {
    chips.innerHTML = '';
    (list || []).forEach(function (q) {
      var c = el('button', 'ca-chip', q); c.type = 'button';
      c.addEventListener('click', function () { ask(q); });
      chips.appendChild(c);
    });
  }
  function ask(q) {
    add(q, 'me'); setChips([]);
    var t = el('div', 'ca-msg ca-bot ca-typing'); t.innerHTML = '<span></span><span></span><span></span>'; log.appendChild(t); log.scrollTop = log.scrollHeight;
    var fd = new FormData(); fd.append('message', q);
    fetch(api, { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (d) { t.remove(); add(d.text || 'Maaf, terjadi kendala.', 'bot'); setChips(d.suggestions); })
      .catch(function () { t.remove(); add('Maaf, chat sedang bermasalah. Coba lewat WhatsApp ya.', 'bot'); });
  }
  function open() {
    panel.classList.add('is-open'); fab.setAttribute('aria-expanded', 'true'); input.focus();
    if (started) return; started = true;
    fetch(api).then(function (r) { return r.json(); }).then(function (d) {
      add(d.sapaan || 'Halo!', 'bot'); setChips(d.populer);
    }).catch(function () { add('Halo! Chat sedang bermasalah, silakan hubungi admin lewat WhatsApp.', 'bot'); });
  }
  function shut() { panel.classList.remove('is-open'); fab.setAttribute('aria-expanded', 'false'); fab.focus(); }
  fab.addEventListener('click', function () { panel.classList.contains('is-open') ? shut() : open(); });
  close.addEventListener('click', shut);
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && panel.classList.contains('is-open')) shut(); });
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var q = input.value.trim(); if (!q) return;
    input.value = ''; ask(q);
  });
})();
