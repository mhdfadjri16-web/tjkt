<?php
require_once __DIR__ . '/../includes/init.php';

$page_title = 'Walk-in Lab';
$page_desc  = 'Jalan-jalan keliling laboratorium komputer Jurusan TJKT lewat simulator 3D: pakai keyboard di komputer atau layar sentuh di HP.';
$active     = 'walkin';
$extra_css  = [asset('css/walkin.css')];
$extra_js   = [asset('js/walkin.js')];

$sim_url     = asset('walkin/index.html');
$preview_url = asset('img/walkin-preview.jpg');

$svg = fn(string $d) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
require __DIR__ . '/../includes/header.php';
?>
<main>
  <section class="page-head container">
    <h1>Walk-in lab</h1>
    <p>Jalan-jalan keliling laboratorium <?= h(site('singkatan')) ?> dari layar. Masuk lewat pintu, lihat deretan komputer, lalu kunjungi ruangan di baliknya.</p>
  </section>

  <section class="section">
    <div class="container">
      <div class="walkin-stage glass" id="walkin" data-src="<?= h($sim_url) ?>">
        <iframe id="walkin-frame" title="Simulator walk-in lab <?= h(site('singkatan')) ?>" allow="fullscreen" allowfullscreen referrerpolicy="no-referrer"></iframe>

        <div class="walkin-start" id="walkin-start">
          <div class="walkin-start-bg" style="background-image:url('<?= h($preview_url) ?>')"></div>
          <div class="walkin-start-card glass">
            <h2>Masuk ke lab <?= h(site('singkatan')) ?></h2>
            <p>Simulator 3D ini berjalan di browser, tanpa pasang aplikasi. Muat pertama kali butuh beberapa detik.</p>
            <button class="btn btn-primary" id="walkin-go" type="button">Mulai walk-in</button>
          </div>
        </div>
      </div>

      <div class="walkin-bar">
        <p class="muted" id="walkin-hint">Setelah mulai, klik sekali di dalam simulator supaya tombol keyboard terbaca.</p>
        <div class="walkin-actions">
          <button class="btn btn-sm" id="walkin-fs" type="button"><?= $svg('<path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/>') ?> Layar penuh</button>
          <a class="btn btn-sm" href="<?= h($sim_url) ?>" target="_blank" rel="noopener">Buka di tab baru</a>
        </div>
      </div>
      <noscript><p class="card glass">Walk-in butuh JavaScript. <a href="<?= h($sim_url) ?>">Buka simulator langsung</a>.</p></noscript>
    </div>
  </section>

  <section class="section" style="padding-top:0">
    <div class="container">
      <div class="section-head"><div><h2>Cara memakai</h2><p>Dua cara, sesuai perangkatmu.</p></div></div>
      <div class="grid grid-2">
        <article class="card glass">
          <h3>Komputer atau laptop</h3>
          <ul class="howto">
            <li><span class="chip">W A S D</span><span>Jalan maju, kiri, mundur, kanan. Tombol panah juga bisa.</span></li>
            <li><span class="chip">Mouse</span><span>Tahan dan geser untuk melihat sekeliling.</span></li>
            <li><span class="chip">Shift</span><span>Tahan untuk berlari.</span></li>
          </ul>
        </article>
        <article class="card glass">
          <h3>HP atau tablet</h3>
          <ul class="howto">
            <li><span class="chip">Landscape</span><span>Putar HP ke mode mendatar. Di iPhone, matikan kunci rotasi dulu.</span></li>
            <li><span class="chip">Kiri</span><span>Geser jari di sisi kiri layar untuk berjalan.</span></li>
            <li><span class="chip">Kanan</span><span>Geser di sisi kanan untuk melihat sekeliling. Tombol <b>Lari</b> ada di kanan bawah.</span></li>
          </ul>
        </article>
      </div>
    </div>
  </section>

  <section class="section" style="padding-top:0">
    <div class="container">
      <div class="section-head"><div><h2>Ruangan yang bisa dikunjungi</h2><p>Nama ruangan tampil di pojok kiri bawah simulator saat kamu berpindah.</p></div></div>
      <div class="grid grid-3">
        <article class="card glass"><h3>Ruang A</h3><p class="muted">Lab komputer utama dengan deretan meja siswa, meja guru, layar besar, dan bagan struktur di dinding.</p></article>
        <article class="card glass"><h3>Ruang B</h3><p class="muted">Area sofa untuk duduk santai. Masuk lewat pintu di bagian depan lab.</p></article>
        <article class="card glass"><h3>Ruang C</h3><p class="muted">Ruang kerja dengan meja, lemari, dan perlengkapan. Lanjut dari Ruang B lewat sekat.</p></article>
      </div>
      <p class="muted walkin-note">Ini model 3D yang menggambarkan tata letak lab. Detail kecil bisa sedikit berbeda dari aslinya.</p>
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
