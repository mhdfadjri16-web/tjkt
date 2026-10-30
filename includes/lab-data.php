<?php
/**
 * Data Tur Virtual Lab. Urutan scene = urutan jalan. Foto ada di assets/img/lab/.
 * Hotspot: x,y dalam persen dari foto. tipe 'pindah' (ke = nomor scene tujuan, mulai dari 1) atau 'info' (judul + teks).
 * Untuk mengganti foto: timpa file lab-NN.jpg (rasio boleh landscape atau portrait, ukuran sisi terpanjang sekitar 1600px) dan ubah w/h di sini.
 */
function lab_zones(): array {
    return [['id' => 'lab', 'judul' => 'Ruang Lab Komputer', 'deskripsi' => 'Tempat siswa praktik: deretan komputer, layar besar, proyektor, dan meja instruktur.'], ['id' => 'belakang', 'judul' => 'Ruang Belakang', 'deskripsi' => 'Ruang di balik pintu lab, dengan area duduk dan dinding jadwal.'], ['id' => 'kerja', 'judul' => 'Ruang Kerja', 'deskripsi' => 'Ruang kerja kecil dengan meja dan jendela bertirai.']];
}

function lab_scenes(): array {
    return [
        ['judul' => 'Pandangan umum lab', 'zona' => 'lab', 'file' => 'lab-01.jpg', 'thumb' => 'lab-01-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => []],
        ['judul' => 'Deretan komputer dan jendela biru', 'zona' => 'lab', 'file' => 'lab-02.jpg', 'thumb' => 'lab-02-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => []],
        ['judul' => 'Layar besar dan meja guru', 'zona' => 'lab', 'file' => 'lab-03.jpg', 'thumb' => 'lab-03-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => [['x' => 80, 'y' => 45, 'tipe' => 'info', 'judul' => 'Layar besar', 'teks' => 'Layar besar di depan kelas untuk menampilkan materi saat belajar.']]],
        ['judul' => 'Layar besar dan pintu ke ruang belakang', 'zona' => 'lab', 'file' => 'lab-04.jpg', 'thumb' => 'lab-04-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => [['x' => 63, 'y' => 58, 'tipe' => 'pindah', 'ke' => 14, 'label' => 'Pintu ke ruang belakang']]],
        ['judul' => 'Barisan komputer di sisi jendela', 'zona' => 'lab', 'file' => 'lab-05.jpg', 'thumb' => 'lab-05-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => [['x' => 78, 'y' => 21, 'tipe' => 'info', 'judul' => 'Pendingin ruangan', 'teks' => 'AC terpasang di dinding agar ruang lab tetap sejuk saat praktik.']]],
        ['judul' => 'Meja instruktur dengan printer', 'zona' => 'lab', 'file' => 'lab-06.jpg', 'thumb' => 'lab-06-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => [['x' => 65, 'y' => 64, 'tipe' => 'info', 'judul' => 'Printer dan perangkat', 'teks' => 'Printer dan perangkat pendukung ada di meja instruktur.']]],
        ['judul' => 'Komputer siswa baris kiri', 'zona' => 'lab', 'file' => 'lab-07.jpg', 'thumb' => 'lab-07-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => [['x' => 83, 'y' => 58, 'tipe' => 'info', 'judul' => 'Komputer siswa', 'teks' => 'Setiap meja siswa punya komputer lengkap dengan keyboard dan mouse.']]],
        ['judul' => 'Komputer siswa baris tengah', 'zona' => 'lab', 'file' => 'lab-08.jpg', 'thumb' => 'lab-08-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => []],
        ['judul' => 'Dispenser air dan barisan komputer', 'zona' => 'lab', 'file' => 'lab-09.jpg', 'thumb' => 'lab-09-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => [['x' => 28, 'y' => 61, 'tipe' => 'info', 'judul' => 'Dispenser air minum', 'teks' => 'Dispenser air minum tersedia di sudut ruangan.']]],
        ['judul' => 'Barisan komputer dari sudut samping', 'zona' => 'lab', 'file' => 'lab-10.jpg', 'thumb' => 'lab-10-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => []],
        ['judul' => 'Dinding depan: proyektor dan layar', 'zona' => 'lab', 'file' => 'lab-11.jpg', 'thumb' => 'lab-11-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => [['x' => 48, 'y' => 6, 'tipe' => 'info', 'judul' => 'Proyektor', 'teks' => 'Proyektor terpasang di langit-langit untuk presentasi materi.'], ['x' => 67, 'y' => 58, 'tipe' => 'info', 'judul' => 'Layar di dinding depan', 'teks' => 'Layar berdiri di depan kelas, di samping meja instruktur.']]],
        ['judul' => 'Lorong tengah lab', 'zona' => 'lab', 'file' => 'lab-12.jpg', 'thumb' => 'lab-12-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => []],
        ['judul' => 'Meja guru menghadap deretan komputer', 'zona' => 'lab', 'file' => 'lab-13.jpg', 'thumb' => 'lab-13-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => []],
        ['judul' => 'Pintu menuju ruang belakang', 'zona' => 'belakang', 'file' => 'lab-14.jpg', 'thumb' => 'lab-14-t.jpg', 'w' => 960, 'h' => 1280, 'hot' => [['x' => 50, 'y' => 58, 'tipe' => 'pindah', 'ke' => 15, 'label' => 'Buka pintu']]],
        ['judul' => 'Ambang pintu ruang belakang', 'zona' => 'belakang', 'file' => 'lab-15.jpg', 'thumb' => 'lab-15-t.jpg', 'w' => 960, 'h' => 1280, 'hot' => [['x' => 52, 'y' => 58, 'tipe' => 'pindah', 'ke' => 16, 'label' => 'Masuk ruang belakang']]],
        ['judul' => 'Ruang belakang: area duduk', 'zona' => 'belakang', 'file' => 'lab-16.jpg', 'thumb' => 'lab-16-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => []],
        ['judul' => 'Area duduk dan jalan ke ruang kerja', 'zona' => 'belakang', 'file' => 'lab-17.jpg', 'thumb' => 'lab-17-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => [['x' => 33, 'y' => 42, 'tipe' => 'pindah', 'ke' => 21, 'label' => 'Ke ruang kerja']]],
        ['judul' => 'Sudut ruang belakang', 'zona' => 'belakang', 'file' => 'lab-18.jpg', 'thumb' => 'lab-18-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => []],
        ['judul' => 'Sudut jendela ruang belakang', 'zona' => 'belakang', 'file' => 'lab-19.jpg', 'thumb' => 'lab-19-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => []],
        ['judul' => 'Dinding jadwal dan jalan ke ruang kerja', 'zona' => 'belakang', 'file' => 'lab-20.jpg', 'thumb' => 'lab-20-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => [['x' => 50, 'y' => 50, 'tipe' => 'pindah', 'ke' => 21, 'label' => 'Ke ruang kerja']]],
        ['judul' => 'Tirai menuju ruang kerja', 'zona' => 'kerja', 'file' => 'lab-21.jpg', 'thumb' => 'lab-21-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => []],
        ['judul' => 'Ruang kerja dan jendela', 'zona' => 'kerja', 'file' => 'lab-22.jpg', 'thumb' => 'lab-22-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => []],
        ['judul' => 'Meja kerja guru', 'zona' => 'kerja', 'file' => 'lab-23.jpg', 'thumb' => 'lab-23-t.jpg', 'w' => 1280, 'h' => 960, 'hot' => []],
    ];
}
