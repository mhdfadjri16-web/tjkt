<?php
$page_title = 'Kegiatan';
$page_desc = 'Kegiatan terbaru dan agenda mendatang Jurusan TJKT.';
$active = 'kegiatan';
require __DIR__ . '/../includes/header.php';
$list = kegiatan_list();
$agenda = agenda_list();
?>
<main>
  <section class="page-head container">
    <h1>Kegiatan jurusan</h1>
    <p>Praktik, pelatihan, kunjungan, dan agenda yang akan datang.</p>
  </section>

  <section class="section">
    <div class="container split">
      <div>
        <h2>Kegiatan terbaru</h2>
        <?php if ($list): ?>
          <div class="grid grid-2"><?php foreach ($list as $k) echo kegiatan_card($k); ?></div>
        <?php else: ?>
          <div class="card glass empty"><h3>Belum ada kegiatan</h3><p>Kegiatan yang ditambahkan admin akan muncul di sini.</p></div>
        <?php endif; ?>
      </div>
      <aside class="card glass" id="agenda" aria-labelledby="agenda-h">
        <h2 id="agenda-h" style="font-size:1.4rem">Agenda mendatang</h2>
        <?php if ($agenda): ?>
          <ul class="agenda-list"><?php foreach ($agenda as $a) echo agenda_item($a); ?></ul>
        <?php else: ?>
          <p class="muted">Belum ada agenda yang dijadwalkan.</p>
        <?php endif; ?>
      </aside>
    </div>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
