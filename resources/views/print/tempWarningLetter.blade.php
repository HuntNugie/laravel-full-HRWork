<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>
        Surat Peringatan - {{ $warningLetter->letter_number }}
    </title>

    <style>
        @page {
            size: A4;
            margin: 18mm 20mm 20mm 20mm;
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
            font-size: 11pt;
            line-height: 1.6;
        }

        p {
            margin: 0 0 8px;
        }

        .letterhead {
            page-break-inside: avoid;
        }

        .letterhead-table,
        .identity-table,
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
            margin: 28px 0 24px;
            text-align: center;
        }

        .document-heading h1 {
            margin: 0;
            font-size: 16pt;
            font-weight: 700;
            text-transform: uppercase;
        }

        .document-heading .number {
            margin-top: 7px;
            font-size: 10.5pt;
        }

        .recipient {
            margin-top: 12px;
        }

        .identity-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .identity-table .label {
            width: 140px;
        }

        .identity-table .colon {
            width: 18px;
            text-align: center;
        }

        .section {
            margin-top: 24px;
            text-align: justify;
        }

        .section-title {
            margin-bottom: 9px;
            font-weight: 700;
        }

        .level-box {
            margin-top: 18px;
            padding: 10px 12px;
            border: 1px solid #222;
            text-align: center;
        }

        .level-box .label {
            font-size: 9pt;
            text-transform: uppercase;
        }

        .level-box .level {
            margin-top: 3px;
            font-size: 14pt;
            font-weight: 700;
        }

        .reason-box {
            margin-top: 10px;
            padding: 12px 14px;
            border: 1px solid #555;
        }

        .description {
            margin-top: 10px;
            white-space: pre-line;
        }

        .warning-box {
            margin-top: 24px;
            padding: 12px 14px;
            border: 1px solid #222;
        }

        .warning-box p:last-child {
            margin-bottom: 0;
        }

        .signature-section {
            margin-top: 48px;
            page-break-inside: avoid;
        }

        .signature-table {
            table-layout: fixed;
        }

        .signature-table td {
            width: 50%;
            padding: 0 18px;
            text-align: center;
            vertical-align: top;
        }

        .signature-space {
            height: 85px;
        }

        .signature-name {
            margin-bottom: 2px;
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
            margin-top: 24px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
            text-align: center;
            color: #666;
            font-size: 8pt;
        }
    </style>
</head>

<body>

    <div>

        {{-- =========================================================
        KOP SURAT
        ========================================================== --}}
        <header class="letterhead">

            <table class="letterhead-table">
                <tr>

                    <td class="logo-cell">
                        <img class="logo"
                            src="data:image/webp;base64,{{ base64_encode(file_get_contents(public_path('assets/logo-inovindo.webp'))) }}"
                            alt="Logo PT. Inovindo Digital Media">
                    </td>

                    <td class="company-cell">

                        <h1 class="company-name">
                            PT. INOVINDO DIGITAL MEDIA
                        </h1>

                        <p class="company-detail">
                            Komplek Buana Citra Ciwastra No. D3, Kabupaten Bandung
                        </p>

                        <p class="company-detail">
                            WhatsApp: 0856-2251-196
                            &nbsp;|&nbsp;
                            Website: www.inovindo.co.id
                        </p>

                    </td>

                </tr>
            </table>

            <div class="header-line"></div>

        </header>


        {{-- =========================================================
        JUDUL DOKUMEN
        ========================================================== --}}
        <section class="document-heading">

            <h1>
                Surat Peringatan
            </h1>

            <p class="number">
                Nomor:
                <strong>
                    {{ $warningLetter->letter_number ?? '-' }}
                </strong>
            </p>

        </section>


        {{-- =========================================================
        PENERIMA
        ========================================================== --}}
        <section class="recipient">

            <p>
                Dengan hormat,
            </p>

            <p>
                Surat Peringatan ini diberikan kepada:
            </p>

            <table class="identity-table">

                <tr>
                    <td class="label">
                        Nama Karyawan
                    </td>

                    <td class="colon">
                        :
                    </td>

                    <td>
                        <strong>
                            {{ $warningLetter->employee->user?->name ?? '-' }}
                        </strong>
                    </td>
                </tr>

                <tr>
                    <td class="label">
                        Nomor Karyawan
                    </td>

                    <td class="colon">
                        :
                    </td>

                    <td>
                        {{ $warningLetter->employee->employee_code ?? '-' }}
                    </td>
                </tr>

            </table>

        </section>


        {{-- =========================================================
        LEVEL SP
        ========================================================== --}}
        <div class="level-box">

            <div class="label">
                Tingkat Surat Peringatan
            </div>

            <div class="level">
                {{ $warningLetter->warning_level }}
            </div>

        </div>


        {{-- =========================================================
        ALASAN
        ========================================================== --}}
        <section class="section">

            <p class="section-title">
                Dasar Pemberian Surat Peringatan
            </p>

            <div class="reason-box">
                {{ $warningLetter->reason }}
            </div>

        </section>


        {{-- =========================================================
        KETERANGAN
        ========================================================== --}}
        @if ($warningLetter->description)
            <section class="section">

                <p class="section-title">
                    Uraian Pelanggaran
                </p>

                <div class="description">
                    {{ $warningLetter->description }}
                </div>

            </section>
        @endif


        {{-- =========================================================
        ISI PERINGATAN
        ========================================================== --}}
        <section class="section">

            <p>
                Berdasarkan hasil pemeriksaan dan pertimbangan perusahaan
                terhadap kejadian tersebut, Surat Peringatan ini diberikan
                kepada yang bersangkutan sebagai bagian dari pembinaan dan
                penegakan disiplin kerja.
            </p>

            <p>
                Yang bersangkutan diharapkan untuk melakukan perbaikan,
                mematuhi ketentuan perusahaan, serta melaksanakan kewajiban
                pekerjaan sesuai dengan peraturan dan kebijakan yang berlaku.
            </p>

            <div class="warning-box">

                <p>
                    <strong>Perhatian:</strong>
                </p>

                <p>
                    Surat Peringatan ini merupakan catatan tindakan disiplin
                    perusahaan dan wajib diperhatikan oleh yang bersangkutan.
                    Pelanggaran berikutnya dapat menjadi bahan pertimbangan
                    perusahaan untuk melakukan tindakan disiplin sesuai
                    tingkat pelanggaran dan ketentuan yang berlaku.
                </p>

            </div>

        </section>


        {{-- =========================================================
        PENUTUP
        ========================================================== --}}
        <section class="section">

            <p>
                Demikian Surat Peringatan ini dibuat untuk dapat dipahami
                dan dilaksanakan sebagaimana mestinya.
            </p>

        </section>


        {{-- =========================================================
        TANDA TANGAN
        ========================================================== --}}
        <section class="signature-section">

            <table class="signature-table">

                <tr>

                    <td>

                        <p>
                            Bandung,
                            {{ $warningLetter->issued_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}
                        </p>

                        <p>
                            <strong>
                                Yang Menerbitkan
                            </strong>
                        </p>

                        <p>
                            PT. Inovindo Digital Media
                        </p>

                        <div class="signature-space"></div>

                        <p class="signature-name">
                            {{ $warningLetter->issuer?->name ?? '-' }}
                        </p>

                    </td>


                    <td>

                        <p>
                            Bandung,
                            {{ $warningLetter->issued_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}
                        </p>

                        <p>
                            <strong>
                                Yang Menerima
                            </strong>
                        </p>

                        <p>
                            Karyawan
                        </p>

                        <div class="signature-space"></div>

                        <p class="signature-name">
                            {{ $warningLetter->employee->user?->name ?? '-' }}
                        </p>

                        <p class="small">
                            {{ $warningLetter->employee->employee_code ?? '-' }}
                        </p>

                    </td>

                </tr>

            </table>

        </section>


        {{-- =========================================================
        FOOTER
        ========================================================== --}}
        <div class="footer-note">

            Nomor Surat Peringatan:
            <strong>
                {{ $warningLetter->letter_number ?? '-' }}
            </strong>

        </div>

    </div>

</body>

</html>
