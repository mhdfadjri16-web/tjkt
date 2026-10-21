<?php
$page_title = 'Kontak';
$page_desc = 'Hubungi admin dan guru Jurusan TJKT.';
$active = 'kontak';
require __DIR__ . '/../includes/header.php';
?>
<main>
  <section class="page-head container">
    <h1>Hubungi kami</h1>
    <p>Pertanyaan soal jurusan, pendaftaran, atau PKL bisa ditanyakan lewat kanal berikut.</p>
  </section>

  <section class="section">
    <div class="container contact-list">
      <article class="card glass">
        <h3>WhatsApp</h3>
        <p class="muted">Cara tercepat untuk bertanya ke admin atau guru.</p>
        <a class="btn btn-primary btn-sm" href="<?= h(wa_link('Halo, saya ingin bertanya tentang jurusan TJKT.')) ?>" target="_blank" rel="noopener">Kirim pesan</a>
      </article>
      <article class="card glass">
        <h3>Email</h3>
        <p class="muted">Untuk pertanyaan yang butuh penjelasan panjang.</p>
        <a class="btn btn-sm" href="mailto:<?= h(site('kontak_email')) ?>"><?= h(site('kontak_email')) ?></a>
      </article>
      <article class="card glass">
        <h3>Alamat</h3>
        <p class="muted"><?= h(site('nama_sekolah')) ?><br><?= h(site('alamat')) ?></p>
      </article>
      <article class="card glass">
        <h3>Media sosial</h3>
        <div class="social">
          <a href="<?= h(site('sosial_instagram')) ?>" rel="noopener">Instagram</a>
          <a href="<?= h(site('sosial_youtube')) ?>" rel="noopener">YouTube</a>
          <a href="<?= h(site('sosial_facebook')) ?>" rel="noopener">Facebook</a>
        </div>
      </article>
    </div>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
