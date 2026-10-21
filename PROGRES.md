# Catatan Progres - sampai Tahap B (AI ke-2)

## Status
Tahap A dan B selesai ditulis, tetapi PHP tidak tersedia di sandbox pembuat, jadi kode HANYA dicek statis
(kurung/kurawal seimbang) dan BELUM dijalankan, terutama bagian database dan login. Uji di Railway/XAMPP
memakai checklist di bawah, lalu laporkan error apa adanya ke AI berikutnya.

## Tahap B: yang dibuat atau diubah
- `includes/db.php`: PDO dari env `MYSQLHOST/PORT/USER/PASSWORD/DATABASE`; helper `db()`, `q_all()`, `q_one()`, `q_exec()`
  (semua prepared statement; q_all/q_one mengembalikan null bila DB mati atau tabel belum ada).
- `includes/data.php`: DIGANTI jadi query MySQL. Bila DB tidak terhubung / tabel belum ada, otomatis jatuh ke
  `includes/data-dummy.php` (isi lama Tahap A, fungsi berawalan `dummy_`). Nama fungsi dan bentuk array tidak berubah.
- `install.php`: pasang schema + seed + superadmin pertama; butuh env `SETUP_TOKEN`; terkunci otomatis setelah ada admin.
- `includes/auth.php`: session aman (HttpOnly, SameSite=Lax, Secure di HTTPS), login, logout, `require_login()`,
  `require_role('superadmin')`, rate limit (5 gagal / 10 menit per username atau IP), `cek_password_baru()` (min 8 karakter),
  `redirect()`, `flash_set()/flash_get()`.
- `includes/csrf.php`: `csrf_field()` dan `csrf_check()` untuk semua form POST.
- `includes/admin-header.php`, `admin-footer.php`, `assets/css/admin.css`: layout admin bergaya glass (sidebar + konten).
- `admin/`: `login.php`, `logout.php` (POST), `index.php`, `dashboard.php` (ringkasan jumlah), `akun-saya.php` (ganti nama/password),
  `kelola-akun.php` (superadmin: tambah, ubah role/aktif, reset password, hapus; tidak bisa menghapus/menurunkan diri sendiri
  atau superadmin aktif terakhir).
- `.htaccess` di `includes/`, `database/`, `docker/` menolak akses langsung; `Dockerfile` kini mengaktifkan AllowOverride All.

## Deploy ke Railway
1. Buat project, tambahkan service **MySQL** dan service **Web** dari repo/folder ini (Railway memakai `Dockerfile`).
2. Di service Web, Variables: referensikan variabel MySQL (`MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`,
   `MYSQLDATABASE`), tambahkan `SETUP_TOKEN` (teks acak panjang) dan `UPLOAD_DIR=/data/uploads`.
3. Tambahkan **Volume** ke service Web, mount path `/data`.
4. Buka `https://<domain-railway>/install.php`, isi token + akun superadmin, centang data contoh, pasang.
5. Masuk lewat `/admin/login.php`.

## Checklist uji (belum pernah dijalankan)
- [ ] `install.php` berhasil dan setelahnya menampilkan "sudah terpasang"
- [ ] Beranda dan halaman publik menampilkan data dari DB (ubah satu baris di DB, pastikan berubah di situs)
- [ ] Login benar/salah, lalu 5x salah memunculkan pesan terkunci
- [ ] Editor tidak bisa membuka `kelola-akun.php` (403)
- [ ] Tambah akun editor, reset password, nonaktifkan, hapus
- [ ] Keluar (tombol di sidebar) menghapus sesi

## Penyimpangan dari PRD
- `flyer_pkl` punya kolom tambahan `bidang` dan `kuota`.
- File tambahan: `includes/helpers.php`, `components.php`, `data-dummy.php`, `csrf.php`, `admin-header/footer.php`, `akun-saya.php`.
- Reset password hanya oleh superadmin (tanpa email), sesuai PRD v2.

## Belum dikerjakan
- Tahap C: CRUD kegiatan, galeri, flyer, mitra, profil, pengajar; upload ke Volume (`includes/upload.php`).
- Tahap D: plugin loader + plugin chat-agent, tema, pengaturan situs, tracking pengunjung, kelola FAQ.
- Logo (menyusul), konten asli (semua masih contoh).

## Instruksi untuk AI ke-3 (Tahap C + D)
- **Template halaman admin**: `require_once includes/auth.php; $admin = require_login();` (atau `require_role('superadmin')`),
  set `$admin_title` dan `$admin_active`, lalu `require includes/admin-header.php` ... `admin-footer.php`.
- **Nama file yang sudah dicantumkan di menu** (otomatis muncul begitu file dibuat di `admin/`):
  `kelola-kegiatan.php`, `kelola-galeri.php`, `kelola-flyer.php`, `kelola-mitra.php`, `kelola-profil.php`,
  `plugins.php`, `tema.php`, `pengaturan.php`. Kunci `$admin_active`: kegiatan, galeri, flyer, mitra, profil, plugins, tema, pengaturan.
- Semua form POST: `<?= csrf_field() ?>` + `csrf_check()` di awal handler; pesan lewat `flash_set('ok'|'err', ...)` + `redirect()`.
- Query: pakai `q_all/q_one/q_exec`; untuk INSERT yang butuh ID baru pakai `db()->lastInsertId()`.
- Gaya form/tabel/alert sudah ada di `admin.css` (`.form .field .input .row .table .alert .badge .btn-danger`).
- Upload: simpan ke `getenv('UPLOAD_DIR') ?: '/data/uploads'` dengan nama acak; di DB simpan NAMA FILE saja
  (fungsi `media()` dan `upload_url()` sudah mengurus tampilannya via `media.php`). Validasi MIME + `getimagesize`, batasi ukuran.
- Slug kegiatan harus unik (kolom `slug` UNIQUE); buat helper slugify.
- `kegiatan.created_by` isi dengan `$admin['id']`; editor boleh mengedit semua konten.
- Plugin: `hook('head_end')` dan `hook('footer_end')` sudah dipanggil di `header.php`/`footer.php`; isi fungsi `hook()` di
  `helpers.php` lewat `includes/plugin-loader.php`. `plugin_aktif($nama)` sudah membaca tabel `plugins`.
  Tabel `chat_faq` sudah terisi 4 FAQ contoh. Mesin chat dibuat bisa diganti (rule-based dulu).
- Tema: `header.php` sudah membaca `site('warna_bg'|'warna_aksen_1..3')`; tinggal dibuat form yang menulis ke `pengaturan_situs`
  (pakai `INSERT ... ON DUPLICATE KEY UPDATE`).
- Tracking pengunjung: tabel `visitor_log`; catat di `header.php` publik (hash IP + garam), jangan simpan IP mentah.

## Jalankan lokal
XAMPP/PHP 8+: buat database `tjkt` kosong, atur env `SETUP_TOKEN`, jalankan `php -S localhost:8000`, buka `/install.php`.
Tanpa database, halaman publik tetap tampil memakai data dummy.
