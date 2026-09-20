<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Payroll - {{ $period->name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 12mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #fff;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            color: #111827;
            font-size: 8.2pt;
            line-height: 1.35;
        }

        .brand {
            display: table;
            width: 100%;
            table-layout: fixed;
            padding-bottom: 8px;
            border-bottom: 1.5px solid #111827;
        }

        .brand-logo {
            display: table-cell;
            width: 60px;
            vertical-align: middle;
        }

        .brand-logo img {
            width: 45px;
            max-height: 48px;
            display: block;
        }

        .brand-info {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding-right: 45px;
        }

        .brand-info h1 {
            margin: 0 0 2px;
            font-size: 13pt;
        }

        .brand-info p {
            margin: 1px 0;
            font-size: 7.5pt;
            color: #4b5563;
        }

        .title {
            text-align: center;
            margin: 10px 0;
        }

        .title h2 {
            margin: 0;
            font-size: 13pt;
        }

        .title p {
            margin: 2px 0 0;
            color: #4b5563;
            font-size: 8pt;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .meta td {
            padding: 3px 0;
        }

        .meta td:last-child {
            text-align: right;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
        }

        .report th,
        .report td {
            border: 1px solid #d1d5db;
            padding: 5px 5px;
        }

        .report th {
            background: #f3f4f6;
            font-size: 7.3pt;
            text-transform: uppercase;
            text-align: left;
        }

        .report th.num,
        .report td.num {
            text-align: right;
        }

        .report td {
            vertical-align: top;
        }

        .footer {
            margin-top: 9px;
            display: table;
            width: 100%;
        }

        .footer div {
            display: table-cell;
            width: 50%;
        }

        .footer div:last-child {
            text-align: right;
        }

        .total-row td {
            font-weight: 700;
            background: #f9fafb;
        }
    </style>
</head>

<body>
    <header class="brand">
        <div class="brand-logo"> <img class="logo"
                src="data:image/webp;base64,{{ base64_encode(file_get_contents(public_path('assets/logo-inovindo.webp'))) }}"
                alt="Logo PT. Inovindo Digital Media"></div>
        <div class="brand-info">
            <h1>PT. INOVINDO DIGITAL MEDIA</h1>
            <p>Komplek Buana Citra Ciwastra No. D3, Kabupaten Bandung</p>
            <p>WhatsApp: 0856-2251-196 &nbsp;|&nbsp; Website: www.inovindo.co.id</p>
        </div>
    </header>

    <div class="title">
        <h2>REKAP PAYROLL</h2>
        <p>{{ $period->name }} &nbsp;•&nbsp; {{ $period->start_date->format('d M Y') }} -
            {{ $period->end_date->format('d M Y') }}</p>
    </div>

    <table class="meta">
        <tr>
            <td>Jumlah Karyawan: <strong>{{ number_format($employeeCount) }}</strong></td>
            <td>Status Periode: <strong>{{ strtoupper($period->status) }}</strong></td>
        </tr>
    </table>

    <table class="report">
        <thead>
            <tr>
                <th style="width:4%">No</th>
                <th style="width:22%">Karyawan</th>
                <th style="width:11%">Kode</th>
                <th style="width:15%">Jabatan</th>
                <th class="num" style="width:8%">Hari Dibayar</th>
                <th class="num" style="width:13%">Pendapatan</th>
                <th class="num" style="width:13%">Potongan</th>
                <th class="num" style="width:14%">Gaji Bersih</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['employee']?->user?->name ?? 'Karyawan tidak ditemukan' }}</td>
                    <td>{{ $row['employee']?->employee_code ?? '-' }}</td>
                    <td>{{ $row['payroll']->position_name ?? '-' }}</td>
                    <td class="num">{{ number_format((int) $row['payroll']->paid_days) }}</td>
                    <td class="num">Rp{{ number_format((float) $row['payroll']->gross_amount, 0, ',', '.') }}</td>
                    <td class="num">Rp{{ number_format((float) $row['payroll']->deduction_amount, 0, ',', '.') }}
                    </td>
                    <td class="num">Rp{{ number_format((float) $row['payroll']->net_amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5">TOTAL</td>
                <td class="num">Rp{{ number_format((float) $grossTotal, 0, ',', '.') }}</td>
                <td class="num">Rp{{ number_format((float) $deductionTotal, 0, ',', '.') }}</td>
                <td class="num">Rp{{ number_format((float) $netTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div>Dokumen internal payroll perusahaan.</div>
        <div>Dibuat {{ now()->format('d M Y H:i') }}</div>
    </div>
</body>

</html>
