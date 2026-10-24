<?php
require_once __DIR__ . '/../includes/auth.php';
$admin = require_login();
$admin_title = 'Dashboard';
$admin_active = 'dashboard';

function jumlah(string $tabel): int {
    $r = q_one('SELECT COUNT(*) AS n FROM `' . $tabel . '`');   // $tabel hanya konstanta di bawah
    return $r ? (int)$r['n'] : 0;
}
$stat = [
    'Kegiatan'       => jumlah('kegiatan'),
    'Foto dan video' => jumlah('galeri'),
    'Flyer PKL'      => jumlah('flyer_pkl'),
    'Mitra industri' => jumlah('mitra_industri'),
];

// Pengunjung
$hari = q_one('SELECT COUNT(*) AS v, COUNT(DISTINCT hash_ip) AS u FROM visitor_log WHERE tanggal = CURDATE()') ?: ['v' => 0, 'u' => 0];
$minggu = q_one('SELECT COUNT(*) AS v, COUNT(DISTINCT hash_ip) AS u FROM visitor_log WHERE tanggal >= CURDATE() - INTERVAL 6 DAY') ?: ['v' => 0, 'u' => 0];
$per_hari = [];
foreach (q_all('SELECT tanggal, COUNT(*) AS v FROM visitor_log WHERE tanggal >= CURDATE() - INTERVAL 6 DAY GROUP BY tanggal') ?? [] as $r) $per_hari[$r['tanggal']] = (int)$r['v'];
$seri = [];
for ($i = 6; $i >= 0; $i--) { $d = date('Y-m-d', strtotime("-$i days")); $seri[$d] = $per_hari[$d] ?? 0; }
$maks = max(1, max($seri));
$top = q_all('SELECT path, COUNT(*) AS n FROM visitor_log WHERE tanggal >= CURDATE() - INTERVAL 6 DAY GROUP BY path ORDER BY n DESC LIMIT 5') ?? [];

require __DIR__ . '/../includes/admin-header.php';
?>
<p class="muted">Halo, <?= h($admin['nama'] ?: $admin['username']) ?>. Ringkasan isi dan pengunjung situs.</p>
<div class="stats">
  <?php foreach ($stat as $label => $n): ?>
    <div class="card glass stat"><b><?= $n ?></b><span><?= h($label) ?></span></div>
  <?php endforeach; ?>
</div>

<div class="grid grid-2">
  <div class="card glass">
    <h3>Pengunjung</h3>
    <div class="stats" style="margin-bottom:1rem">
      <div class="stat"><b><?= (int)$hari['u'] ?></b><span>pengunjung hari ini</span></div>
      <div class="stat"><b><?= (int)$minggu['u'] ?></b><span>pengunjung 7 hari</span></div>
      <div class="stat"><b><?= (int)$minggu['v'] ?></b><span>halaman dilihat 7 hari</span></div>
    </div>
    <div class="bars" role="img" aria-label="Grafik halaman dilihat 7 hari terakhir">
      <?php foreach ($seri as $tgl => $v): ?>
        <div class="bar-col"><span class="bar-n"><?= $v ?></span><i style="height:<?= max(4, (int)round($v / $maks * 100)) ?>%"></i><span class="bar-d"><?= h(date('d/m', strtotime($tgl))) ?></span></div>
      <?php endforeach; ?>
    </div>
    <p class="muted" style="font-size:.9rem;margin:.8rem 0 0">Pengunjung dihitung anonim per hari. Bot dan admin yang sedang login tidak dihitung.</p>
  </div>
  <div class="card glass">
    <h3>Halaman terpopuler (7 hari)</h3>
    <?php if (!$top): ?>
      <p class="muted">Belum ada data kunjungan.</p>
    <?php else: ?>
      <table class="table"><tbody>
        <?php foreach ($top as $t): ?><tr><td><?= h($t['path']) ?></td><td style="text-align:right"><?= (int)$t['n'] ?></td></tr><?php endforeach; ?>
      </tbody></table>
    <?php endif; ?>
  </div>
</div>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
