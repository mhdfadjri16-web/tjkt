<?php
/** Potongan HTML yang dipakai berulang di beberapa halaman. */

function kegiatan_card(array $k): string {
    $href = url('pages/kegiatan-detail.php') . '?slug=' . rawurlencode($k['slug']);
    return '<a class="k-card glass" href="' . h($href) . '">'
         . media($k['gambar'], $k['slug'], $k['judul'])
         . '<div class="k-body"><time datetime="' . h($k['tanggal']) . '">' . h(tgl_id($k['tanggal'])) . '</time>'
         . '<h3>' . h($k['judul']) . '</h3><p>' . h($k['ringkasan']) . '</p></div></a>';
}

function agenda_item(array $k): string {
    $t = strtotime($k['tanggal']);
    $href = url('pages/kegiatan-detail.php') . '?slug=' . rawurlencode($k['slug']);
    $bln = substr(tgl_id($k['tanggal'], true), strpos(tgl_id($k['tanggal'], true), ' ') + 1, 3);
    return '<li><a class="agenda-item" href="' . h($href) . '">'
         . '<div class="date-badge"><b>' . date('j', $t) . '</b><span>' . h($bln) . '</span></div>'
         . '<div><h3>' . h($k['judul']) . '</h3><small>' . h(tgl_id($k['tanggal'])) . '</small></div></a></li>';
}

function gallery_item(array $g): string {
    $yt = $g['tipe'] === 'video' ? youtube_id($g['url']) : null;
    $attr = ' data-tipe="' . h($g['tipe']) . '" data-kategori="' . h($g['kategori']) . '" data-caption="' . h($g['caption']) . '"'
          . ($yt ? ' data-yt="' . h($yt) . '"' : '');
    // Video: thumbnail dari YouTube bila ada, jika tidak pakai placeholder
    $visual = $yt
        ? art('video' . $g['id'], '', $g['caption'])
        : media($g['url'], 'g' . $g['id'], $g['caption']);
    return '<button type="button" class="g-item"' . $attr . ' aria-label="' . h(($yt ? 'Putar video: ' : 'Lihat foto: ') . $g['caption']) . '">'
         . $visual . ($yt ? '<span class="g-play" aria-hidden="true"></span>' : '')
         . '<span class="g-cap">' . h($g['caption']) . '</span></button>';
}

function flyer_card(array $f): string {
    $href = url('pages/flyer-detail.php') . '?id=' . (int)$f['id'];
    $tags = '';
    foreach (array_filter(array_map('trim', explode(',', $f['bidang']))) as $t) $tags .= '<span class="chip">' . h($t) . '</span>';
    return '<a class="flyer-card glass" href="' . h($href) . '">'
         . media($f['gambar_flyer'], 'f' . $f['id'], $f['nama_tempat'], '')
         . '<div class="k-body"><h3>' . h($f['nama_tempat']) . '</h3><p>' . h(mb_strimwidth($f['deskripsi'], 0, 110, '...')) . '</p>'
         . '<div class="tags">' . $tags . '</div></div></a>';
}

function mitra_pill(array $m): string {
    $ini = mb_strtoupper(mb_substr($m['nama'], 0, 1));
    $logo = $m['logo'] ? '<img src="' . h(upload_url($m['logo'])) . '" alt="">' : h($ini);
    return '<div class="mitra-pill"><span class="logo" aria-hidden="true">' . $logo . '</span>' . h($m['nama']) . '</div>';
}
