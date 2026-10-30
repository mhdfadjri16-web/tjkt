# Catatan Progres - Tahap A sampai D selesai ditulis

## Status
Semua tahap PRD v2 sudah ditulis. PHP TIDAK tersedia di sandbox pembuat, jadi kode PHP hanya dicek statis (kurung dan
kurawal seimbang) dan BELUM pernah dijalankan; JavaScript sudah dicek sintaksnya dengan Node. Langkah berikutnya adalah
uji di Railway memakai checklist di bawah, lalu perbaiki error yang muncul.

## Ringkasan per tahap
- **A**: Dockerfile, schema/seed, design system liquid glass (`assets/css/style.css`), halaman publik.
- **B**: `includes/db.php`, data dari MySQL dengan cadangan dummy (`data.php` + `data-dummy.php`), `install.php`, login,
  role superadmin/editor, CSRF, rate limit, kelola akun, akun saya.
- **C**: `includes/upload.php` (upload ke Volume), `includes/crud.php` (mesin CRUD generik), halaman
  `admin/kelola-kegiatan|galeri|flyer|mitra|pengajar|profil.php`.
- **D**: `includes/plugin-loader.php` (plugin + hook), plugin `plugins/chat-agent/` (widget, API, mesin rule-based, placeholder AI),
  `admin/plugins.php`, `admin/chat-faq.php`, `admin/tema.php`, `admin/pengaturan.php`, `includes/stats.php` (tracking anonim),
  dashboard dengan grafik pengunjung.

## Hak akses
- Editor: dashboard, kegiatan, galeri, flyer, mitra, pengajar, profil, akun saya (boleh edit semua konten).
- Superadmin: semua di atas + kelola akun, plugin (termasuk FAQ chat), tema, pengaturan situs.

## Deploy ke Railway
1. Buat project; tambahkan service **MySQL** dan service **Web** dari folder ini (memakai `Dockerfile`).
2. Variables service Web: referensikan `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`; tambahkan
   `SETUP_TOKEN` (acak, panjang), `UPLOAD_DIR=/data/uploads`, dan (disarankan) `APP_SECRET` (acak, untuk hash pengunjung).
3. Tambahkan **Volume** ke service Web, mount path `/data`. Dockerfile otomatis membuat folder upload dan memberi izin tulis saat start.
4. Buka `/install.php`, isi token + akun superadmin, centang data contoh. Setelah itu halaman terkunci.
5. Masuk di `/admin/login.php`. Ganti semua isi contoh dari dashboard.

## Checklist uji (belum pernah dijalankan)
Instalasi dan login
- [ ] `install.php` sukses; kunjungan kedua menampilkan "sudah terpasang"
- [ ] Login benar/salah; 5x salah memunculkan pesan terkunci; keluar menghapus sesi
- [ ] Editor tidak bisa membuka kelola-akun, plugins, tema, pengaturan (403)
Konten dan upload
- [ ] Tambah/ubah/hapus kegiatan dengan gambar; slug unik; agenda mendatang muncul di kolom agenda
- [ ] Galeri: foto (upload) dan video (link YouTube), filter, lightbox, modal video di situs publik
- [ ] Flyer PKL, mitra, pengajar, profil (termasuk keunggulan dan logo)
- [ ] Gambar tetap ada setelah redeploy (bukti Volume bekerja); hapus data ikut menghapus file
- [ ] Upload gagal bila bukan JPG/PNG/WebP atau > 8 MB
Plugin, tema, pengaturan
- [ ] Chat muncul di semua halaman publik bila plugin aktif; hilang bila dinonaktifkan
- [ ] Chat menjawab FAQ; pertanyaan tak dikenal menampilkan saran dan tombol WhatsApp
- [ ] Ubah warna/logo/banner di Tema langsung terlihat di situs; ubah nama sekolah/kontak di Pengaturan
- [ ] Dashboard menampilkan angka pengunjung setelah beberapa kali membuka situs (bukan saat login admin)

## Catatan teknis penting
- `includes/crud.php`: tambah halaman CRUD baru cukup dengan satu file berisi konfigurasi (lihat `admin/kelola-kegiatan.php`).
  Tipe field: text, textarea, url, select, date, number, checkbox, image, media_url (khusus galeri).
- Gambar disimpan sebagai NAMA FILE di DB; ditampilkan lewat `media()`/`upload_url()` dan `media.php`.
- Plugin baru: buat `plugins/<slug>/plugin.json` + `bootstrap.php` (memakai `register_hook('footer_end'|'head_end', fn)`).
  Muncul otomatis di Admin > Plugin; pengaturan yang dideklarasikan di plugin.json punya formulir otomatis.
- Mesin chat bisa diganti: `plugins/chat-agent/engines/ai.php` masih placeholder. Panggil API AI dari server, simpan kunci di env,
  batasi laju per IP, dan jangan pernah menaruh kunci di JavaScript.
- Tracking pengunjung menyimpan hash IP (dengan tanggal dan garam), bukan IP asli. Tabel `visitor_log` belum dibersihkan otomatis.
- `.htaccess` di includes/, database/, docker/, plugins/ menolak akses langsung (plugins: hanya `api.php` yang boleh).
- Sesi PHP disimpan di disk container; setelah redeploy admin perlu login ulang.

## Penyimpangan dari PRD
- `flyer_pkl` punya kolom tambahan `bidang` dan `kuota`.
- Ada halaman tambahan `kelola-pengajar.php` dan `chat-faq.php`; `plugin.json` memakai kunci `pengaturan` untuk formulir otomatis.
- Reset password hanya oleh superadmin (tanpa email), sesuai PRD v2.

## Belum ada / ide lanjutan
- Logo asli sekolah dan konten asli (semua masih contoh) - isi lewat dashboard.
- Mesin chat berbasis AI, pembersihan log pengunjung lama, paginasi di daftar admin (batas saat ini 300 baris), kompres/resize gambar.
- Reset password lewat email (butuh SMTP).

## Tambahan: Tur Virtual Lab (walk-in)
- Halaman `pages/lab.php`, data di `includes/lab-data.php`, gaya `assets/css/lab.css`, mesin `assets/js/lab.js`.
- 23 foto lab (+ thumbnail) di `assets/img/lab/lab-NN.jpg`. Ini file statis di repo (bukan upload dashboard), jadi tidak hilang saat redeploy.
- Fitur: efek berjalan maju/mundur, titik pindah ruangan (pintu), titik info, jalan otomatis, layar penuh, geser di HP, keyboard, filmstrip, tautan langsung `#scene-N`.
- Mengganti/menambah foto: edit `includes/lab-data.php` (urutan = urutan jalan; w/h = ukuran foto; hot x,y dalam persen). Belum bisa diedit dari dashboard.
- `header.php` dan `footer.php` kini mendukung `$extra_css` / `$extra_js` per halaman. Menu bertambah "Tur Lab" (menu HP muncul mulai lebar 1000px).
- Sudah diuji di Chromium (navigasi, pin, info, Home/End, filmstrip, layar HP, tanpa error JS). Sisi PHP-nya belum dijalankan.
