<?php
require_once __DIR__ . '/../includes/init.php';
$f = flyer_find((int)($_GET['id'] ?? 0));
if (!$f) not_found();
$page_title = $f['nama_tempat'];
$page_desc = mb_strimwidth($f['deskripsi'], 0, 150, '...');
$active = 'pkl';
require __DIR__ . '/../includes/header.php';
$wa = preg_replace('/\D/', '', $f['kontak_mitra']);
if (strpos($wa, '0') === 0) $wa = '62' . substr($wa, 1);
?>
<main>
  <section class="page-head container">
    <nav class="breadcrumb" aria-label="Jejak halaman"><a href="<?= h(url('pages/flyer-pkl.php')) ?>">Tempat PKL</a> / <?= h($f['nama_tempat']) ?></nav>
    <h1><?= h($f['nama_tempat']) ?></h1>
    <p><?= h($f['alamat']) ?></p>
  </section>

  <section class="section">
    <div class="container detail">
      <article>
        <div class="detail-cover glass"><?= media($f['gambar_flyer'], 'f' . $f['id'], 'Flyer ' . $f['nama_tempat']) ?></div>
        <div class="card glass prose">
          <h2>Tentang tempat PKL</h2>
          <?= paragraphs($f['deskripsi']) ?>
          <div class="tags"><?php foreach (array_filter(array_map('trim', explode(',', $f['bidang']))) as $t) echo '<span class="chip">' . h($t) . '</span>'; ?></div>
        </div>
      </article>
      <aside class="card glass">
        <h3>Info PKL</h3>
        <ul class="facts">
          <li><b>Lokasi</b><?= h($f['alamat']) ?></li>
          <?php if ($f['kuota']): ?><li><b>Kuota siswa</b><?= (int)$f['kuota'] ?> siswa</li><?php endif; ?>
          <li><b>Kontak mitra</b><?= h($f['kontak_mitra']) ?></li>
        </ul>
        <?php if ($wa): ?>
          <a class="btn btn-primary btn-sm" style="margin-top:1.2rem" href="https://wa.me/<?= h($wa) ?>" target="_blank" rel="noopener">Hubungi mitra</a>
        <?php endif; ?>
      </aside>
    </div>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
