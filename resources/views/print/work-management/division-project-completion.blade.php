<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Berita Acara Penyelesaian Proyek - {{ $divisionProject->name }}</title>

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
            color: #111827;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
        }

        p {
            margin: 0 0 7px;
        }

        .letterhead,
        .section,
        .summary-box,
        .teams,
        .signature-section {
            page-break-inside: avoid;
        }

        .letterhead-table,
        .identity-table,
        .summary-table,
        .teams-table,
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
            border-top: 2px solid #111827;
            border-bottom: 1px solid #111827;
        }

        .document-heading {
            text-align: center;
            margin: 20px 0 18px;
        }

        .document-heading h1 {
            margin: 0;
            font-size: 15pt;
            line-height: 1.2;
            font-weight: 700;
            text-transform: uppercase;
        }

        .document-number {
            margin-top: 7px;
            font-size: 10pt;
        }

        .section {
            margin-top: 17px;
        }

        .section-title {
            margin-bottom: 7px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .identity-table td {
            padding: 2.5px 0;
            vertical-align: top;
        }

        .identity-table .label {
            width: 145px;
        }

        .identity-table .colon {
            width: 16px;
            text-align: center;
        }

        .summary-box {
            border: 1px solid #1f2937;
            padding: 0;
        }

        .summary-table {
            table-layout: fixed;
        }

        .summary-table td {
            width: 20%;
            border-right: 1px solid #1f2937;
            padding: 9px 7px;
            text-align: center;
            vertical-align: middle;
        }

        .summary-table td:last-child {
            border-right: 0;
        }

        .summary-value {
            display: block;
            font-size: 14pt;
            line-height: 1.15;
            font-weight: 700;
        }

        .summary-label {
            display: block;
            margin-top: 4px;
            font-size: 8pt;
            color: #4b5563;
        }

        .teams {
            margin-top: 8px;
        }

        .teams-table {
            table-layout: fixed;
        }

        .teams-table th,
        .teams-table td {
            border: 1px solid #1f2937;
            padding: 6px 7px;
            vertical-align: top;
        }

        .teams-table th {
            background: #f3f4f6;
            font-weight: 700;
            text-align: left;
        }

        .teams-table .number {
            width: 34px;
            text-align: center;
        }

        .teams-table .task-count,
        .teams-table .member-count {
            width: 78px;
            text-align: center;
        }

        .completion-box {
            padding: 11px 13px;
            border: 1px solid #1f2937;
            text-align: justify;
        }

        .status {
            margin: 10px 0 0;
            padding: 8px 10px;
            border: 1px solid #1f2937;
            text-align: center;
        }

        .status-label {
            font-size: 8pt;
            text-transform: uppercase;
            color: #4b5563;
        }

        .status-value {
            margin-top: 3px;
            font-size: 13pt;
            font-weight: 700;
        }

        .signature-section {
            margin-top: 36px;
        }

        .signature-table {
            table-layout: fixed;
        }

        .signature-table td {
            width: 50%;
            padding: 0 12px;
            vertical-align: top;
            text-align: center;
        }

        .signature-title {
            margin-top: 4px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .signature-space {
            height: 82px;
        }

        .signature-name {
            margin-bottom: 2px;
            font-weight: 700;
            text-decoration: underline;
        }

        .signature-role {
            font-size: 9pt;
            color: #4b5563;
        }

        .footer-note {
            margin-top: 20px;
            padding-top: 7px;
            border-top: 1px solid #d1d5db;
            text-align: center;
            color: #6b7280;
            font-size: 8pt;
        }
    </style>
</head>

<body>
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

    @php
        $activeTasks = $divisionProject->tasks->reject(
            fn ($task) => $task->status === 'cancelled'
        );

        $completedTasks = $activeTasks->where('status', 'done')->count();
        $totalTasks = $activeTasks->count();
        $totalMembers = $divisionProject->teams->sum(
            fn ($team) => $team->employees->where('status_employee', 'active')->count()
        );

        $documentNumber = 'BAP/WM/' . $completionDate->format('Y') . '/' . str_pad((string) $divisionProject->id, 4, '0', STR_PAD_LEFT);
    @endphp

    <section class="document-heading">
        <h1>Berita Acara Penyelesaian Proyek</h1>
        <p class="document-number">
            Nomor Dokumen:
            <strong>{{ $documentNumber }}</strong>
        </p>
    </section>

    <section class="section">
        <p class="section-title">A. Informasi Proyek</p>

        <table class="identity-table">
            <tr>
                <td class="label">Nama Proyek</td>
                <td class="colon">:</td>
                <td><strong>{{ $divisionProject->name }}</strong></td>
            </tr>
            <tr>
                <td class="label">Master Project</td>
                <td class="colon">:</td>
                <td>{{ $divisionProject->masterProject?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Divisi</td>
                <td class="colon">:</td>
                <td>{{ $divisionProject->division?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Manager</td>
                <td class="colon">:</td>
                <td>{{ $divisionProject->manager?->user?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Periode Proyek</td>
                <td class="colon">:</td>
                <td>
                    {{ $divisionProject->start_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}
                    &nbsp;s/d&nbsp;
                    {{ $divisionProject->due_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}
                </td>
            </tr>
        </table>
    </section>

    <section class="section">
        <p class="section-title">B. Rekapitulasi Pelaksanaan</p>

        <div class="summary-box">
            <table class="summary-table">
                <tr>
                    <td>
                        <span class="summary-value">{{ $divisionProject->teams->count() }}</span>
                        <span class="summary-label">Team</span>
                    </td>
                    <td>
                        <span class="summary-value">{{ $totalMembers }}</span>
                        <span class="summary-label">Anggota Aktif</span>
                    </td>
                    <td>
                        <span class="summary-value">{{ $totalTasks }}</span>
                        <span class="summary-label">Total Task</span>
                    </td>
                    <td>
                        <span class="summary-value">{{ $completedTasks }}</span>
                        <span class="summary-label">Task Selesai</span>
                    </td>
                    <td>
                        <span class="summary-value">{{ $totalTasks - $completedTasks }}</span>
                        <span class="summary-label">Task Belum Selesai</span>
                    </td>
                </tr>
            </table>
        </div>
    </section>

    <section class="section">
        <p class="section-title">C. Team yang Terlibat</p>

        <div class="teams">
            <table class="teams-table">
                <thead>
                    <tr>
                        <th class="number">No.</th>
                        <th>Team</th>
                        <th>Supervisor</th>
                        <th class="member-count">Anggota</th>
                        <th class="task-count">Task</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($divisionProject->teams as $index => $team)
                        @php
                            $teamTasks = $activeTasks->where('team_id', $team->id);
                            $teamMembers = $team->employees->where('status_employee', 'active');
                        @endphp
                        <tr>
                            <td class="number">{{ $index + 1 }}</td>
                            <td>{{ $team->name }}</td>
                            <td>{{ $team->supervisor?->user?->name ?? '-' }}</td>
                            <td class="member-count">{{ $teamMembers->count() }}</td>
                            <td class="task-count">{{ $teamTasks->count() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;">Tidak ada Team.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="section">
        <p class="section-title">D. Hasil Penyelesaian</p>

        <div class="completion-box">
            Berdasarkan hasil pelaksanaan pekerjaan dan pemeriksaan terhadap
            Division Project tersebut, seluruh task aktif yang ditetapkan pada
            Work Management System telah diselesaikan dan Division Project
            dinyatakan selesai.
        </div>

        <div class="status">
            <div class="status-label">Status Penyelesaian</div>
            <div class="status-value">DISETUJUI</div>
            <div style="margin-top:4px;">
                Tanggal Persetujuan:
                <strong>{{ $completionDate->locale('id')->translatedFormat('d F Y') }}</strong>
            </div>
        </div>
    </section>

    <section class="section">
        <p class="section-title">E. Pengesahan</p>
        <p>
            Berita Acara ini diterbitkan sebagai dokumentasi resmi atas
            penyelesaian Division Project dan persetujuan General Manager.
        </p>
    </section>

    <section class="signature-section">
        <table class="signature-table">
            <tr>
                <td></td>
                <td>
                    <p>Bandung, {{ $completionDate->locale('id')->translatedFormat('d F Y') }}</p>
                    <p class="signature-title">General Manager</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ $approver?->user?->name ?? '-' }}</p>
                    <p class="signature-role">Pemberi Persetujuan</p>
                </td>
            </tr>
        </table>
    </section>

    <div class="footer-note">
        Dokumen diterbitkan melalui Work Management System &nbsp;•&nbsp;
        {{ $documentNumber }}
    </div>
</body>

</html>
