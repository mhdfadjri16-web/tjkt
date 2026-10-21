<?php
$page_title = 'Galeri';
$page_desc = 'Foto praktik, video kegiatan, dan fasilitas laboratorium Jurusan TJKT.';
$active = 'galeri';
require __DIR__ . '/../includes/header.php';
$items = galeri_list();
?>
<main>
  <section class="page-head container">
    <h1>Galeri praktik</h1>
    <p>Foto praktik, video kegiatan, dan fasilitas laboratorium.</p>
  </section>

  <section class="section">
    <div class="container">
      <div class="tabs" role="group" aria-label="Filter galeri">
        <button type="button" class="tab" data-filter="semua" aria-pressed="true">Semua</button>
        <button type="button" class="tab" data-filter="praktek" aria-pressed="false">Praktik</button>
        <button type="button" class="tab" data-filter="fasilitas" aria-pressed="false">Fasilitas dan lab</button>
        <button type="button" class="tab" data-filter="video" aria-pressed="false">Video</button>
      </div>
      <?php if ($items): ?>
        <div class="gallery"><?php foreach ($items as $g) echo gallery_item($g); ?></div>
      <?php else: ?>
        <div class="card glass empty"><h3>Galeri masih kosong</h3><p>Foto dan video yang diunggah admin akan muncul di sini.</p></div>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
