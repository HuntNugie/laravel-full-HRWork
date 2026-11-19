# HRWork — HRIS for Work

HRWork (*HRIS for Work*) adalah aplikasi Human Resource Information System berbasis Laravel dan Livewire yang berfokus pada **Karyawan**, **HRD**, dan **Administrator**. Layanan inti: data karyawan, jabatan (Position), kontrak kerja, presensi, cuti/izin, payroll, surat peringatan, resignation, dan termination. Work Management dan struktur organisasi (Divisi/Team) sudah dipensiunkan dari alur HRIS aktif.

## Ruang Lingkup dan Hak Akses

- **Karyawan:** layanan mandiri (presensi, data diri, kontrak, payroll, cuti/izin, riwayat SP, resignation).
- **HRD (`hr`):** data dan jabatan karyawan, kontrak, payroll, cuti/izin, presensi, SP, resignation, termination, serta AI Assistant dan CV Analyzer.
- **Administrator (`administrator`):** pengelolaan akun, status aktif akun, penggantian password, role dan permission; tidak otomatis mendapat akses operasional HRD.
- **Superadmin (`super-admin`):** akses keseluruhan fitur melalui `Gate::before`, termasuk fungsi semua peran.
- Jabatan **Manager**, **Supervisor**, dan **General Manager** tetap boleh ada sebagai nama Position dalam data/kontrak, **bukan** role atau penugasan organisasi.
- Handover resignation/termination tetap berupa item manual HRD. Clearance organisasi lama hanya arsip dan tidak lagi memblokir penyelesaian.

## Migrasi dari Struktur Lama (Database Eksisting)

1. Pastikan database yang akan diperbarui memang hanya berisi **data dummy yang boleh dihapus permanen**. Untuk database dengan data nyata, lakukan backup terverifikasi dan peninjauan retensi sebelum melanjutkan. Jangan jalankan `migrate:fresh` di database nyata.
2. Checkout branch `refactor/remove-work-management-hris`, install dependensi dengan `composer install`, lalu jalankan `php artisan migrate`.
3. Migrasi `2026_11_19_000000_archive_work_management_tables` terlebih dahulu melepaskan referensi `task_id` dari handover dan mengarsipkan sembilan tabel Work Management.
4. Migrasi `2026_11_19_000100_archive_organization_structure` melepaskan relasi `employees.team_id` dan mengarsipkan tabel `teams` serta `divisis`.
5. Migrasi `2026_11_19_000200_drop_archived_work_management_tables` dan `2026_11_19_000300_drop_archived_organization_structure_tables` **menghapus permanen 11 tabel arsip**, dalam urutan yang menjaga dependensi foreign key. Migrasi `2026_11_19_000400_drop_retired_organization_and_task_reference_columns` lalu menghapus `employees.legacy_team_id` serta kedua kolom handover `legacy_task_id`. Data yang dihapus tidak dapat dipulihkan dengan `migrate:rollback`.
6. Jalankan `php artisan db:seed --class=PermissionSeeder`, `php artisan db:seed --class=RoleSeeder`, lalu `php artisan db:seed --class=RolePermissionSeeder`. Tahap ini mencabut permission divisi/team, mempensiunkan role manager/supervisor lama, dan mengalihkan akun karyawan terdampak ke role employee.
7. Pastikan pengguna Administrator mempunyai permission pengelolaan akun dan HRD mempunyai permission terkait Karyawan dan Termination. Jalankan `php artisan permission:cache-reset`.
8. Jalankan `php artisan test` dan lakukan uji manual login, role, pembuatan karyawan/kontrak, detail karyawan, presensi, cuti, penggajian dan exit. Deploy hanya setelah hasilnya sesuai.

### Seeder

- `php artisan db:seed` menjalankan katalog hak akses, pembuatan **Superadmin** berdasarkan `SUPERADMIN_EMAIL` dan **`SUPERADMIN_PASSWORD` wajib di `.env`**, master bank dan konfigurasi HR. Password tersebut tidak dibuat otomatis; hindari hard-coded credential produksi.
- Seeder `HRISDevelopmentSeeder` kini **hanya untuk development** dan tidak dipanggil oleh `DatabaseSeeder`: jalankan secara sengaja dengan `php artisan db:seed --class=HRISDevelopmentSeeder` di lingkungan pengujian. Seeder ini membuat 50 akun contoh dengan Position tanpa divisi/team. **Jangan jalankan di produksi.**
- Seeder ini tidak menghapus data karyawan aktif. Dump `hris.sql` lama merupakan rekaman historis, **bukan** skema target setelah migrasi penghapusan.

## Laravel

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
