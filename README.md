# HRWork — Kerja Praktik

Cabang terpisah dari `main`, berfokus pada administrasi data karyawan dan kontrak kerja. **Status: kandidat implementasi, menunggu verifikasi runtime.** Jangan gunakan basis data branch main untuk branch ini.

## Ruang lingkup

- Administrator: dashboard akun, pembuatan/penonaktifan akun HRD, reset kata sandi, permission individual.
- HRD: dashboard, karyawan (tanpa akun login), kontrak, jabatan, benefit, jenis cuti sesuai permission.
- Kontrak: draft, aktivasi, berakhir otomatis, pengakhiran, nomor unik, riwayat status, snapshot benefit/hak cuti dan cetak PDF.
- Presensi, payroll, pengajuan cuti/izin/sakit, kedisiplinan, alur resign/PHK, CV Analyzer dan self-service karyawan tidak termasuk.

## Instalasi lokal

Gunakan PHP >= 8.4, Composer, Node.js >= 22.12, serta ekstensi PHP sesuai Composer. Gunakan direktori kerja terpisah.

```bash
git clone -b kerjaPraktek --single-branch https://github.com/HuntNugie/laravel-full-HRWork.git hrwork-kp
cd hrwork-kp
composer install
cp .env.example .env
touch database/kerja_praktek.sqlite
php artisan key:generate
```

Isi `.env` sebelum migrasi:

```dotenv
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/hrwork-kp/database/kerja_praktek.sqlite
HRWORK_ADMIN_EMAIL=
HRWORK_ADMIN_PASSWORD=
```

Gunakan email admin dan password kuat minimal 12 karakter. Jangan commit file .env.

```bash
php artisan migrate --seed
php artisan storage:link
npm ci
npm run build
php artisan serve
```

Seeder mengisi wilayah, bank, katalog permission, dua role dan administrator awal; tidak membuat akun demo otomatis. Login administrator, buat akun HRD, lalu pilih permission masing-masing HRD. Role hr tidak otomatis mendapatkan permission operasional.

## AI Assistant (opsional)

Chat WireKit dari main dan Laravel AI menggunakan gateway 9Router/OpenAI-compatible. Lima tool read-only mengikuti permission HRD. Tidak ada NIK, alamat, rekening, kontak pribadi, gaji atau catatan bebas pada hasil tool. Prompt dan hasil tool dapat diteruskan ke provider; perusahaan harus menyetujui provider dan pemrosesan data sebelum aktivasi.

Tambahkan konfigurasi ke .env lokal (jangan commit API key):

```dotenv
HRWORK_AI_ENABLED=true
OPENAI_COMPATIBLE_URL=https://router.huntnugie.my.id/v1
OPENAI_COMPATIBLE_API_KEY=
OPENAI_COMPATIBLE_MODEL=op-key/gemini-3.8-flash
DEBUGBAR_COLLECTORS_AI=false
```

Isi URL gateway, API key dan model yang valid. Seperti branch main, nonaktifkan AI Collector Debugbar dengan DEBUGBAR_COLLECTORS_AI=false karena collector 4.4.x tidak kompatibel dengan Laravel AI v1. Perubahan .env.example tidak otomatis mengubah .env lokal. Untuk database KP yang sudah ada jalankan `php artisan db:seed --class=PermissionSeeder` dan `php artisan optimize:clear`. Administrator memberi permission **view-ai-assistant** melalui Hak Akses HRD. Permission AI tidak otomatis memberi akses karyawan, kontrak atau jabatan. Tidak perlu migration ulang.

## Database

Migrasi domain KP dikonsolidasikan dalam satu file schema baru. Migrasi infrastruktur Laravel, Spatie dan wilayah tetap digunakan. Tidak tersedia jalur upgrade in-place dari database main; migrasi data lama harus dirancang terpisah bila dibutuhkan. Jangan menjalankan migrate:fresh pada database yang berisi data penting.

## Kontrak

Jabatan, benefit dan jenis cuti harus dibuat sebelum penyusunan kontrak. Kontrak aktif harus berlaku hari ini; kontrak masa depan disimpan sebagai draft. Aktifkan draft saat tanggal mulai tiba. Scheduler menjalankan app:expire-contracts setiap hari. Untuk development jalankan php artisan schedule:work; pada deployment gunakan scheduler Laravel setiap menit.

Identitas pihak perusahaan pada PDF mengikuti nama akun HRD yang sedang login dan mencetak, dengan jabatan HRD. Pencetakan ulang oleh akun HRD lain akan menggunakan identitas akun tersebut. Ini tidak otomatis memberikan kewenangan menandatangani kontrak secara hukum.

PDF membutuhkan Chrome yang dapat dijalankan PHP. Lihat variabel LARAVEL_PDF_CHROME_* pada .env.example. Estimasi gaji bulanan memakai hari kerja pada bulan tanggal mulai kontrak, default Senin–Sabtu dari konfigurasi warisan; konfirmasikan kebijakan perusahaan dan sesuaikan config/hrwork.php sebelum penggunaan.

## Pengujian

```bash
php artisan test
php artisan route:list
npm run build
```

Uji manual login kedua role, penolakan akses URL langsung, pemberian/pencabutan permission, tambah karyawan tanpa user, CRUD master, draft/aktivasi/expiry/pengakhiran kontrak, riwayat snapshot dan render PDF. PHPUnit memakai SQLite in-memory. Paket Leaflet yang tidak lagi dipakai dapat dibersihkan berikut lockfile melalui Composer/npm di lingkungan development; jangan mengedit lockfile secara manual.
