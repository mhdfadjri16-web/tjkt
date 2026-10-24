<?php
$page_title = 'Beranda';
$active = 'beranda';
require __DIR__ . '/includes/header.php';
$kegiatan = kegiatan_list(3);
$agenda   = agenda_list(3);
$galeri   = galeri_list(null, 'foto', 5);
?>
<main>
  <!-- Hero -->
  <section class="hero">
    <div class="container hero-grid">
      <div class="hero-seq">
        <p class="chip"><?= h(site('nama_sekolah')) ?></p>
        <h1>Dari kabel UTP sampai server, semuanya dipelajari langsung.</h1>
        <p class="hero-lead">Jurusan <?= h(site('nama_jurusan')) ?>. Siswa membangun jaringan sungguhan di laboratorium, lalu menerapkannya saat PKL di dunia usaha.</p>
        <div class="hero-cta">
          <a class="btn btn-primary" href="<?= h(url('pages/kegiatan.php')) ?>">Lihat kegiatan terbaru</a>
          <a class="btn" href="<?= h(url('pages/flyer-pkl.php')) ?>">Cari tempat PKL</a>
        </div>
      </div>

      <div class="hero-visual topo glass">
        <svg viewBox="0 0 460 400" role="img" aria-label="Diagram topologi jaringan: internet, router, switch, server, access point, dan komputer">
          <defs>
            <path id="p1" d="M230 62 L230 128"/>
            <path id="p2" d="M230 172 L230 236"/>
            <path id="p3" d="M205 270 L110 330"/>
            <path id="p4" d="M255 270 L350 330"/>
            <path id="p5" d="M230 280 L230 330"/>
          </defs>
          <g class="link">
            <use href="#p1"/><use href="#p2"/><use href="#p3"/><use href="#p4"/><use href="#p5"/>
          </g>
          <g class="node"><rect x="170" y="14" width="120" height="48" rx="16"/><text x="230" y="36">Internet</text><text class="sub" x="230" y="51">ISP</text><circle class="led" cx="276" cy="26" r="3"/></g>
          <g class="node"><rect x="170" y="128" width="120" height="44" rx="16"/><text x="230" y="149">Router</text><text class="sub" x="230" y="163">192.168.1.1</text><circle class="led v" cx="276" cy="140" r="3"/></g>
          <g class="node"><rect x="170" y="236" width="120" height="44" rx="16"/><text x="230" y="257">Switch</text><text class="sub" x="230" y="271">24 port</text><circle class="led" cx="276" cy="248" r="3"/></g>
          <g class="node"><rect x="50" y="330" width="120" height="48" rx="16"/><text x="110" y="351">Server</text><text class="sub" x="110" y="366">Linux</text><circle class="led v" cx="156" cy="342" r="3"/></g>
          <g class="node"><rect x="290" y="330" width="120" height="48" rx="16"/><text x="350" y="351">Access point</text><text class="sub" x="350" y="366">Wi-Fi lab</text><circle class="led" cx="396" cy="342" r="3"/></g>
          <g class="node"><rect x="175" y="330" width="110" height="48" rx="16"/><text x="230" y="351">Komputer</text><text class="sub" x="230" y="366">Lab siswa</text></g>
          <circle class="pkt" r="3.5"><animateMotion dur="2.4s" repeatCount="indefinite"><mpath href="#p1"/></animateMotion></circle>
          <circle class="pkt" r="3.5"><animateMotion dur="2.4s" begin="0.8s" repeatCount="indefinite"><mpath href="#p2"/></animateMotion></circle>
          <circle class="pkt" r="3.5"><animateMotion dur="2.8s" begin="1.2s" repeatCount="indefinite"><mpath href="#p3"/></animateMotion></circle>
          <circle class="pkt" r="3.5"><animateMotion dur="2.8s" begin="0.4s" repeatCount="indefinite"><mpath href="#p4"/></animateMotion></circle>
          <circle class="pkt" r="3.5"><animateMotion dur="2.2s" begin="1.6s" repeatCount="indefinite"><mpath href="#p5"/></animateMotion></circle>
        </svg>
      </div>
    </div>
  </section>

  <?php if (site('banner_hero')): ?>
  <div class="container"><div class="hero-banner glass"><img src="<?= h(upload_url(site('banner_hero'))) ?>" alt="Banner Jurusan <?= h(site('singkatan')) ?>"></div></div>
  <?php endif; ?>

  <!-- Kompetensi -->
  <section class="section">
    <div class="container">
      <div class="section-head">
        <div><h2>Yang dipelajari di TJKT</h2><p>Materi mengikuti kebutuhan kerja di bidang jaringan.</p></div>
        <a class="btn btn-sm" href="<?= h(url('pages/profil.php')) ?>">Baca profil jurusan</a>
      </div>
      <div class="bento">
        <?php foreach (kompetensi() as $k): ?>
          <article class="card glass"><h3><?= h($k['judul']) ?></h3><p class="muted"><?= h($k['deskripsi']) ?></p></article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Kegiatan + agenda -->
  <section class="section">
    <div class="container">
      <div class="section-head">
        <div><h2>Kegiatan terbaru</h2><p>Praktik, pelatihan, dan kunjungan dari jurusan.</p></div>
        <a class="btn btn-sm" href="<?= h(url('pages/kegiatan.php')) ?>">Semua kegiatan</a>
      </div>
      <div class="split">
        <div class="grid grid-2">
          <?php foreach ($kegiatan as $k) echo kegiatan_card($k); ?>
        </div>
        <aside class="card glass" aria-labelledby="agenda-h">
          <h3 id="agenda-h">Agenda mendatang</h3>
          <?php if ($agenda): ?>
            <ul class="agenda-list"><?php foreach ($agenda as $a) echo agenda_item($a); ?></ul>
          <?php else: ?>
            <p class="muted">Belum ada agenda. Cek lagi nanti.</p>
          <?php endif; ?>
        </aside>
      </div>
    </div>
  </section>

  <!-- Galeri -->
  <section class="section">
    <div class="container">
      <div class="section-head">
        <div><h2>Suasana praktik</h2><p>Sekilas dari laboratorium dan kegiatan siswa.</p></div>
        <a class="btn btn-sm" href="<?= h(url('pages/galeri.php')) ?>">Buka galeri</a>
      </div>
      <div class="mosaic"><?php foreach ($galeri as $g) echo gallery_item($g); ?></div>
    </div>
  </section>

  <!-- PKL -->
  <section class="section">
    <div class="container">
      <div class="section-head">
        <div><h2>Tempat PKL</h2><p>Pilihan tempat Praktik Kerja Lapangan bersama mitra industri.</p></div>
        <a class="btn btn-sm" href="<?= h(url('pages/flyer-pkl.php')) ?>">Semua tempat PKL</a>
      </div>
      <div class="grid grid-3"><?php foreach (flyer_list(3) as $f) echo flyer_card($f); ?></div>
    </div>
  </section>

  <!-- CTA -->
  <section class="section">
    <div class="container">
      <div class="cta-band glass">
        <div><h2>Ada pertanyaan tentang jurusan?</h2><p>Tanyakan langsung ke admin atau guru TJKT lewat WhatsApp.</p></div>
        <a class="btn btn-primary" href="<?= h(wa_link('Halo, saya ingin bertanya tentang jurusan TJKT.')) ?>" target="_blank" rel="noopener">Hubungi lewat WhatsApp</a>
      </div>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
