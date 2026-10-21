<?php
require_once __DIR__ . '/../includes/init.php';
$k = kegiatan_find((string)($_GET['slug'] ?? ''));
if (!$k) not_found();
$page_title = $k['judul'];
$page_desc = $k['ringkasan'];
$active = 'kegiatan';
require __DIR__ . '/../includes/header.php';
$lain = array_values(array_filter(kegiatan_list(4), fn($x) => $x['slug'] !== $k['slug']));
$lain = array_slice($lain, 0, 3);
?>
<main>
  <section class="page-head container">
    <nav class="breadcrumb" aria-label="Jejak halaman"><a href="<?= h(url('pages/kegiatan.php')) ?>">Kegiatan</a> / <?= h($k['judul']) ?></nav>
    <h1><?= h($k['judul']) ?></h1>
    <p><?= $k['is_agenda'] ? 'Agenda' : 'Kegiatan' ?> pada <time datetime="<?= h($k['tanggal']) ?>"><?= h(tgl_id($k['tanggal'])) ?></time><?= $k['tanggal_selesai'] ? ' sampai ' . h(tgl_id($k['tanggal_selesai'])) : '' ?></p>
  </section>

  <section class="section">
    <div class="container detail">
      <article>
        <div class="detail-cover glass"><?= media($k['gambar'], $k['slug'], $k['judul']) ?></div>
        <div class="card glass prose"><?= paragraphs($k['deskripsi']) ?></div>
      </article>
      <aside class="card glass">
        <h3>Info singkat</h3>
        <ul class="facts">
          <li><b>Tanggal</b><?= h(tgl_id($k['tanggal'])) ?></li>
          <li><b>Jenis</b><?= $k['is_agenda'] ? 'Agenda mendatang' : 'Kegiatan selesai' ?></li>
          <li><b>Penyelenggara</b>Jurusan <?= h(site('singkatan')) ?>, <?= h(site('nama_sekolah')) ?></li>
        </ul>
        <a class="btn btn-sm" style="margin-top:1.2rem" href="<?= h(wa_link('Halo, saya ingin bertanya tentang: ' . $k['judul'])) ?>" target="_blank" rel="noopener">Tanya admin</a>
      </aside>
    </div>
  </section>

  <?php if ($lain): ?>
  <section class="section" style="padding-top:0">
    <div class="container">
      <div class="section-head"><h2>Kegiatan lainnya</h2></div>
      <div class="grid grid-3"><?php foreach ($lain as $x) echo kegiatan_card($x); ?></div>
    </div>
  </section>
  <?php endif; ?>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
