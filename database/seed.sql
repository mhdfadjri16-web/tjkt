-- Data contoh. Semua isi bersifat DUMMY dan harus diganti lewat dashboard.
-- Tidak ada akun admin di sini: superadmin pertama dibuat lewat install.php.
SET NAMES utf8mb4;

INSERT INTO pengaturan_situs (`key`,`value`) VALUES
('nama_sekolah','SMKN 1 Simpang Kiri'),
('nama_jurusan','Teknik Jaringan Komputer dan Telekomunikasi'),
('singkatan','TJKT'),
('tagline','Belajar membangun jaringan, dari kabel UTP sampai server.'),
('warna_bg','#0a1020'),('warna_aksen_1','#22d3ee'),('warna_aksen_2','#8b5cf6'),('warna_aksen_3','#3b82f6'),
('kontak_wa','6281234567890'),('kontak_email','tjkt@example.sch.id'),
('alamat','Simpang Kiri, Aceh (alamat lengkap diisi lewat Pengaturan)'),
('sosial_instagram','#'),('sosial_youtube','#'),('sosial_facebook','#')
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`);

INSERT INTO plugins (nama_plugin,status_aktif) VALUES ('chat-agent',1)
ON DUPLICATE KEY UPDATE nama_plugin=nama_plugin;

INSERT INTO profil_jurusan (identitas,visi,misi,keunggulan) VALUES (
'Jurusan Teknik Jaringan Komputer dan Telekomunikasi (TJKT) SMKN 1 Simpang Kiri membekali siswa dengan keterampilan membangun, mengelola, dan mengamankan jaringan komputer serta perangkat telekomunikasi. Pembelajaran dilakukan lewat praktik langsung di laboratorium dan diperkuat dengan Praktik Kerja Lapangan di dunia usaha.',
'Menjadi jurusan unggulan yang menghasilkan lulusan terampil, berkarakter, dan siap bekerja di bidang jaringan komputer dan telekomunikasi.',
'Menyelenggarakan pembelajaran berbasis praktik di laboratorium jaringan.\nMenjalin kerja sama dengan dunia usaha dan dunia industri untuk PKL dan penyerapan lulusan.\nMembiasakan siswa bekerja rapi, jujur, dan bertanggung jawab.\nMendorong siswa mengikuti sertifikasi dan lomba kompetensi.',
'[{"judul":"Praktik langsung","deskripsi":"Siswa merakit kabel, mengatur router, dan membangun jaringan sendiri."},{"judul":"Siap PKL","deskripsi":"Pembekalan dan jaringan mitra membantu siswa mendapat tempat PKL yang sesuai."},{"judul":"Kompetensi relevan","deskripsi":"Materi mengikuti kebutuhan kerja: LAN/WAN, server Linux, nirkabel, dan keamanan dasar."}]'
);

INSERT INTO pengajar (nama,jabatan,urutan) VALUES
('Nama Kepala Jurusan (contoh)','Kepala Jurusan TJKT',1),
('Nama Guru Produktif 1 (contoh)','Guru Administrasi Infrastruktur Jaringan',2),
('Nama Guru Produktif 2 (contoh)','Guru Teknologi Jaringan Berbasis Luas',3),
('Nama Guru Produktif 3 (contoh)','Guru Administrasi Sistem Jaringan',4);

INSERT INTO kegiatan (judul,slug,ringkasan,deskripsi,tanggal,is_agenda) VALUES
('Praktik Konfigurasi Router Mikrotik Kelas XI','praktik-konfigurasi-router-mikrotik','Siswa kelas XI mengatur IP address, DHCP server, dan NAT pada router Mikrotik di laboratorium.','Siswa kelas XI berlatih mengonfigurasi router Mikrotik dari awal: reset, pemberian IP address, DHCP server, hingga NAT agar komputer di lab bisa terhubung ke internet.\n\nSetiap kelompok memeriksa hasilnya sendiri dengan perintah ping dan traceroute, lalu mencatat masalah yang mereka temui.',CURDATE() - INTERVAL 12 DAY,0),
('Pelatihan Crimping dan Instalasi Kabel UTP','pelatihan-crimping-kabel-utp','Latihan memasang konektor RJ45 dan menguji kabel dengan LAN tester.','Siswa kelas X belajar urutan warna kabel straight dan crossover, memasang konektor RJ45 dengan crimping tool, lalu menguji hasilnya memakai LAN tester.',CURDATE() - INTERVAL 30 DAY,0),
('Kunjungan Industri ke Penyedia Layanan Internet','kunjungan-industri-penyedia-internet','Siswa melihat langsung ruang server dan jaringan distribusi sebuah penyedia layanan internet.','Siswa mengunjungi penyedia layanan internet untuk melihat ruang server, perangkat distribusi, dan cara tim teknis memantau jaringan pelanggan. Kegiatan ditutup dengan tanya jawab bersama teknisi.',CURDATE() - INTERVAL 45 DAY,0),
('Lomba Desain Topologi Jaringan Antar Kelas','lomba-desain-topologi-jaringan','Setiap kelas merancang topologi jaringan untuk kantor kecil dan mempresentasikannya.','Setiap kelas membuat rancangan topologi jaringan untuk sebuah kantor kecil lengkap dengan alokasi IP address, lalu mempresentasikannya di depan juri guru.',CURDATE() - INTERVAL 60 DAY,0),
('Workshop Dasar Keamanan Jaringan','workshop-dasar-keamanan-jaringan','Pengenalan firewall, password yang kuat, dan kebiasaan aman saat memakai jaringan.','Workshop untuk siswa kelas XI dan XII tentang firewall dasar, pengelolaan password, dan kebiasaan aman saat memakai jaringan sekolah.',CURDATE() + INTERVAL 9 DAY,1),
('Pembekalan PKL Siswa Kelas XI','pembekalan-pkl-kelas-xi','Penjelasan aturan PKL, etika kerja, dan cara membuat laporan.','Pembekalan sebelum siswa berangkat PKL: aturan, etika kerja, tata cara pelaporan, dan pembagian tempat PKL.',CURDATE() + INTERVAL 21 DAY,1),
('Uji Kompetensi Keahlian (UKK)','uji-kompetensi-keahlian','Uji teori dan praktik untuk siswa kelas XII.','Uji kompetensi teori dan praktik bagi siswa kelas XII. Jadwal ruang dan penguji diumumkan melalui wali kelas.',CURDATE() + INTERVAL 45 DAY,1);

INSERT INTO galeri (tipe,kategori,url,caption) VALUES
('foto','praktek','','Merakit kabel UTP di laboratorium'),
('foto','praktek','','Konfigurasi router Mikrotik'),
('foto','praktek','','Pengujian kabel dengan LAN tester'),
('foto','praktek','','Instalasi access point'),
('foto','praktek','','Praktik instalasi server Linux'),
('foto','praktek','','Diskusi kelompok membaca topologi'),
('foto','fasilitas','','Laboratorium jaringan'),
('foto','fasilitas','','Rak server dan switch'),
('foto','fasilitas','','Ruang teori TJKT'),
('video','praktek','https://www.youtube.com/watch?v=jNQXAC9IMG0','Video contoh: dokumentasi praktik'),
('video','praktek','https://www.youtube.com/watch?v=jNQXAC9IMG0','Video contoh: kunjungan industri'),
('video','fasilitas','https://www.youtube.com/watch?v=jNQXAC9IMG0','Video contoh: keliling laboratorium');

INSERT INTO flyer_pkl (nama_tempat,deskripsi,alamat,bidang,kuota,kontak_mitra) VALUES
('Penyedia Internet Lokal (contoh)','Siswa ikut mendampingi teknisi memasang dan merawat jaringan pelanggan, termasuk penarikan kabel dan pengaturan perangkat.','Simpang Kiri, Aceh','Instalasi jaringan, Perawatan perangkat',4,'081234567801'),
('Kantor Pemerintah Bidang TIK (contoh)','Siswa membantu pengelolaan jaringan kantor, pemeliharaan komputer, dan dokumentasi aset TI.','Simpang Kiri, Aceh','Administrasi jaringan, Helpdesk',3,'081234567802'),
('Toko Komputer dan Servis (contoh)','Siswa belajar diagnosis kerusakan, instalasi sistem operasi, dan perakitan komputer.','Simpang Kiri, Aceh','Servis komputer, Perakitan PC',4,'081234567803'),
('Studio Digital dan Warnet (contoh)','Siswa membantu perawatan jaringan warnet, pengaturan hotspot, dan layanan pelanggan.','Simpang Kiri, Aceh','Hotspot, Perawatan jaringan',2,'081234567804'),
('Perusahaan Telekomunikasi (contoh)','Siswa mengenal infrastruktur telekomunikasi, fiber optik, dan prosedur keselamatan kerja di lapangan.','Aceh','Fiber optik, Telekomunikasi',3,'081234567805');

INSERT INTO mitra_industri (nama,urutan) VALUES
('Penyedia Internet Lokal',1),('Kantor Pemerintah Bidang TIK',2),('Toko Komputer dan Servis',3),
('Studio Digital dan Warnet',4),('Perusahaan Telekomunikasi',5),('Mitra Industri Lain',6);

INSERT INTO chat_faq (pertanyaan,kata_kunci,jawaban,is_populer,urutan) VALUES
('Apa itu jurusan TJKT?','tjkt,jurusan,apa itu','TJKT adalah Teknik Jaringan Komputer dan Telekomunikasi. Siswa belajar membangun, mengelola, dan mengamankan jaringan komputer.',1,1),
('Bagaimana cara mendaftar?','daftar,pendaftaran,ppdb,spmb','Informasi pendaftaran diumumkan oleh sekolah. Hubungi admin lewat tombol WhatsApp untuk info terbaru.',1,2),
('Di mana tempat PKL?','pkl,magang,prakerin','Daftar tempat PKL ada di halaman Flyer PKL.',1,3),
('Apa saja yang dipelajari?','belajar,materi,mapel,kompetensi','Materi mencakup instalasi jaringan LAN/WAN, router Mikrotik, server Linux, jaringan nirkabel, dan keamanan dasar.',0,4);
