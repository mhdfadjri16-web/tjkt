<?php
/**
 * DATA DUMMY (cadangan).
 * Dipakai oleh includes/data.php bila database belum terpasang / tidak terhubung,
 * sehingga situs tetap tampil saat testing. Jangan dimuat langsung oleh halaman.
 */

function dummy_site(string $key, string $default = ''): string {
    static $s = [
        'nama_sekolah'  => 'SMKN 1 Simpang Kiri',
        'nama_jurusan'  => 'Teknik Jaringan Komputer dan Telekomunikasi',
        'singkatan'     => 'TJKT',
        'tagline'       => 'Belajar membangun jaringan, dari kabel UTP sampai server.',
        'warna_bg'      => '#0a1020',
        'warna_aksen_1' => '#22d3ee',
        'warna_aksen_2' => '#8b5cf6',
        'warna_aksen_3' => '#3b82f6',
        'kontak_wa'     => '6281234567890',
        'kontak_email'  => 'tjkt@example.sch.id',
        'alamat'        => 'Simpang Kiri, Aceh (alamat lengkap diisi lewat Pengaturan)',
        'sosial_instagram' => '#',
        'sosial_youtube'   => '#',
        'sosial_facebook'  => '#',
    ];
    return $s[$key] ?? $default;
}

/** Plugin aktif? Tahap D: baca tabel plugins. */
function dummy_plugin_aktif(string $nama): bool { return false; }

function dummy_profil(): array {
    return [
        'identitas' => 'Jurusan Teknik Jaringan Komputer dan Telekomunikasi (TJKT) SMKN 1 Simpang Kiri membekali siswa dengan keterampilan membangun, mengelola, dan mengamankan jaringan komputer serta perangkat telekomunikasi. Pembelajaran dilakukan lewat praktik langsung di laboratorium dan diperkuat dengan Praktik Kerja Lapangan di dunia usaha.',
        'visi' => 'Menjadi jurusan unggulan yang menghasilkan lulusan terampil, berkarakter, dan siap bekerja di bidang jaringan komputer dan telekomunikasi.',
        'misi' => [
            'Menyelenggarakan pembelajaran berbasis praktik di laboratorium jaringan.',
            'Menjalin kerja sama dengan dunia usaha dan dunia industri untuk PKL dan penyerapan lulusan.',
            'Membiasakan siswa bekerja rapi, jujur, dan bertanggung jawab.',
            'Mendorong siswa mengikuti sertifikasi dan lomba kompetensi.',
        ],
        'keunggulan' => [
            ['judul' => 'Praktik langsung', 'deskripsi' => 'Siswa merakit kabel, mengatur router, dan membangun jaringan sendiri.'],
            ['judul' => 'Siap PKL', 'deskripsi' => 'Pembekalan dan jaringan mitra membantu siswa mendapat tempat PKL yang sesuai.'],
            ['judul' => 'Kompetensi relevan', 'deskripsi' => 'Materi mengikuti kebutuhan kerja: LAN/WAN, server Linux, nirkabel, dan keamanan dasar.'],
        ],
        'logo' => null,
    ];
}

/** Daftar kompetensi (konten tetap di kode; bisa dipindah ke DB bila perlu). */
function dummy_kompetensi(): array {
    return [
        ['judul' => 'Instalasi jaringan LAN dan WAN', 'deskripsi' => 'Menarik dan memasang kabel UTP, crimping RJ45, memasang switch, dan menguji sambungan sampai jaringan menyala.'],
        ['judul' => 'Router Mikrotik', 'deskripsi' => 'IP address, DHCP, NAT, dan pembagian bandwidth.'],
        ['judul' => 'Server Linux', 'deskripsi' => 'Memasang dan mengelola layanan web, DNS, dan berbagi berkas.'],
        ['judul' => 'Jaringan nirkabel', 'deskripsi' => 'Access point, hotspot, dan cakupan sinyal.'],
        ['judul' => 'Telekomunikasi dan fiber optik', 'deskripsi' => 'Dasar sistem telekomunikasi, penyambungan fiber, dan keselamatan kerja di lapangan.'],
        ['judul' => 'Keamanan dasar', 'deskripsi' => 'Firewall, password, dan kebiasaan aman.'],
    ];
}

function dummy_pengajar_list(): array {
    return [
        ['nama' => 'Nama Kepala Jurusan (contoh)', 'jabatan' => 'Kepala Jurusan TJKT', 'foto' => null],
        ['nama' => 'Nama Guru Produktif 1 (contoh)', 'jabatan' => 'Guru Administrasi Infrastruktur Jaringan', 'foto' => null],
        ['nama' => 'Nama Guru Produktif 2 (contoh)', 'jabatan' => 'Guru Teknologi Jaringan Berbasis Luas', 'foto' => null],
        ['nama' => 'Nama Guru Produktif 3 (contoh)', 'jabatan' => 'Guru Administrasi Sistem Jaringan', 'foto' => null],
    ];
}

function dummy_kegiatan_all(): array {
    $d = fn(int $n) => date('Y-m-d', strtotime(($n >= 0 ? '+' : '') . $n . ' days'));
    return [
        ['id'=>1,'slug'=>'praktik-konfigurasi-router-mikrotik','judul'=>'Praktik Konfigurasi Router Mikrotik Kelas XI','ringkasan'=>'Siswa kelas XI mengatur IP address, DHCP server, dan NAT pada router Mikrotik di laboratorium.','deskripsi'=>"Siswa kelas XI berlatih mengonfigurasi router Mikrotik dari awal: reset, pemberian IP address, DHCP server, hingga NAT agar komputer di lab bisa terhubung ke internet.\n\nSetiap kelompok memeriksa hasilnya sendiri dengan perintah ping dan traceroute, lalu mencatat masalah yang mereka temui.",'tanggal'=>$d(-12),'tanggal_selesai'=>null,'gambar'=>null,'is_agenda'=>0],
        ['id'=>2,'slug'=>'pelatihan-crimping-kabel-utp','judul'=>'Pelatihan Crimping dan Instalasi Kabel UTP','ringkasan'=>'Latihan memasang konektor RJ45 dan menguji kabel dengan LAN tester.','deskripsi'=>'Siswa kelas X belajar urutan warna kabel straight dan crossover, memasang konektor RJ45 dengan crimping tool, lalu menguji hasilnya memakai LAN tester.','tanggal'=>$d(-30),'tanggal_selesai'=>null,'gambar'=>null,'is_agenda'=>0],
        ['id'=>3,'slug'=>'kunjungan-industri-penyedia-internet','judul'=>'Kunjungan Industri ke Penyedia Layanan Internet','ringkasan'=>'Siswa melihat langsung ruang server dan jaringan distribusi sebuah penyedia layanan internet.','deskripsi'=>'Siswa mengunjungi penyedia layanan internet untuk melihat ruang server, perangkat distribusi, dan cara tim teknis memantau jaringan pelanggan. Kegiatan ditutup dengan tanya jawab bersama teknisi.','tanggal'=>$d(-45),'tanggal_selesai'=>null,'gambar'=>null,'is_agenda'=>0],
        ['id'=>4,'slug'=>'lomba-desain-topologi-jaringan','judul'=>'Lomba Desain Topologi Jaringan Antar Kelas','ringkasan'=>'Setiap kelas merancang topologi jaringan untuk kantor kecil dan mempresentasikannya.','deskripsi'=>'Setiap kelas membuat rancangan topologi jaringan untuk sebuah kantor kecil lengkap dengan alokasi IP address, lalu mempresentasikannya di depan juri guru.','tanggal'=>$d(-60),'tanggal_selesai'=>null,'gambar'=>null,'is_agenda'=>0],
        ['id'=>5,'slug'=>'workshop-dasar-keamanan-jaringan','judul'=>'Workshop Dasar Keamanan Jaringan','ringkasan'=>'Pengenalan firewall, password yang kuat, dan kebiasaan aman saat memakai jaringan.','deskripsi'=>'Workshop untuk siswa kelas XI dan XII tentang firewall dasar, pengelolaan password, dan kebiasaan aman saat memakai jaringan sekolah.','tanggal'=>$d(9),'tanggal_selesai'=>null,'gambar'=>null,'is_agenda'=>1],
        ['id'=>6,'slug'=>'pembekalan-pkl-kelas-xi','judul'=>'Pembekalan PKL Siswa Kelas XI','ringkasan'=>'Penjelasan aturan PKL, etika kerja, dan cara membuat laporan.','deskripsi'=>'Pembekalan sebelum siswa berangkat PKL: aturan, etika kerja, tata cara pelaporan, dan pembagian tempat PKL.','tanggal'=>$d(21),'tanggal_selesai'=>null,'gambar'=>null,'is_agenda'=>1],
        ['id'=>7,'slug'=>'uji-kompetensi-keahlian','judul'=>'Uji Kompetensi Keahlian (UKK)','ringkasan'=>'Uji teori dan praktik untuk siswa kelas XII.','deskripsi'=>'Uji kompetensi teori dan praktik bagi siswa kelas XII. Jadwal ruang dan penguji diumumkan melalui wali kelas.','tanggal'=>$d(45),'tanggal_selesai'=>null,'gambar'=>null,'is_agenda'=>1],
    ];
}

/** Kegiatan yang sudah berlangsung, terbaru dulu. */
function dummy_kegiatan_list(?int $limit = null): array {
    $r = array_values(array_filter(dummy_kegiatan_all(), fn($k) => !$k['is_agenda']));
    usort($r, fn($a, $b) => strcmp($b['tanggal'], $a['tanggal']));
    return $limit ? array_slice($r, 0, $limit) : $r;
}

/** Agenda mendatang, terdekat dulu. */
function dummy_agenda_list(?int $limit = null): array {
    $r = array_values(array_filter(dummy_kegiatan_all(), fn($k) => $k['is_agenda'] && $k['tanggal'] >= date('Y-m-d')));
    usort($r, fn($a, $b) => strcmp($a['tanggal'], $b['tanggal']));
    return $limit ? array_slice($r, 0, $limit) : $r;
}

function dummy_kegiatan_find(string $slug): ?array {
    foreach (dummy_kegiatan_all() as $k) if ($k['slug'] === $slug) return $k;
    return null;
}

/** $kategori: 'praktek' | 'fasilitas' | null. $tipe: 'foto' | 'video' | null. */
function dummy_galeri_list(?string $kategori = null, ?string $tipe = null, ?int $limit = null): array {
    $yt = 'https://www.youtube.com/watch?v=jNQXAC9IMG0';  // video contoh
    $all = [
        ['id'=>1,'tipe'=>'foto','kategori'=>'praktek','url'=>'','caption'=>'Merakit kabel UTP di laboratorium'],
        ['id'=>2,'tipe'=>'foto','kategori'=>'praktek','url'=>'','caption'=>'Konfigurasi router Mikrotik'],
        ['id'=>3,'tipe'=>'foto','kategori'=>'praktek','url'=>'','caption'=>'Pengujian kabel dengan LAN tester'],
        ['id'=>4,'tipe'=>'foto','kategori'=>'praktek','url'=>'','caption'=>'Instalasi access point'],
        ['id'=>5,'tipe'=>'foto','kategori'=>'praktek','url'=>'','caption'=>'Praktik instalasi server Linux'],
        ['id'=>6,'tipe'=>'foto','kategori'=>'praktek','url'=>'','caption'=>'Diskusi kelompok membaca topologi'],
        ['id'=>7,'tipe'=>'foto','kategori'=>'fasilitas','url'=>'','caption'=>'Laboratorium jaringan'],
        ['id'=>8,'tipe'=>'foto','kategori'=>'fasilitas','url'=>'','caption'=>'Rak server dan switch'],
        ['id'=>9,'tipe'=>'foto','kategori'=>'fasilitas','url'=>'','caption'=>'Ruang teori TJKT'],
        ['id'=>10,'tipe'=>'video','kategori'=>'praktek','url'=>$yt,'caption'=>'Video contoh: dokumentasi praktik'],
        ['id'=>11,'tipe'=>'video','kategori'=>'praktek','url'=>$yt,'caption'=>'Video contoh: kunjungan industri'],
        ['id'=>12,'tipe'=>'video','kategori'=>'fasilitas','url'=>$yt,'caption'=>'Video contoh: keliling laboratorium'],
    ];
    $r = array_values(array_filter($all, fn($g) => (!$kategori || $g['kategori'] === $kategori) && (!$tipe || $g['tipe'] === $tipe)));
    return $limit ? array_slice($r, 0, $limit) : $r;
}

function dummy_flyer_list(?int $limit = null): array {
    $r = [
        ['id'=>1,'nama_tempat'=>'Penyedia Internet Lokal (contoh)','deskripsi'=>'Siswa ikut mendampingi teknisi memasang dan merawat jaringan pelanggan, termasuk penarikan kabel dan pengaturan perangkat.','alamat'=>'Simpang Kiri, Aceh','bidang'=>'Instalasi jaringan, Perawatan perangkat','kuota'=>4,'gambar_flyer'=>null,'kontak_mitra'=>'081234567801'],
        ['id'=>2,'nama_tempat'=>'Kantor Pemerintah Bidang TIK (contoh)','deskripsi'=>'Siswa membantu pengelolaan jaringan kantor, pemeliharaan komputer, dan dokumentasi aset TI.','alamat'=>'Simpang Kiri, Aceh','bidang'=>'Administrasi jaringan, Helpdesk','kuota'=>3,'gambar_flyer'=>null,'kontak_mitra'=>'081234567802'],
        ['id'=>3,'nama_tempat'=>'Toko Komputer dan Servis (contoh)','deskripsi'=>'Siswa belajar diagnosis kerusakan, instalasi sistem operasi, dan perakitan komputer.','alamat'=>'Simpang Kiri, Aceh','bidang'=>'Servis komputer, Perakitan PC','kuota'=>4,'gambar_flyer'=>null,'kontak_mitra'=>'081234567803'],
        ['id'=>4,'nama_tempat'=>'Studio Digital dan Warnet (contoh)','deskripsi'=>'Siswa membantu perawatan jaringan warnet, pengaturan hotspot, dan layanan pelanggan.','alamat'=>'Simpang Kiri, Aceh','bidang'=>'Hotspot, Perawatan jaringan','kuota'=>2,'gambar_flyer'=>null,'kontak_mitra'=>'081234567804'],
        ['id'=>5,'nama_tempat'=>'Perusahaan Telekomunikasi (contoh)','deskripsi'=>'Siswa mengenal infrastruktur telekomunikasi, fiber optik, dan prosedur keselamatan kerja di lapangan.','alamat'=>'Aceh','bidang'=>'Fiber optik, Telekomunikasi','kuota'=>3,'gambar_flyer'=>null,'kontak_mitra'=>'081234567805'],
    ];
    return $limit ? array_slice($r, 0, $limit) : $r;
}

function dummy_flyer_find(int $id): ?array {
    foreach (dummy_flyer_list() as $f) if ($f['id'] === $id) return $f;
    return null;
}

function dummy_mitra_list(): array {
    return [
        ['nama'=>'Penyedia Internet Lokal','logo'=>null,'website'=>null],
        ['nama'=>'Kantor Pemerintah Bidang TIK','logo'=>null,'website'=>null],
        ['nama'=>'Toko Komputer dan Servis','logo'=>null,'website'=>null],
        ['nama'=>'Studio Digital dan Warnet','logo'=>null,'website'=>null],
        ['nama'=>'Perusahaan Telekomunikasi','logo'=>null,'website'=>null],
        ['nama'=>'Mitra Industri Lain','logo'=>null,'website'=>null],
    ];
}
