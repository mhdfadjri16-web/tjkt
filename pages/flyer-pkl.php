<?php
$page_title = 'Tempat PKL';
$page_desc = 'Daftar tempat Praktik Kerja Lapangan dan mitra industri Jurusan TJKT.';
$active = 'pkl';
require __DIR__ . '/../includes/header.php';
$list = flyer_list();
?>
<main>
  <section class="page-head container">
    <h1>Tempat PKL</h1>
    <p>Pilih tempat Praktik Kerja Lapangan sesuai bidang yang kamu minati.</p>
  </section>

  <section class="section">
    <div class="container">
      <?php if ($list): ?>
        <div class="grid grid-3"><?php foreach ($list as $f) echo flyer_card($f); ?></div>
      <?php else: ?>
        <div class="card glass empty"><h3>Belum ada tempat PKL</h3><p>Flyer PKL yang ditambahkan admin akan muncul di sini.</p></div>
      <?php endif; ?>
    </div>
  </section>

  <section class="section" style="padding-top:0">
    <div class="container">
      <div class="section-head"><h2>Mitra industri</h2><p>Dunia usaha yang bekerja sama dengan jurusan.</p></div>
      <div class="mitra-row"><?php foreach (mitra_list() as $m) echo mitra_pill($m); ?></div>
    </div>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
