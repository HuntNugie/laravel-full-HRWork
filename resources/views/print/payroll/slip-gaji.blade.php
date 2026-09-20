<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $employee->user?->name ?? 'Karyawan' }}</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 14mm 16mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            color: #111827;
            font-size: 9pt;
            line-height: 1.45;
        }

        .page {
            width: 100%;
        }

        .brand {
            display: table;
            width: 100%;
            table-layout: fixed;
            padding-bottom: 9px;
            border-bottom: 1.5px solid #111827;
        }

        .brand-logo {
            display: table-cell;
            width: 70px;
            vertical-align: middle;
        }

        .brand-logo img {
            display: block;
            width: 52px;
            max-height: 54px;
        }

        .brand-info {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding-right: 70px;
        }

        .brand-info h1 {
            margin: 0 0 3px;
            font-size: 15pt;
            line-height: 1.15;
        }

        .brand-info p {
            margin: 1px 0;
            font-size: 8pt;
            color: #374151;
        }

        .title {
            text-align: center;
            margin: 13px 0 10px;
        }

        .title h2 {
            margin: 0;
            font-size: 14pt;
            letter-spacing: .4px;
        }

        .title p {
            margin: 3px 0 0;
            font-size: 8.5pt;
            color: #4b5563;
        }

        .section {
            margin-top: 10px;
        }

        .section-title {
            padding-bottom: 5px;
            border-bottom: 1px solid #d1d5db;
            font-weight: 700;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: .35px;
        }

        .info-table,
        .items-table,
        .total-table,
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .info-label {
            width: 23%;
            color: #6b7280;
        }

        .info-value {
            font-weight: 600;
        }

        .items-table {
            margin-top: 6px;
        }

        .items-table th,
        .items-table td {
            padding: 5px 6px;
            border-bottom: 1px solid #e5e7eb;
        }

        .items-table th {
            font-size: 7.5pt;
            color: #6b7280;
            text-transform: uppercase;
            text-align: left;
        }

        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }

        .muted {
            color: #6b7280;
        }

        .empty {
            color: #9ca3af;
            font-style: italic;
        }

        .attendance-table {
            margin-top: 6px;
            width: 100%;
            border-collapse: collapse;
        }

        .attendance-table td {
            padding: 3.5px 0;
        }

        .attendance-table td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .totals {
            margin-top: 11px;
            border-top: 1.5px solid #111827;
            border-bottom: 1px solid #d1d5db;
        }

        .totals td {
            padding: 7px 0;
        }

        .grand-total td {
            padding: 10px 0;
            font-weight: 700;
            font-size: 12pt;
        }

        .grand-total td:last-child {
            text-align: right;
        }

        .status-box {
            margin-top: 10px;
            padding: 7px 10px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
        }

        .status-box strong {
            font-size: 9pt;
        }

        .notes {
            margin-top: 10px;
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            color: #4b5563;
        }

        .signature {
            margin-top: 20px;
        }

        .signature td {
            vertical-align: bottom;
            text-align: right;
        }

        .signature-line {
            display: inline-block;
            width: 170px;
            margin-top: 36px;
            border-bottom: 1px solid #111827;
        }
    </style>
</head>

<body>
    <div class="page">
        <header class="brand">
            <div class="brand-logo">
                <img class="logo"
                    src="data:image/webp;base64,{{ base64_encode(file_get_contents(public_path('assets/logo-inovindo.webp'))) }}"
                    alt="Logo PT. Inovindo Digital Media">
            </div>
            <div class="brand-info">
                <h1>PT. INOVINDO DIGITAL MEDIA</h1>
                <p>Komplek Buana Citra Ciwastra No. D3, Kabupaten Bandung</p>
                <p>WhatsApp: 0856-2251-196 &nbsp;|&nbsp; Website: www.inovindo.co.id</p>
            </div>
        </header>

        <div class="title">
            <h2>SLIP GAJI</h2>
            <p>{{ $period->name }} &nbsp;•&nbsp; {{ $period->start_date->format('d M Y') }} -
                {{ $period->end_date->format('d M Y') }}</p>
        </div>

        <section class="section">
            <div class="section-title">Data Karyawan</div>
            <table class="info-table">
                <tr>
                    <td class="info-label">Nama</td>
                    <td class="info-value">{{ $employee->user?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Kode Karyawan</td>
                    <td class="info-value">{{ $employee->employee_code ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Jabatan</td>
                    <td class="info-value">{{ $payroll->position_name ?? '-' }}</td>
                </tr>
            </table>
        </section>

        <section class="section">
            <div class="section-title">Ringkasan Kehadiran</div>
            <table class="attendance-table">
                <tr>
                    <td>Hari Kerja</td>
                    <td>{{ number_format($payroll->working_days) }} hari</td>
                </tr>
                <tr>
                    <td>Hadir</td>
                    <td>{{ number_format($payroll->present_days) }} hari</td>
                </tr>
                <tr>
                    <td>Hari Dibayar</td>
                    <td>{{ number_format($payroll->paid_days) }} hari</td>
                </tr>
                <tr>
                    <td>Terlambat</td>
                    <td>{{ number_format($payroll->late_days) }} kali</td>
                </tr>
                <tr>
                    <td>Cuti Dibayar</td>
                    <td>{{ number_format($payroll->paid_leave_days) }} hari</td>
                </tr>
                <tr>
                    <td>Tidak Dibayar</td>
                    <td>{{ number_format($payroll->unpaid_leave_days) }} hari</td>
                </tr>
                <tr>
                    <td>Tidak Hadir</td>
                    <td>{{ number_format($payroll->absent_days) }} hari</td>
                </tr>
            </table>
        </section>

        <section class="section">
            <div class="section-title">Pendapatan</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Komponen</th>
                        <th>Keterangan</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($earnings as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td class="muted">{{ $item->description ?: '-' }}</td>
                            <td>Rp{{ number_format((float) $item->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="empty">Tidak ada komponen pendapatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="section">
            <div class="section-title">Potongan</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Komponen</th>
                        <th>Keterangan</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($deductions as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td class="muted">{{ $item->description ?: '-' }}</td>
                            <td>Rp{{ number_format((float) $item->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="empty">Tidak ada komponen potongan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <table class="total-table totals">
            <tr>
                <td>Total Pendapatan</td>
                <td style="text-align:right">Rp{{ number_format((float) $payroll->gross_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Potongan</td>
                <td style="text-align:right">Rp{{ number_format((float) $payroll->deduction_amount, 0, ',', '.') }}
                </td>
            </tr>
            <tr class="grand-total">
                <td>TOTAL DITERIMA</td>
                <td>Rp{{ number_format((float) $payroll->net_amount, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="status-box">
            Status Pembayaran:
            <strong>{{ $payroll->status === 'paid' ? 'PAID' : 'PROCESSED' }}</strong>
            @if ($payroll->paid_at)
                <span class="muted">&nbsp;•&nbsp; Dibayar {{ $payroll->paid_at->format('d M Y H:i') }}</span>
            @endif
        </div>

        @if ($payroll->notes)
            <div class="notes">
                <strong>Catatan</strong><br>
                {{ $payroll->notes }}
            </div>
        @endif

        <table class="footer-table signature">
            <tr>
                <td>
                    {{ $period->paid_at?->format('d M Y') ?? $period->end_date->format('d M Y') }}<br>
                    HR / Finance
                    <br>
                    <span class="signature-line"></span>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
