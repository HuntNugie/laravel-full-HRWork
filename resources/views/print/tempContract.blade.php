<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Perjanjian Kerja - {{ $contract->contract_number }}</title>

    <style>
        @page {
            size: A4;
            margin: 16mm 18mm 18mm 18mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #fff;
            color: #111;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.2pt;
            line-height: 1.52;
        }

        p {
            margin: 0 0 7px;
        }

        .letterhead,
        .parties,
        .salary-box,
        .signature-section {
            page-break-inside: avoid;
        }

        .letterhead-table,
        .identity-table,
        .salary-table,
        .schedule-table,
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .letterhead-table td {
            vertical-align: middle;
        }

        .logo-cell {
            width: 78px;
        }

        .logo {
            width: 62px;
            height: auto;
            display: block;
        }

        .company-cell {
            text-align: center;
        }

        .company-name {
            margin: 0 0 4px;
            font-size: 17pt;
            line-height: 1.15;
            font-weight: 700;
        }

        .company-detail {
            margin: 2px 0;
            font-size: 8.8pt;
            line-height: 1.35;
        }

        .header-line {
            margin-top: 10px;
            height: 4px;
            border-top: 2px solid #111;
            border-bottom: 1px solid #111;
        }

        .document-heading {
            text-align: center;
            margin: 21px 0 18px;
        }

        .document-heading h1 {
            margin: 0;
            font-size: 15pt;
            text-transform: uppercase;
        }

        .document-heading p {
            margin-top: 6px;
            font-size: 10pt;
        }

        .intro {
            text-align: justify;
        }

        .party-title,
        .article-title {
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        .parties {
            margin-top: 12px;
        }

        .identity-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .identity-table .label {
            width: 150px;
        }

        .identity-table .colon {
            width: 18px;
            text-align: center;
        }

        .article {
            margin-top: 15px;
            text-align: justify;
        }

        .article ol {
            margin: 5px 0 8px 23px;
            padding: 0;
        }

        .article li {
            margin-bottom: 5px;
            padding-left: 3px;
        }

        .salary-box {
            margin-top: 8px;
        }

        .salary-table {
            table-layout: fixed;
        }

        .salary-table th,
        .salary-table td {
            border: 1px solid #222;
            padding: 6px 7px;
            vertical-align: top;
        }

        .salary-table th {
            background: #f0f0f0;
            text-align: left;
        }

        .salary-table .amount {
            text-align: right;
            white-space: nowrap;
        }

        .summary-table td {
            border: 1px solid #222;
            padding: 6px 7px;
        }

        .summary-table {
            margin-top: 10px;
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table .label {
            width: 67%;
        }

        .summary-table .amount {
            width: 33%;
            text-align: right;
            white-space: nowrap;
        }

        .note {
            margin-top: 9px;
            padding: 8px 10px;
            border: 1px solid #999;
            font-size: 9pt;
        }

        .schedule-table {
            margin: 8px 0 10px;
            table-layout: fixed;
        }

        .schedule-table th,
        .schedule-table td {
            border: 1px solid #555;
            padding: 5px 6px;
        }

        .schedule-table th {
            background: #f2f2f2;
        }

        .signature-section {
            margin-top: 28px;
        }

        .signature-table {
            table-layout: fixed;
        }

        .signature-table td {
            width: 50%;
            padding: 0 14px;
            text-align: center;
            vertical-align: top;
        }

        .signature-city {
            margin-bottom: 4px;
        }

        .signature-space {
            height: 75px;
        }

        .signature-name {
            font-weight: 700;
            text-decoration: underline;
        }

        .small {
            font-size: 9pt;
        }

        .muted {
            color: #555;
        }

        .footer-note {
            margin-top: 15px;
            padding-top: 6px;
            border-top: 1px solid #ccc;
            text-align: center;
            color: #666;
            font-size: 8pt;
        }
    </style>
</head>

<body>
    <div>

        <header class="letterhead">
            <table class="letterhead-table">
                <tr>
                    <td class="logo-cell">
                        <img class="logo"
                            src="data:image/webp;base64,{{ base64_encode(file_get_contents(public_path('assets/logo-inovindo.webp'))) }}"
                            alt="Logo PT. Inovindo Digital Media">
                    </td>
                    <td class="company-cell">
                        <h1 class="company-name">PT. INOVINDO DIGITAL MEDIA</h1>
                        <p class="company-detail">Komplek Buana Citra Ciwastra No. D3, Kabupaten Bandung</p>
                        <p class="company-detail">WhatsApp: 0856-2251-196 &nbsp;|&nbsp; Website: www.inovindo.co.id</p>
                    </td>
                </tr>
            </table>
            <div class="header-line"></div>
        </header>

        <section class="document-heading">
            <h1>Perjanjian Kerja</h1>
            <p>Nomor: <strong>{{ $contract->contract_number }}</strong></p>
        </section>

        <section class="intro">
            <p>
                Pada hari ini, <strong>{{ now()->locale('id')->translatedFormat('l') }}</strong>, tanggal
                <strong>{{ now()->locale('id')->translatedFormat('d F Y') }}</strong>, bertempat di Bandung,
                telah dibuat dan ditandatangani Perjanjian Kerja oleh dan antara Para Pihak sebagai berikut.
            </p>
        </section>

        <section class="parties">
            <p class="party-title">1. PIHAK PERTAMA</p>
            <table class="identity-table">
                <tr>
                    <td class="label">Nama Perusahaan</td>
                    <td class="colon">:</td>
                    <td><strong>PT. Inovindo Digital Media</strong></td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td class="colon">:</td>
                    <td>Komplek Buana Citra Ciwastra No. D3, Kabupaten Bandung</td>
                </tr>
                <tr>
                    <td class="label">Diwakili Oleh</td>
                    <td class="colon">:</td>
                    <td><strong>{{ auth()->user()?->name ?? 'Novi Setia Nurviat' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Jabatan</td>
                    <td class="colon">:</td>
                    <td>{{ auth()->user()?->employees?->position?->name ?? 'Direktur' }}</td>
                </tr>
                <tr>
                    <td class="label">Jenis Usaha</td>
                    <td class="colon">:</td>
                    <td>{{ $companyBusinessType ?? '[lengkapi dari data perusahaan]' }}</td>
                </tr>
            </table>
        </section>

        <section class="parties">
            <p class="party-title">2. PIHAK KEDUA</p>
            <table class="identity-table">
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td class="colon">:</td>
                    <td><strong>{{ $employee->user->name }}</strong></td>
                </tr>
                <tr>
                    <td class="label">NIK</td>
                    <td class="colon">:</td>
                    <td>{{ $employeeProfile->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Nomor Karyawan</td>
                    <td class="colon">:</td>
                    <td>{{ $employee->employee_code }}</td>
                </tr>
                <tr>
                    <td class="label">Jenis Kelamin</td>
                    <td class="colon">:</td>
                    @if ($employeeProfile->gender === 'male')
                        <td>Laki Laki</td>
                    @else
                        <td>Perempuan</td>
                    @endif
                </tr>
                <tr>
                    <td class="label">Tanggal Lahir</td>
                    <td class="colon">:</td>
                    <td>{{ $employeeProfile->tanggal_lahir ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td class="colon">:</td>
                    <td>{{ $employeeAddress->full_address ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jabatan / Pekerjaan</td>
                    <td class="colon">:</td>
                    <td><strong>{{ $contract->position_name }}</strong></td>
                </tr>
            </table>
        </section>

        <section class="article">
            <p>
                Pihak Pertama dan Pihak Kedua selanjutnya secara bersama-sama disebut sebagai
                <strong>“Para Pihak”</strong>. Para Pihak sepakat untuk mengikatkan diri dalam hubungan kerja
                dengan ketentuan-ketentuan sebagaimana diatur dalam Perjanjian Kerja ini, dengan tetap tunduk
                pada peraturan perundang-undangan ketenagakerjaan yang berlaku, Peraturan Perusahaan,
                dan/atau Perjanjian Kerja Bersama apabila ada.
            </p>
        </section>

        <section class="article">
            <p class="article-title">PASAL 1 — JENIS DAN JANGKA WAKTU HUBUNGAN KERJA</p>
            @if ($contract->employement_type === 'pkwtt')
                <p>
                    Perjanjian ini merupakan <strong>Perjanjian Kerja Waktu Tidak Tertentu (PKWTT)</strong>.
                    Hubungan kerja berlaku sejak
                    <strong>{{ $contract->start_date?->locale('id')->translatedFormat('d F Y') }}</strong>
                    dan tidak ditentukan batas tanggal berakhirnya, kecuali hubungan kerja berakhir berdasarkan
                    pengunduran diri, kesepakatan, atau alasan dan prosedur pengakhiran hubungan kerja yang sah.
                </p>
            @elseif ($contract->employement_type === 'pkwt')
                <p>
                    Perjanjian ini merupakan <strong>Perjanjian Kerja Waktu Tertentu (PKWT)</strong> yang berlaku
                    sejak <strong>{{ $contract->start_date?->locale('id')->translatedFormat('d F Y') }}</strong>
                    sampai dengan
                    <strong>{{ $contract->end_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}</strong>.
                </p>
                <p>
                    PKWT ini tidak mensyaratkan masa percobaan kerja. Ketentuan mengenai jangka waktu,
                    berakhirnya hubungan kerja, dan hak Pihak Kedua mengikuti ketentuan peraturan perundang-undangan.
                </p>
            @elseif ($contract->employement_type === 'internship')
                <p>Hubungan kerja ini tercatat sebagai <strong>program internship/magang</strong> sesuai dokumen dan
                    ketentuan yang berlaku.</p>
            @else
                <p>Jenis hubungan kerja: <strong>{{ strtoupper($contract->employement_type) }}</strong>.</p>
            @endif
        </section>

        <section class="article">
            <p class="article-title">PASAL 2 — JABATAN, TUGAS, DAN TEMPAT KERJA</p>
            <p>
                Pihak Kedua ditempatkan sebagai <strong>{{ $contract->position_name }}</strong>.
            </p>
            <p>
                Pihak Kedua wajib melaksanakan pekerjaan sesuai uraian jabatan, target pekerjaan,
                SOP, instruksi atasan yang berwenang, serta kebutuhan operasional perusahaan sepanjang
                masih berkaitan dengan hubungan kerja dan tidak bertentangan dengan ketentuan hukum.
            </p>
            <p>
                Tempat kerja utama Pihak Kedua adalah tempat kerja yang ditetapkan Pihak Pertama.
                Penyesuaian tempat kerja karena kebutuhan operasional dilaksanakan sesuai ketentuan perusahaan dan
                hukum.
            </p>
        </section>

        <section class="article">
            <p class="article-title">PASAL 3 — WAKTU KERJA DAN PRESENSI</p>
            <p>
                Pola kerja perusahaan adalah <strong>6 (enam) hari kerja</strong> dari Senin sampai dengan Sabtu.
                Jadwal operasional yang digunakan pada sistem perusahaan adalah sebagai berikut:
            </p>
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th>Waktu</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Senin–Jumat</td>
                        <td>09.00–17.00</td>
                        <td>Waktu istirahat mengikuti kebijakan perusahaan</td>
                    </tr>
                    <tr>
                        <td>Sabtu</td>
                        <td>09.00–14.00</td>
                        <td>Sesuai jadwal operasional</td>
                    </tr>
                    <tr>
                        <td>Minggu</td>
                        <td>Libur</td>
                        <td>Istirahat mingguan</td>
                    </tr>
                </tbody>
            </table>
            <p>
                Pihak Kedua wajib melakukan presensi masuk dan pulang melalui sistem yang ditetapkan perusahaan.
                Ketidakhadiran, keterlambatan, izin, sakit, dan cuti wajib disampaikan melalui prosedur yang berlaku.
            </p>
            <p>
                Kerja lembur hanya dilakukan sesuai kebutuhan dan persetujuan perusahaan serta mengikuti ketentuan
                waktu kerja dan pengupahan yang berlaku.
            </p>
        </section>

        <section class="article">
            <p class="article-title">PASAL 4 — UPAH DAN TUNJANGAN</p>
            <p>
                Pihak Kedua menerima upah dasar sebesar
                <strong>Rp{{ number_format($dailySalary, 0, ',', '.') }}</strong>
                untuk setiap hari kerja sesuai dasar pengupahan perusahaan.
            </p>

            <div class="salary-box">
                <table class="salary-table">
                    <thead>
                        <tr>
                            <th style="width:58%">Komponen</th>
                            <th style="width:42%">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Upah dasar per hari kerja</strong></td>
                            <td class="amount"><strong>Rp{{ number_format($dailySalary, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Total Gaji 1 bulan penuh</strong></td>
                            <td class="amount"><strong>Rp{{ number_format($monthlySalary, 0, ',', '.') }}</strong></td>
                        </tr>

                        @foreach ($benefits as $benefit)
                            <tr>
                                <td>
                                    <strong>{{ $benefit['name'] }}</strong>

                                    @if ($benefit['description'])
                                        <br>
                                        <span class="muted">
                                            {{ $benefit['description'] }}
                                        </span>
                                    @endif
                                </td>

                                <td class="amount">
                                    Rp{{ number_format($benefit['amount'], 0, ',', '.') }}
                                    / hari
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td><strong>Total tunjangan aktual per hari</strong></td>
                            <td class="amount"><strong>Rp{{ number_format($dailyBenefitTotal, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Total tunjangan untuk 1 bulan penuh</strong></td>
                            <td class="amount">
                                <strong>Rp{{ number_format($monthlyBenefitTotal, 0, ',', '.') }}</strong>
                            </td>

                        </tr>
                        <tr>
                            <td>
                                <strong>Total Tunjangan / Hari</strong>
                            </td>

                            <td class="amount">
                                <strong>
                                    Rp{{ number_format($dailyBenefitTotal, 0, ',', '.') }}
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Total Tunjangan / Bulan</strong>
                            </td>

                            <td class="amount">
                                <strong>
                                    Rp{{ number_format($monthlyBenefitTotal, 0, ',', '.') }}
                                </strong>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Total Gaji + Tunjangan / Hari</strong>
                            </td>

                            <td class="amount">
                                <strong>
                                    Rp{{ number_format($dailyTotalCompensation, 0, ',', '.') }}
                                </strong>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Total Gaji + Tunjangan / Bulan</strong>
                            </td>

                            <td class="amount">
                                <strong>
                                    Rp{{ number_format($monthlyTotalCompensation, 0, ',', '.') }}
                                </strong>
                            </td>
                        </tr>
                    </tbody>
                </table>





                <div class="note">
                    <strong>Keterangan:</strong> Nilai “1 bulan penuh” merupakan estimasi berdasarkan jumlah hari kerja
                    Senin–Sabtu pada bulan acuan {{ $referenceMonth->locale('id')->translatedFormat('F Y') }}.
                    Pembayaran aktual mengikuti kehadiran, tanggal efektif hubungan kerja, hari libur resmi,
                    cuti/izin/sakit, lembur, potongan yang sah, serta kebijakan penggajian perusahaan.
                </div>
            </div>

            <p>
                Pembayaran upah dilakukan sesuai siklus pembayaran penggajian perusahaan. Pemotongan hanya dilakukan
                sesuai ketentuan peraturan perundang-undangan dan/atau kebijakan yang sah.
            </p>
        </section>

        <section class="article">
            <p class="article-title">PASAL 5 — HAK DAN KEWAJIBAN PIHAK KEDUA</p>
            <p>Pihak Kedua berhak:</p>
            <ol>
                <li>Menerima upah dan tunjangan sesuai Perjanjian Kerja dan ketentuan yang berlaku.</li>
                <li>Menggunakan hak istirahat, cuti, sakit, dan izin sesuai ketentuan.</li>
                <li>Mendapatkan lingkungan kerja yang aman dan layak.</li>
                <li>Mendapatkan perlindungan jaminan sosial sesuai ketentuan kepesertaan.</li>
            </ol>
            <p>Pihak Kedua berkewajiban:</p>
            <ol>
                <li>Melaksanakan pekerjaan dengan itikad baik, disiplin, dan profesional.</li>
                <li>Mematuhi Perjanjian Kerja, Peraturan Perusahaan, SOP, dan instruksi kerja yang sah.</li>
                <li>Menjaga kerahasiaan data, dokumen, sistem, akun, kode sumber, dan informasi perusahaan.</li>
                <li>Menjaga serta mengembalikan aset perusahaan pada saat diminta atau hubungan kerja berakhir.</li>
                <li>Menjaga nama baik perusahaan dan menghindari tindakan yang merugikan perusahaan.</li>
            </ol>
        </section>

        <section class="article">
            <p class="article-title">PASAL 6 — WAKTU ISTIRAHAT DAN CUTI</p>
            <p>
                Pihak Kedua memperoleh waktu istirahat dan cuti sesuai peraturan perundang-undangan,
                Peraturan Perusahaan, dan/atau Perjanjian Kerja Bersama yang berlaku.
            </p>
            <p>
                Pelaksanaan cuti tahunan dilakukan melalui prosedur perusahaan dan disesuaikan dengan kebutuhan
                operasional.
                Untuk pengaturan operasional internal, pengajuan cuti tahunan reguler pada bulan berjalan dapat dibatasi
                paling banyak <strong>1 (satu) hari kerja per bulan</strong>, sepanjang tidak menghapus atau mengurangi
                hak minimum cuti tahunan yang diberikan oleh peraturan perundang-undangan.
            </p>
            <p>
                Ketentuan tersebut tidak membatasi hak atas sakit, izin karena alasan penting, cuti khusus,
                atau keadaan lain yang berdasarkan hukum dan kebijakan perusahaan diperlakukan secara tersendiri.
            </p>
        </section>

        <section class="article">
            <p class="article-title">PASAL 7 — KEHADIRAN, IZIN, DAN KETIDAKHADIRAN</p>
            <p>
                Dalam hal berhalangan hadir, Pihak Kedua wajib memberikan pemberitahuan kepada atasan dan/atau bagian
                yang ditunjuk serta menyampaikan keterangan dan bukti pendukung sesuai prosedur.
            </p>
            <p>
                Ketidakhadiran tanpa pemberitahuan selama lebih dari <strong>3 (tiga) hari kerja</strong> dapat menjadi
                dasar pemeriksaan pelanggaran disiplin dan tindakan sesuai Peraturan Perusahaan atau Perjanjian Kerja
                ini.
            </p>
            <p>
                Ketentuan internal tersebut tidak mengesampingkan ketentuan hukum mengenai mangkir. Dalam hal pekerja
                mangkir selama <strong>5 (lima) hari kerja atau lebih berturut-turut</strong> tanpa keterangan tertulis
                yang dilengkapi bukti sah, perusahaan dapat menempuh prosedur pengakhiran hubungan kerja sesuai hukum,
                termasuk pemanggilan secara patut dan tertulis sesuai ketentuan yang berlaku.
            </p>
        </section>

        <section class="article">
            <p class="article-title">PASAL 8 — TATA TERTIB DAN SURAT PERINGATAN</p>
            <p>
                Pelanggaran terhadap Perjanjian Kerja, Peraturan Perusahaan, Perjanjian Kerja Bersama, SOP,
                atau kebijakan perusahaan dapat dikenakan tindakan disiplin sesuai tingkat pelanggarannya.
            </p>
            <ol>
                <li>Surat Peringatan Pertama (SP-1);</li>
                <li>Surat Peringatan Kedua (SP-2);</li>
                <li>Surat Peringatan Ketiga/terakhir (SP-3).</li>
            </ol>
            <p>
                Apabila setelah SP-3 Pihak Kedua kembali melakukan pelanggaran yang memenuhi ketentuan untuk tindakan
                lebih lanjut, perusahaan dapat melakukan Pemutusan Hubungan Kerja sesuai prosedur dan ketentuan hukum.
                Penerbitan SP dan PHK tidak semata-mata didasarkan pada jumlah hari ketidakhadiran, tetapi pada fakta,
                bukti, jenis pelanggaran, ketentuan internal, dan prosedur yang berlaku.
            </p>
        </section>

        <section class="article">
            <p class="article-title">PASAL 9 — KERAHASIAAN DAN INFORMASI PERUSAHAAN</p>
            <p>
                Pihak Kedua wajib menjaga kerahasiaan seluruh informasi perusahaan, termasuk data pelanggan, data
                karyawan,
                dokumen internal, informasi keuangan, strategi bisnis, kredensial, sistem, konfigurasi, kode sumber,
                dokumentasi teknis, serta informasi lain yang dinyatakan rahasia.
            </p>
            <p>
                Kewajiban kerahasiaan tetap berlaku setelah hubungan kerja berakhir sepanjang informasi tersebut masih
                bersifat rahasia atau dilindungi oleh ketentuan hukum.
            </p>
        </section>

        <section class="article">
            <p class="article-title">PASAL 10 — ASET DAN FASILITAS PERUSAHAAN</p>
            <p>
                Seluruh perangkat, dokumen, akses sistem, akun, dan fasilitas yang diberikan untuk pekerjaan wajib
                dijaga,
                digunakan sesuai kewenangan, dan dikembalikan pada saat hubungan kerja berakhir atau ketika diminta.
            </p>
        </section>

        <section class="article">
            <p class="article-title">PASAL 11 — KESELAMATAN KERJA DAN PERILAKU PROFESIONAL</p>
            <p>
                Pihak Kedua wajib mematuhi ketentuan keselamatan dan kesehatan kerja serta menjaga perilaku profesional
                di lingkungan kerja. Diskriminasi, intimidasi, kekerasan, pelecehan, dan tindakan lain yang melanggar
                hukum atau kebijakan perusahaan dilarang.
            </p>
        </section>

        <section class="article">
            <p class="article-title">PASAL 12 — JAMINAN SOSIAL DAN HAK KETENAGAKERJAAN</p>
            <p>
                Pihak Pertama memenuhi hak ketenagakerjaan dan program jaminan sosial sesuai status hubungan kerja
                dan peraturan perundang-undangan yang berlaku.
            </p>
        </section>

        <section class="article">
            <p class="article-title">PASAL 13 — PENGUNDURAN DIRI</p>
            <p>
                Pengunduran diri dilakukan secara tertulis paling lambat <strong>30 (tiga puluh) hari</strong> sebelum
                tanggal efektif, tidak terikat dalam ikatan dinas, dan tetap melaksanakan kewajiban sampai tanggal
                efektif,
                kecuali ditentukan lain menurut ketentuan yang berlaku.
            </p>
            <p>
                Pihak Kedua wajib melakukan serah terima pekerjaan, dokumen, aset, akun, dan akses perusahaan sebelum
                tanggal efektif berakhirnya hubungan kerja.
            </p>
        </section>

        <section class="article">
            <p class="article-title">PASAL 14 — PENGAKHIRAN HUBUNGAN KERJA</p>
            <p>
                Pengakhiran hubungan kerja dilakukan berdasarkan alasan, prosedur, dan hak-hak Para Pihak sesuai
                peraturan perundang-undangan, Peraturan Perusahaan, Perjanjian Kerja Bersama, dan Perjanjian Kerja ini.
            </p>
            <p>
                Dalam hal terdapat pelanggaran yang telah melalui mekanisme peringatan sebagaimana diatur dalam Pasal 8,
                perusahaan dapat melakukan PHK apabila alasan dan prosedur PHK telah terpenuhi berdasarkan hukum.
            </p>
            @if ($contract->employement_type === 'pkwt')
                <p>
                    Untuk PKWT, berakhir atau diakhirinya hubungan kerja dilakukan dengan memperhatikan ketentuan
                    mengenai
                    jangka waktu dan hak kompensasi PKWT sesuai peraturan perundang-undangan.
                </p>
            @endif
        </section>

        <section class="article">
            <p class="article-title">PASAL 15 — PERUBAHAN PERJANJIAN</p>
            <p>
                Perubahan atas syarat material Perjanjian Kerja ini dilakukan berdasarkan kesepakatan Para Pihak
                dan dituangkan dalam addendum atau dokumen perubahan yang sah. Ketentuan perusahaan yang berlaku
                menjadi bagian dari hubungan kerja sepanjang tidak bertentangan dengan hukum.
            </p>
        </section>

        <section class="article">
            <p class="article-title">PASAL 16 — PENYELESAIAN PERSELISIHAN</p>
            <p>
                Perselisihan diupayakan terlebih dahulu melalui musyawarah dan mekanisme internal hubungan industrial.
                Apabila tidak tercapai penyelesaian, Para Pihak menempuh mekanisme penyelesaian perselisihan hubungan
                industrial sesuai peraturan perundang-undangan.
            </p>
        </section>

        <section class="article">
            <p class="article-title">PASAL 17 — PENUTUP</p>
            <p>
                Perjanjian Kerja ini dibuat berdasarkan kesepakatan Para Pihak, dalam keadaan sadar dan tanpa paksaan
                dari pihak mana pun. Para Pihak menyatakan telah membaca, memahami, dan menyetujui seluruh ketentuan
                dalam Perjanjian Kerja ini.
            </p>
            <p>
                Apabila terdapat ketentuan dalam Perjanjian Kerja ini yang bertentangan dengan peraturan
                perundang-undangan,
                ketentuan tersebut tidak mengurangi keberlakuan ketentuan lainnya dan akan ditafsirkan serta disesuaikan
                sejauh diperlukan agar tetap berlaku secara sah.
            </p>
            <p>
                Perjanjian Kerja ini dibuat sekurang-kurangnya dalam 2 (dua) rangkap yang masing-masing mempunyai
                kekuatan hukum yang sama dan diberikan kepada masing-masing pihak.
            </p>
        </section>

        <section class="signature-section">
            <p>
                Demikian Perjanjian Kerja ini dibuat untuk dilaksanakan dengan penuh tanggung jawab oleh Para Pihak.
            </p>

            <table class="signature-table">
                <tr>
                    <td>
                        <p class="signature-city">Bandung, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
                        <p><strong>PIHAK PERTAMA</strong></p>
                        <p>PT. Inovindo Digital Media</p>
                        <div class="signature-space"></div>
                        <p class="signature-name">{{ auth()->user()?->name ?? 'Novi Setia Nurviat' }}</p>
                        <p class="small">{{ auth()->user()?->employees?->position?->name ?? 'Direktur' }}</p>
                    </td>
                    <td>
                        <p class="signature-city">Bandung, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
                        <p><strong>PIHAK KEDUA</strong></p>
                        <p>Pekerja</p>
                        <div class="signature-space"></div>
                        <p class="signature-name">{{ $employee->user->name }}</p>
                        <p class="small">{{ $employee->employee_code }}</p>
                    </td>
                </tr>
            </table>
        </section>

        <div class="footer-note">
            Nomor Perjanjian: <strong>{{ $contract->contract_number }}</strong>
        </div>

    </div>
</body>

</html>
