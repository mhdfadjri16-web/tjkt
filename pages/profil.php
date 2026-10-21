<?php
$page_title = 'Profil Jurusan';
$page_desc = 'Identitas, visi, misi, dan keunggulan Jurusan ' . 'TJKT.';
$active = 'profil';
require __DIR__ . '/../includes/header.php';
$p = profil();
?>
<main>
  <section class="page-head container">
    <h1>Profil Jurusan <?= h(site('singkatan')) ?></h1>
    <p><?= h(site('nama_jurusan')) ?>, <?= h(site('nama_sekolah')) ?>.</p>
  </section>

  <section class="section">
    <div class="container detail">
      <div class="card glass prose">
        <h2>Tentang jurusan</h2>
        <?= paragraphs($p['identitas']) ?>
      </div>
      <div class="card glass">
        <?= media($p['logo'], 'logo-tjkt', 'Logo jurusan ' . site('singkatan')) ?>
        <p class="muted" style="margin-top:.8rem">Logo jurusan akan ditambahkan.</p>
      </div>
    </div>
  </section>

  <section class="section" style="padding-top:0">
    <div class="container grid grid-2">
      <div class="card glass prose">
        <h2>Visi</h2>
        <p><?= h($p['visi']) ?></p>
      </div>
      <div class="card glass prose">
        <h2>Misi</h2>
        <ol><?php foreach ($p['misi'] as $m) echo '<li>' . h($m) . '</li>'; ?></ol>
      </div>
    </div>
  </section>

  <section class="section" style="padding-top:0">
    <div class="container">
      <div class="section-head"><h2>Keunggulan jurusan</h2></div>
      <div class="grid grid-3">
        <?php foreach ($p['keunggulan'] as $k): ?>
          <article class="card glass"><h3><?= h($k['judul']) ?></h3><p class="muted"><?= h($k['deskripsi']) ?></p></article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="section" style="padding-top:0">
    <div class="container">
      <div class="section-head"><h2>Kompetensi</h2><p>Keterampilan yang dilatih selama tiga tahun.</p></div>
      <div class="bento">
        <?php foreach (kompetensi() as $k): ?>
          <article class="card glass"><h3><?= h($k['judul']) ?></h3><p class="muted"><?= h($k['deskripsi']) ?></p></article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="section" style="padding-top:0">
    <div class="container">
      <div class="section-head"><h2>Guru dan kepala jurusan</h2><p>Nama di bawah masih contoh dan akan diganti.</p></div>
      <div class="grid grid-4">
        <?php foreach (pengajar_list() as $g): ?>
          <article class="card glass person">
            <div class="avatar"><?= $g['foto'] ? '<img src="' . h(upload_url($g['foto'])) . '" alt="">' : h(mb_strtoupper(mb_substr($g['nama'], 0, 1))) ?></div>
            <h3><?= h($g['nama']) ?></h3>
            <p><?= h($g['jabatan']) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
