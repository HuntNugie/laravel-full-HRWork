<x-wirekit::stack gap="lg">

    @php
        $attentionItems = [
            ['label' => 'User menunggu aktivasi', 'value' => $pendingUsers, 'route' => 'user.view', 'tone' => 'warning'],
            ['label' => 'Pengajuan cuti', 'value' => $pendingLeave, 'route' => 'leave.manage.view', 'tone' => 'info'],
            ['label' => 'Pengajuan sakit / izin', 'value' => $pendingAbsence, 'route' => 'absence.view', 'tone' => 'info'],
            ['label' => 'Report Work Management', 'value' => $pendingProjectReports, 'route' => 'work-management.master-projects', 'tone' => 'primary'],
            ['label' => 'Master Project siap review', 'value' => $masterReadyForReview, 'route' => 'work-management.master-projects', 'tone' => 'success'],
        ];

        $userTotal = max(1, $totalUsers);
        $activeUserPercent = min(100, round(($activeUsers / $userTotal) * 100));
        $pendingUserPercent = min(100, round(($pendingUsers / $userTotal) * 100));
        $inactiveUserPercent = min(100, round(($inactiveUsers / $userTotal) * 100));

        $payrollStatusMeta = match ($latestPayrollSummary['period']?->status) {
            'paid' => ['label' => 'Sudah dibayar', 'class' => 'bg-emerald-50 text-emerald-700'],
            'processed' => ['label' => 'Sudah diproses', 'class' => 'bg-sky-50 text-sky-700'],
            'processing' => ['label' => 'Sedang diproses', 'class' => 'bg-amber-50 text-amber-700'],
            default => [
                'label' => $latestPayrollSummary['period']?->status
                    ? str_replace('_', ' ', $latestPayrollSummary['period']->status)
                    : 'Belum ada periode',
                'class' => 'bg-slate-100 text-slate-600',
            ],
        };
    @endphp

    <div class="relative overflow-hidden rounded-2xl border border-blue-700 bg-blue-700 px-6 py-7 shadow-sm sm:px-8">
        <div class="pointer-events-none absolute -right-20 -top-24 size-64 rounded-full bg-cyan-300/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 left-1/3 size-56 rounded-full bg-blue-300/10 blur-3xl"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <x-wirekit::stack gap="sm">
                <span class="text-sm font-semibold text-cyan-100">System Administration</span>

                <div class="space-y-2">
                    <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        {{ $greeting }}, Super Admin.
                    </h1>
                    <p class="max-w-2xl text-sm leading-6 text-slate-300">
                        Pantau pengguna, organisasi, HR operations, payroll, dan Work Management dalam satu dashboard.
                    </p>
                </div>
            </x-wirekit::stack>

            <div class="rounded-xl border border-white/20 bg-white/10 px-4 py-3">
                <p class="text-xs font-medium uppercase tracking-wide text-blue-100">Hari ini</p>
                <p class="mt-1 text-sm font-semibold text-white">{{ $today->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-500">Active Employees</span>
                        <span class="size-2.5 rounded-full bg-emerald-400"></span>
                    </div>
                    <p class="text-3xl font-bold tracking-tight text-slate-900">{{ $activeEmployees }}</p>
                    <span class="text-xs text-slate-400">{{ $activeUsers }} user aktif</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-500">Attendance Today</span>
                        <span class="size-2.5 rounded-full bg-[#30AFFF]"></span>
                    </div>
                    <p class="text-3xl font-bold tracking-tight text-slate-900">{{ $checkedInToday }}</p>
                    <div class="flex items-center gap-3 text-xs">
                        <span class="font-medium text-emerald-600">{{ $presentToday }} hadir</span>
                        <span class="font-medium text-amber-600">{{ $lateToday }} terlambat</span>
                    </div>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-500">Need Attention</span>
                        <span class="size-2.5 rounded-full bg-amber-400"></span>
                    </div>
                    <p class="text-3xl font-bold tracking-tight text-slate-900">{{ $pendingActions }}</p>
                    <span class="text-xs text-slate-400">Item yang masih membutuhkan tindakan</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <x-wirekit::card class="xl:col-span-2">
            <x-wirekit::card.header>
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Workforce & Access</h2>
                    <p class="text-sm text-slate-500">Ringkasan akun dan struktur organisasi.</p>
                </x-wirekit::stack>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <div class="grid gap-6 md:grid-cols-2">
                    <x-wirekit::stack gap="md">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-700">User Status</p>
                                <p class="text-xs text-slate-400">{{ $totalUsers }} akun terdaftar</p>
                            </div>
                            @can('view-user')
                                <a href="{{ route('user.view') }}" wire:navigate class="text-xs font-semibold text-[#168ED1] hover:underline">
                                    Kelola
                                </a>
                            @endcan
                        </div>

                        <div class="space-y-4">
                            <div>
                                <div class="mb-2 flex items-center justify-between text-xs">
                                    <span class="text-slate-500">Active</span>
                                    <span class="font-semibold text-slate-800">{{ $activeUsers }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-[#30AFFF]" style="width: {{ $activeUserPercent }}%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="mb-2 flex items-center justify-between text-xs">
                                    <span class="text-slate-500">Pending</span>
                                    <span class="font-semibold text-slate-800">{{ $pendingUsers }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-amber-400" style="width: {{ $pendingUserPercent }}%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="mb-2 flex items-center justify-between text-xs">
                                    <span class="text-slate-500">Non-active</span>
                                    <span class="font-semibold text-slate-800">{{ $inactiveUsers }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-slate-400" style="width: {{ $inactiveUserPercent }}%"></div>
                                </div>
                            </div>
                        </div>
                    </x-wirekit::stack>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs text-slate-400">Divisions</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $organization['divisions'] }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs text-slate-400">Teams</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $organization['teams'] }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs text-slate-400">Positions</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $organization['positions'] }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs text-slate-400">Roles</p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $organization['roles'] }}</p>
                        </div>
                    </div>
                </div>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.header>
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Attendance Hari Ini</h2>
                    <p class="text-sm text-slate-500">Ringkasan check-in karyawan aktif.</p>
                </x-wirekit::stack>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <x-wirekit::stack gap="md">
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-3xl font-bold text-slate-900">{{ $checkedInToday }}</p>
                            <p class="mt-1 text-xs text-slate-400">Sudah tercatat</p>
                        </div>
                        @can('view-monitor-attendance')
                            <a href="{{ route('attendance.monitor.view') }}" wire:navigate class="text-xs font-semibold text-[#168ED1] hover:underline">
                                Monitor
                            </a>
                        @endcan
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3">
                            <p class="text-xs text-emerald-700">Hadir</p>
                            <p class="mt-1 text-xl font-bold text-emerald-800">{{ $presentToday }}</p>
                        </div>
                        <div class="rounded-xl border border-amber-100 bg-amber-50 p-3">
                            <p class="text-xs text-amber-700">Terlambat</p>
                            <p class="mt-1 text-xl font-bold text-amber-800">{{ $lateToday }}</p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500">Belum tercatat</span>
                            <span class="text-sm font-bold text-slate-800">{{ $unrecordedToday }}</span>
                        </div>
                        <p class="mt-1 text-[11px] leading-5 text-slate-400">
                            Belum check-in atau memiliki status non-hadir pada hari ini.
                        </p>
                    </div>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <x-wirekit::card>
            <x-wirekit::card.header>
                <div class="flex items-start justify-between gap-4">
                    <x-wirekit::stack gap="1">
                        <h2 class="text-lg font-semibold text-slate-900">Items Need Attention</h2>
                        <p class="text-sm text-slate-500">Antrean yang paling berguna untuk monitoring admin.</p>
                    </x-wirekit::stack>
                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">{{ $pendingActions }}</span>
                </div>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    @foreach ($attentionItems as $item)
                        <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="size-2.5 shrink-0 rounded-full {{ match($item['tone']) {
                                    'warning' => 'bg-amber-400',
                                    'success' => 'bg-emerald-400',
                                    'primary' => 'bg-[#30AFFF]',
                                    default => 'bg-sky-400',
                                } }}"></span>
                                <span class="truncate text-sm font-medium text-slate-700">{{ $item['label'] }}</span>
                            </div>
                            <a href="{{ route($item['route']) }}" wire:navigate class="ml-4 shrink-0 text-sm font-bold text-slate-900 hover:text-[#168ED1]">
                                {{ $item['value'] }}
                            </a>
                        </div>
                    @endforeach
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.header>
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Payroll</h2>
                    <p class="text-sm text-slate-500">Status periode payroll terbaru.</p>
                </x-wirekit::stack>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <x-wirekit::stack gap="md">
                    @if ($latestPayrollSummary['period'])
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Periode terbaru</p>
                                <p class="mt-1 text-lg font-semibold text-slate-900">
                                    {{ $latestPayrollSummary['period']->start_date?->format('d M Y') }}
                                    — {{ $latestPayrollSummary['period']->end_date?->format('d M Y') }}
                                </p>
                            </div>
                            <span class="inline-flex w-fit rounded-full px-2.5 py-1 text-xs font-semibold {{ $payrollStatusMeta['class'] }}">
                                {{ $payrollStatusMeta['label'] }}
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div class="rounded-xl border border-slate-200 p-3">
                                <p class="text-xs text-slate-400">Payroll</p>
                                <p class="mt-1 text-xl font-bold text-slate-900">{{ $latestPayrollSummary['total'] }}</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 p-3">
                                <p class="text-xs text-slate-400">Processed</p>
                                <p class="mt-1 text-xl font-bold text-slate-900">{{ $latestPayrollSummary['processed'] }}</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 p-3">
                                <p class="text-xs text-slate-400">Paid</p>
                                <p class="mt-1 text-xl font-bold text-slate-900">{{ $latestPayrollSummary['paid'] }}</p>
                            </div>
                        </div>

                        @can('view-payroll')
                            <a href="{{ route('payroll.view') }}" wire:navigate>
                                <x-wirekit::button type="button" variant="outline" class="w-full">Buka Payroll</x-wirekit::button>
                            </a>
                        @endcan
                    @else
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center">
                            <p class="text-sm font-medium text-slate-700">Belum ada periode payroll.</p>
                            <p class="mt-1 text-xs text-slate-400">Periode payroll terbaru akan muncul setelah dibuat.</p>
                        </div>
                    @endif
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Work Management Overview</h2>
                    <p class="text-sm text-slate-500">Ringkasan project, task, dan approval yang sedang berjalan.</p>
                </x-wirekit::stack>
                @can('view-master-project')
                    <a href="{{ route('work-management.master-projects') }}" wire:navigate class="text-xs font-semibold text-[#168ED1] hover:underline">
                        Buka Work Management
                    </a>
                @endcan
            </div>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Master Project</p>
                    <div class="mt-3 flex items-end justify-between gap-3">
                        <p class="text-2xl font-bold text-slate-900">{{ $masterProjects['total'] }}</p>
                        <span class="text-xs font-semibold text-emerald-600">{{ $masterProjects['completed'] }} selesai</span>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                        <span class="rounded-lg bg-sky-50 px-2.5 py-2 text-sky-700">{{ $masterProjects['active'] }} aktif</span>
                        <span class="rounded-lg bg-violet-50 px-2.5 py-2 text-violet-700">{{ $masterProjects['ready_for_review'] }} review</span>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Division Project</p>
                    <div class="mt-3 flex items-end justify-between gap-3">
                        <p class="text-2xl font-bold text-slate-900">{{ $divisionProjects['total'] }}</p>
                        <span class="text-xs font-semibold text-emerald-600">{{ $divisionProjects['completed'] }} selesai</span>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                        <span class="rounded-lg bg-sky-50 px-2.5 py-2 text-sky-700">{{ $divisionProjects['active'] }} aktif</span>
                        <span class="rounded-lg bg-amber-50 px-2.5 py-2 text-amber-700">{{ $divisionProjects['submitted_to_gm'] }} ke GM</span>
                        <span class="rounded-lg bg-rose-50 px-2.5 py-2 text-rose-700">{{ $divisionProjects['revision_required'] }} revisi</span>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Task</p>
                    <div class="mt-3 flex items-end justify-between gap-3">
                        <p class="text-2xl font-bold text-slate-900">{{ $tasks['total'] }}</p>
                        <span class="text-xs font-semibold text-emerald-600">{{ $tasks['done'] }} selesai</span>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                        <span class="rounded-lg bg-sky-50 px-2.5 py-2 text-sky-700">{{ $tasks['open'] }} terbuka</span>
                        <span class="rounded-lg bg-violet-50 px-2.5 py-2 text-violet-700">{{ $tasks['in_review'] }} review</span>
                        <span class="rounded-lg bg-rose-50 px-2.5 py-2 text-rose-700">{{ $tasks['blocked'] }} blocked</span>
                    </div>
                </div>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-4">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs text-slate-400">Supervisor Reports</p>
                    <p class="mt-1 text-xl font-bold text-slate-900">{{ $reportQueue['supervisor'] }}</p>
                    <p class="text-[11px] text-slate-400">Menunggu review Manager</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs text-slate-400">Manager Reports</p>
                    <p class="mt-1 text-xl font-bold text-slate-900">{{ $reportQueue['manager'] }}</p>
                    <p class="text-[11px] text-slate-400">Menunggu review GM</p>
                </div>
                <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3">
                    <p class="text-xs text-emerald-700">Approved Reports</p>
                    <p class="mt-1 text-xl font-bold text-emerald-800">{{ $reportQueue['approved'] }}</p>
                    <p class="text-[11px] text-emerald-600">Total report approved</p>
                </div>
                <div class="rounded-xl border border-rose-100 bg-rose-50 p-3">
                    <p class="text-xs text-rose-700">Rejected Reports</p>
                    <p class="mt-1 text-xl font-bold text-rose-800">{{ $reportQueue['rejected'] }}</p>
                    <p class="text-[11px] text-rose-600">Total report rejected</p>
                </div>
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

    <div class="grid gap-6 xl:grid-cols-2">
        <x-wirekit::card>
            <x-wirekit::card.header>
                <div class="flex items-start justify-between gap-4">
                    <x-wirekit::stack gap="1">
                        <h2 class="text-lg font-semibold text-slate-900">Karyawan Terbaru</h2>
                        <p class="text-sm text-slate-500">Enam karyawan terakhir yang dibuat.</p>
                    </x-wirekit::stack>
                    @can('view-employee')
                        <a href="{{ route('employee.view') }}" wire:navigate class="text-xs font-semibold text-[#168ED1] hover:underline">Semua</a>
                    @endcan
                </div>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    @forelse ($latestEmployees as $employee)
                        @php
                            $status = $employee->user?->status;
                            $statusClass = match ($status) {
                                'active' => 'bg-emerald-50 text-emerald-700',
                                'pending' => 'bg-amber-50 text-amber-700',
                                default => 'bg-slate-100 text-slate-600',
                            };
                            $statusLabel = match ($status) {
                                'active' => 'Active',
                                'pending' => 'Pending',
                                'non-active' => 'Non-active',
                                default => 'Unknown',
                            };
                        @endphp

                        <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-4 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ $employee->user?->name ?? 'Tanpa nama' }}</p>
                                <p class="mt-1 truncate text-xs text-slate-400">
                                    {{ $employee->position?->name ?? 'Tanpa position' }} · {{ $employee->created_at?->diffForHumans() }}
                                </p>
                            </div>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center">
                            <p class="text-sm font-medium text-slate-700">Belum ada data karyawan.</p>
                        </div>
                    @endforelse
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.header>
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Recent Work Reports</h2>
                    <p class="text-sm text-slate-500">Laporan terbaru dari Supervisor dan Manager.</p>
                </x-wirekit::stack>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    @forelse ($recentProjectReports as $report)
                        @php
                            $reportStatusClass = match ($report->status) {
                                'approved' => 'bg-emerald-50 text-emerald-700',
                                'rejected' => 'bg-rose-50 text-rose-700',
                                default => 'bg-amber-50 text-amber-700',
                            };
                            $reportLevelLabel = $report->report_level === 'manager' ? 'Manager' : 'Supervisor';
                        @endphp

                        <div class="rounded-xl border border-slate-200 px-4 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-800">{{ $report->divisionProject?->name ?? 'Division Project' }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $reportLevelLabel }} · {{ $report->reporter?->user?->name ?? 'Tidak diketahui' }}</p>
                                </div>
                                <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $reportStatusClass }}">{{ ucfirst($report->status) }}</span>
                            </div>
                            <p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-500">{{ $report->content }}</p>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center">
                            <p class="text-sm font-medium text-slate-700">Belum ada report Work Management.</p>
                        </div>
                    @endforelse
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Quick Actions</h2>
                <p class="text-sm text-slate-500">Shortcut ke area administrasi yang paling sering digunakan.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @can('view-user')
                    <a href="{{ route('user.view') }}" wire:navigate>
                        <x-wirekit::button type="button" variant="outline" class="w-full">Manage Users</x-wirekit::button>
                    </a>
                @endcan

                @can('view-employee')
                    <a href="{{ route('employee.view') }}" wire:navigate>
                        <x-wirekit::button type="button" variant="outline" class="w-full">Employees</x-wirekit::button>
                    </a>
                @endcan

                @can('view-divisi')
                    <a href="{{ route('divisi.view') }}" wire:navigate>
                        <x-wirekit::button type="button" variant="outline" class="w-full">Organization</x-wirekit::button>
                    </a>
                @endcan

                @can('view-master-project')
                    <a href="{{ route('work-management.master-projects') }}" wire:navigate>
                        <x-wirekit::button type="button" class="w-full bg-[#30AFFF] text-white hover:bg-[#1599E8]">Work Management</x-wirekit::button>
                    </a>
                @endcan
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

</x-wirekit::stack>
