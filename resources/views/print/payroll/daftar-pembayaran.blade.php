<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Pembayaran - {{ $period->name }}</title>
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
            font-size: 8.4pt;
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

        .stats {
            display: table;
            width: 100%;
            border-spacing: 7px 0;
            margin: 0 -7px 9px;
            table-layout: fixed;
        }

        .stats .box {
            display: table-cell;
            border: 1px solid #d1d5db;
            padding: 7px 9px;
        }

        .stats .label {
            display: block;
            color: #6b7280;
            font-size: 7.2pt;
            text-transform: uppercase;
        }

        .stats .value {
            display: block;
            margin-top: 2px;
            font-size: 10pt;
            font-weight: 700;
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

        .status-paid {
            font-weight: 700;
        }

        .status-processed {
            font-weight: 700;
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
        <h2>DAFTAR PEMBAYARAN GAJI</h2>
        <p>{{ $period->name }} &nbsp;•&nbsp; {{ $period->start_date->format('d M Y') }} -
            {{ $period->end_date->format('d M Y') }}</p>
    </div>

    <div class="stats">
        <div class="box"><span class="label">Total Karyawan</span><span
                class="value">{{ number_format($employeeCount) }}</span></div>
        <div class="box"><span class="label">Sudah Dibayar</span><span
                class="value">{{ number_format($paidCount) }}</span></div>
        <div class="box"><span class="label">Belum Dibayar</span><span
                class="value">{{ number_format($unpaidCount) }}</span></div>
        <div class="box"><span class="label">Total Gaji Bersih</span><span
                class="value">Rp{{ number_format((float) $netTotal, 0, ',', '.') }}</span></div>
    </div>

    <table class="report">
        <thead>
            <tr>
                <th style="width:4%">No</th>
                <th style="width:22%">Karyawan</th>
                <th style="width:11%">Kode</th>
                <th style="width:16%">Jabatan</th>
                <th class="num" style="width:16%">Gaji Bersih</th>
                <th style="width:13%">Status</th>
                <th style="width:18%">Tanggal Dibayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['employee']?->user?->name ?? 'Karyawan tidak ditemukan' }}</td>
                    <td>{{ $row['employee']?->employee_code ?? '-' }}</td>
                    <td>{{ $row['payroll']->position_name ?? '-' }}</td>
                    <td class="num">Rp{{ number_format((float) $row['payroll']->net_amount, 0, ',', '.') }}</td>
                    <td class="{{ $row['payroll']->status === 'paid' ? 'status-paid' : 'status-processed' }}">
                        {{ $row['payroll']->status === 'paid' ? 'PAID' : strtoupper($row['payroll']->status) }}
                    </td>
                    <td>{{ $row['payroll']->paid_at?->format('d M Y H:i') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4">TOTAL GAJI BERSIH</td>
                <td class="num">Rp{{ number_format((float) $netTotal, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div>Dokumen internal payroll perusahaan.</div>
        <div>Dibuat {{ now()->format('d M Y H:i') }}</div>
    </div>
</body>

</html>
