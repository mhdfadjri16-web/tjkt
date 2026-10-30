<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/lab-data.php';

$page_title = 'Tur Virtual Lab';
$page_desc = 'Jalan-jalan keliling laboratorium komputer Jurusan TJKT lewat foto: klik panah, geser, atau pakai keyboard.';
$active = 'lab';
$extra_css = [asset('css/lab.css')];
$extra_js = [asset('js/lab.js')];

$zones = lab_zones();
$scenes = lab_scenes();
$payload = [
    'zones' => $zones,
    'scenes' => array_map(fn($s) => [
        'judul' => $s['judul'], 'zona' => $s['zona'], 'w' => $s['w'], 'h' => $s['h'], 'hot' => $s['hot'],
        'src' => asset('img/lab/' . $s['file']), 'thumb' => asset('img/lab/' . $s['thumb']),
    ], $scenes),
];
$hitung = [];
foreach ($scenes as $s) $hitung[$s['zona']] = ($hitung[$s['zona']] ?? 0) + 1;

$svg = fn(string $d) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
require __DIR__ . '/../includes/header.php';
?>
<main>
  <section class="page-head container">
    <h1>Tur virtual lab</h1>
    <p>Jalan-jalan keliling laboratorium <?= h(site('singkatan')) ?> dari layar. Klik panah, geser layar, atau pakai tombol keyboard.</p>
  </section>

  <section class="section">
    <div class="container tour" id="tour">
      <div class="tour-stage glass" id="stage" tabindex="0" role="region" aria-label="Tur virtual lab. Gunakan tombol panah untuk berjalan maju dan mundur.">
        <div id="layers"></div>

        <div class="tour-cap glass"><small id="cap-zone"></small><b id="cap-title" aria-live="polite"></b></div>
        <div class="tour-count glass" id="counter">0 / <?= count($scenes) ?></div>

        <button class="tbtn tbtn-side" id="s-prev" type="button" aria-label="Mundur" disabled><?= $svg('<path d="M15 5l-7 7 7 7"/>') ?></button>
        <button class="tbtn tbtn-side" id="s-next" type="button" aria-label="Maju"><?= $svg('<path d="M9 5l7 7-7 7"/>') ?></button>

        <div class="tour-bar glass" role="group" aria-label="Kontrol tur">
          <button class="tbtn" id="t-prev" type="button" aria-label="Mundur" disabled><?= $svg('<path d="M15 5l-7 7 7 7"/>') ?></button>
          <button class="tbtn" id="t-play" type="button" aria-label="Jalan otomatis" aria-pressed="false"><?= $svg('<path d="M6 4l14 8-14 8z"/>') ?></button>
          <button class="tbtn" id="t-next" type="button" aria-label="Maju"><?= $svg('<path d="M9 5l7 7-7 7"/>') ?></button>
          <span class="tbar-sep" aria-hidden="true"></span>
          <button class="tbtn" id="t-fs" type="button" aria-label="Layar penuh"><?= $svg('<path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/>') ?></button>
        </div>
        <div class="tour-progress" id="progress"></div>

        <div class="tour-intro" id="intro">
          <div class="tour-intro-bg" style="background-image:url('<?= h(asset('img/lab/' . $scenes[0]['file'])) ?>')"></div>
          <div class="tour-intro-card glass">
            <h2>Masuk ke lab <?= h(site('singkatan')) ?></h2>
            <p>Lihat ruang praktik, deretan komputer, dan ruangan di baliknya, seperti sedang berjalan di dalam lab.</p>
            <div class="tour-intro-actions">
              <button class="btn btn-primary" id="start" type="button">Mulai tur</button>
            </div>
            <div class="tour-intro-actions" id="intro-zones" aria-label="Atau langsung ke ruangan"></div>
          </div>
        </div>
      </div>

      <div class="tour-zones"><span>Loncat ke:</span><span id="zones" style="display:contents"></span></div>
      <div class="tour-strip glass" id="strip" aria-label="Daftar foto"></div>

      <script type="application/json" id="lab-data"><?= json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
      <noscript>
        <div class="grid grid-3" style="margin-top:1rem">
          <?php foreach ($scenes as $s): ?>
            <figure class="card glass" style="margin:0"><img src="<?= h(asset('img/lab/' . $s['file'])) ?>" alt="<?= h($s['judul']) ?>" loading="lazy" style="border-radius:12px"><figcaption><?= h($s['judul']) ?></figcaption></figure>
          <?php endforeach; ?>
        </div>
      </noscript>
    </div>
  </section>

  <section class="section" style="padding-top:0">
    <div class="container">
      <div class="section-head"><div><h2>Isi tur</h2><p><?= count($scenes) ?> foto di <?= count($zones) ?> ruangan.</p></div></div>
      <div class="zone-cards">
        <?php foreach ($zones as $z): ?>
          <article class="card glass">
            <h3><?= h($z['judul']) ?></h3>
            <p class="muted"><?= h($z['deskripsi']) ?></p>
            <span class="chip"><?= (int)($hitung[$z['id']] ?? 0) ?> foto</span>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="section" style="padding-top:0">
    <div class="container">
      <div class="card glass">
        <h3>Cara memakai</h3>
        <ul class="howto">
          <li><span class="chip">Klik</span><span>Tombol panah, atau titik berdenyut di foto untuk pindah ruangan.</span></li>
          <li><span class="chip">Geser</span><span>Di HP, geser foto ke kiri untuk maju dan ke kanan untuk mundur.</span></li>
          <li><span class="chip">Keyboard</span><span><kbd>&larr;</kbd> <kbd>&rarr;</kbd> berjalan, <kbd>Spasi</kbd> jalan otomatis, <kbd>F</kbd> layar penuh.</span></li>
          <li><span class="chip">Info</span><span>Klik titik bertanda <b>i</b> untuk keterangan perangkat di ruangan.</span></li>
        </ul>
      </div>
    </div>
  </section>

  <section class="section" style="padding-top:0">
    <div class="container">
      <div class="cta-band glass">
        <div><h2>Mau lihat langsung?</h2><p>Tanyakan jadwal kunjungan ke admin atau guru <?= h(site('singkatan')) ?>.</p></div>
        <a class="btn btn-primary" href="<?= h(wa_link('Halo, saya ingin berkunjung melihat lab TJKT.')) ?>" target="_blank" rel="noopener">Hubungi lewat WhatsApp</a>
      </div>
    </div>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
