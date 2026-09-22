<x-wirekit::stack gap="lg">

    <x-wirekit::stack gap="sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#30AFFF]">Dashboard Supervisor</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                    {{ $employee->user->name }}
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Monitoring presensi anggota team yang kamu supervisi.
                </p>
            </div>

            <x-wirekit::badge intent="neutral" leading-icon="calendar">
                {{ $today->format('d/m/Y') }}
            </x-wirekit::badge>
        </div>
    </x-wirekit::stack>

    @if (! $team)
        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm" class="items-center justify-center py-8 text-center">
                    <p class="text-sm font-medium text-slate-700">
                        Kamu belum ditetapkan sebagai supervisor team.
                    </p>
                    <p class="text-sm text-slate-500">
                        Data presensi anggota team akan muncul setelah sebuah team menunjukmu sebagai supervisor.
                    </p>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <x-wirekit::card>
                <x-wirekit::card.body>
                    <x-wirekit::stack gap="sm">
                        <span class="text-sm font-medium text-slate-500">Team</span>
                        <p class="text-xl font-semibold text-slate-900">{{ $team->name }}</p>
                        <p class="text-xs text-slate-400">{{ $team->divisi?->name ?? '—' }}</p>
                    </x-wirekit::stack>
                </x-wirekit::card.body>
            </x-wirekit::card>

            <x-wirekit::card>
                <x-wirekit::card.body>
                    <x-wirekit::stack gap="sm">
                        <span class="text-sm font-medium text-slate-500">Anggota Team</span>
                        <p class="text-3xl font-semibold text-slate-900">{{ $summary['employees'] }}</p>
                        <p class="text-xs text-slate-400">Tidak termasuk supervisor</p>
                    </x-wirekit::stack>
                </x-wirekit::card.body>
            </x-wirekit::card>

            <x-wirekit::card>
                <x-wirekit::card.body>
                    <x-wirekit::stack gap="sm">
                        <span class="text-sm font-medium text-slate-500">Sudah Check In</span>
                        <p class="text-3xl font-semibold text-emerald-600">{{ $summary['checked_in'] }}</p>
                        <p class="text-xs text-slate-400">Per hari ini</p>
                    </x-wirekit::stack>
                </x-wirekit::card.body>
            </x-wirekit::card>
        </div>

        <x-wirekit::card>
            <x-wirekit::card.header>
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Presensi Team Hari Ini</h2>
                    <p class="text-sm text-slate-500">
                        Hanya anggota team {{ $team->name }} yang kamu supervisi.
                    </p>
                </x-wirekit::stack>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <div class="overflow-x-auto">
                    <x-wirekit::table hoverable>
                        <x-wirekit::table.head>
                            <x-wirekit::table.row>
                                <x-wirekit::table.th>Employee</x-wirekit::table.th>
                                <x-wirekit::table.th>Position</x-wirekit::table.th>
                                <x-wirekit::table.th>Status</x-wirekit::table.th>
                                <x-wirekit::table.th>Check In</x-wirekit::table.th>
                                <x-wirekit::table.th>Check Out</x-wirekit::table.th>
                            </x-wirekit::table.row>
                        </x-wirekit::table.head>

                        <x-wirekit::table.body>
                            @forelse ($attendanceRows as $row)
                                <x-wirekit::table.row>
                                    <x-wirekit::table.td>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $row['employee']->user?->name ?? '-' }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                {{ $row['employee']->employee_code }}
                                            </p>
                                        </div>
                                    </x-wirekit::table.td>
                                    <x-wirekit::table.td>
                                        {{ $row['employee']->position?->name ?? '-' }}
                                    </x-wirekit::table.td>
                                    <x-wirekit::table.td>
                                        <x-wirekit::badge
                                            :intent="$row['status'] === 'late' ? 'warning' : ($row['attendance'] ? 'success' : 'neutral')"
                                            :dot="true"
                                        >
                                            {{ $row['status_label'] }}
                                        </x-wirekit::badge>
                                    </x-wirekit::table.td>
                                    <x-wirekit::table.td>
                                        {{ $row['attendance']?->check_in_at?->format('H:i') ?? '—' }}
                                    </x-wirekit::table.td>
                                    <x-wirekit::table.td>
                                        {{ $row['attendance']?->check_out_at?->format('H:i') ?? '—' }}
                                    </x-wirekit::table.td>
                                </x-wirekit::table.row>
                            @empty
                                <x-wirekit::table.row>
                                    <x-wirekit::table.td colspan="5">
                                        <div class="py-8 text-center text-sm text-slate-500">
                                            Belum ada anggota team untuk ditampilkan.
                                        </div>
                                    </x-wirekit::table.td>
                                </x-wirekit::table.row>
                            @endforelse
                        </x-wirekit::table.body>
                    </x-wirekit::table>
                </div>
            </x-wirekit::card.body>
        </x-wirekit::card>
    @endif

</x-wirekit::stack>
