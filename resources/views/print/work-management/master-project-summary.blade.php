<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Master Project - {{ $masterProject->name }}</title>

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
            font-size: 9.8pt;
            line-height: 1.48;
        }

        p {
            margin: 0 0 6px;
        }

        .letterhead,
        .section,
        .summary-box,
        .division-table,
        .signature-section {
            page-break-inside: avoid;
        }

        .letterhead-table,
        .identity-table,
        .summary-table,
        .division-table,
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

        .division-table {
            margin-top: 8px;
            table-layout: fixed;
        }

        .division-table th,
        .division-table td {
            border: 1px solid #1f2937;
            padding: 6px 6px;
            vertical-align: top;
        }

        .division-table th {
            background: #f3f4f6;
            text-align: left;
            font-weight: 700;
        }

        .division-table .number {
            width: 31px;
            text-align: center;
        }

        .division-table .count {
            width: 66px;
            text-align: center;
        }

        .division-table .status {
            width: 85px;
            text-align: center;
        }

        .completion-box {
            padding: 11px 13px;
            border: 1px solid #1f2937;
            text-align: justify;
        }

        .status-box {
            margin-top: 9px;
            padding: 9px 10px;
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
            margin-top: 34px;
        }

        .signature-table {
            table-layout: fixed;
        }

        .signature-table td {
            width: 50%;
            padding: 0 12px;
            vertical-align: top;
        }

        .signature-table .signature {
            text-align: center;
        }

        .signature-title {
            margin-top: 4px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .signature-space {
            height: 78px;
        }

        .signature-name {
            margin-bottom: 2px;
            font-weight: 700;
            text-decoration: underline;
        }

        .signature-role,
        .muted {
            font-size: 8.8pt;
            color: #4b5563;
        }

        .footer-note {
            margin-top: 18px;
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
        $divisionProjects = $masterProject->divisionProjects;

        $totalDivisions = $divisionProjects->count();
        $totalTeams = $divisionProjects->sum(fn ($project) => $project->teams->count());
        $totalMembers = $divisionProjects->sum(
            fn ($project) => $project->teams->sum(
                fn ($team) => $team->employees->where('status_employee', 'active')->count()
            )
        );
        $activeTasks = $divisionProjects->flatMap(
            fn ($project) => $project->tasks->reject(fn ($task) => $task->status === 'cancelled')
        );
        $totalTasks = $activeTasks->count();
        $completedTasks = $activeTasks->where('status', 'done')->count();

        $documentNumber = 'RMP/WM/' . $completionDate->format('Y') . '/' . str_pad((string) $masterProject->id, 4, '0', STR_PAD_LEFT);
    @endphp

    <section class="document-heading">
        <h1>Rekapitulasi Master Project</h1>
        <p class="document-number">
            Nomor Dokumen:
            <strong>{{ $documentNumber }}</strong>
        </p>
    </section>

    <section class="section">
        <p class="section-title">A. Informasi Master Project</p>

        <table class="identity-table">
            <tr>
                <td class="label">Nama Master Project</td>
                <td class="colon">:</td>
                <td><strong>{{ $masterProject->name }}</strong></td>
            </tr>
            <tr>
                <td class="label">Deskripsi</td>
                <td class="colon">:</td>
                <td>{{ $masterProject->description ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">Periode</td>
                <td class="colon">:</td>
                <td>
                    {{ $masterProject->start_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}
                    &nbsp;s/d&nbsp;
                    {{ $masterProject->due_date?->locale('id')->translatedFormat('d F Y') ?? '-' }}
                </td>
            </tr>
            <tr>
                <td class="label">Dibuat Oleh</td>
                <td class="colon">:</td>
                <td>{{ $masterProject->creator?->user?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Finalisasi</td>
                <td class="colon">:</td>
                <td>{{ $completionDate?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }}</td>
            </tr>
        </table>
    </section>

    <section class="section">
        <p class="section-title">B. Rekapitulasi Pelaksanaan</p>

        <div class="summary-box">
            <table class="summary-table">
                <tr>
                    <td>
                        <span class="summary-value">{{ $totalDivisions }}</span>
                        <span class="summary-label">Division</span>
                    </td>
                    <td>
                        <span class="summary-value">{{ $totalTeams }}</span>
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
                </tr>
            </table>
        </div>
    </section>

    <section class="section">
        <p class="section-title">C. Rekapitulasi Division Project</p>

        <table class="division-table">
            <thead>
                <tr>
                    <th class="number">No.</th>
                    <th>Division Project</th>
                    <th>Divisi</th>
                    <th>Manager</th>
                    <th class="count">Team</th>
                    <th class="count">Task</th>
                    <th class="status">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($divisionProjects as $index => $project)
                    @php
                        $projectTasks = $project->tasks->reject(fn ($task) => $task->status === 'cancelled');
                        $projectDone = $projectTasks->where('status', 'done')->count();
                    @endphp
                    <tr>
                        <td class="number">{{ $index + 1 }}</td>
                        <td><strong>{{ $project->name }}</strong></td>
                        <td>{{ $project->division?->name ?? '-' }}</td>
                        <td>{{ $project->manager?->user?->name ?? '-' }}</td>
                        <td class="count">{{ $project->teams->count() }}</td>
                        <td class="count">{{ $projectDone }} / {{ $projectTasks->count() }}</td>
                        <td class="status">Complete</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="section">
        <p class="section-title">D. Hasil Finalisasi</p>

        <div class="completion-box">
            Master Project telah menyelesaikan seluruh Division Project yang
            ditetapkan. Seluruh Division Project telah berstatus complete dan
            Master Project telah ditandai selesai oleh General Manager melalui
            Work Management System.
        </div>

        <div class="status-box">
            <div class="status-label">Status Master Project</div>
            <div class="status-value">COMPLETED</div>
            <div style="margin-top:4px;">
                Finalisasi:
                <strong>{{ $completionDate?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }}</strong>
            </div>
        </div>
    </section>

    <section class="section">
        <p class="section-title">E. Pengesahan</p>
        <p>
            Rekapitulasi ini diterbitkan sebagai dokumentasi resmi hasil akhir
            Master Project dan pengesahan penyelesaiannya.
        </p>
    </section>

    <section class="signature-section">
        <table class="signature-table">
            <tr>
                <td></td>
                <td class="signature">
                    <p>Bandung, {{ $completionDate?->locale('id')->translatedFormat('d F Y') ?? '-' }}</p>
                    <p class="signature-title">General Manager</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ $masterProject->approver?->user?->name ?? '-' }}</p>
                    <p class="signature-role">Pemberi Persetujuan Finalisasi</p>
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
