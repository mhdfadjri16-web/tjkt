<footer class="footer">
  <div class="container">
    <div class="footer-card glass">
      <div class="footer-brand">
        <strong><?= h(site('nama_sekolah')) ?></strong>
        <p>Jurusan <?= h(site('nama_jurusan')) ?></p>
        <p class="muted"><?= h(site('alamat')) ?></p>
      </div>
      <nav class="footer-links" aria-label="Tautan footer">
        <a href="<?= h(url('pages/profil.php')) ?>">Profil</a>
        <a href="<?= h(url('pages/kegiatan.php')) ?>">Kegiatan</a>
        <a href="<?= h(url('pages/galeri.php')) ?>">Galeri</a>
        <a href="<?= h(url('pages/lab.php')) ?>">Tur Lab</a>
        <a href="<?= h(url('pages/flyer-pkl.php')) ?>">Tempat PKL</a>
        <a href="<?= h(url('pages/kontak.php')) ?>">Kontak</a>
      </nav>
      <div class="footer-contact">
        <a href="<?= h(wa_link()) ?>" rel="noopener" target="_blank">WhatsApp admin</a>
        <a href="mailto:<?= h(site('kontak_email')) ?>"><?= h(site('kontak_email')) ?></a>
        <span class="social">
          <a href="<?= h(site('sosial_instagram')) ?>" rel="noopener">Instagram</a>
          <a href="<?= h(site('sosial_youtube')) ?>" rel="noopener">YouTube</a>
          <a href="<?= h(site('sosial_facebook')) ?>" rel="noopener">Facebook</a>
        </span>
      </div>
    </div>
    <p class="copy">&copy; <?= date('Y') ?> <?= h(site('nama_sekolah')) ?>. Jurusan <?= h(site('singkatan')) ?>.</p>
  </div>
</footer>
<?php hook('footer_end'); ?>
<script src="<?= h(asset('js/main.js')) ?>" defer></script>
<?php foreach (($extra_js ?? []) as $js): ?><script src="<?= h($js) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
