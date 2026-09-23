<x-wirekit::stack gap="md">

    <x-wirekit::stack gap="sm">
        <a href="{{ route('resignation.view') }}" wire:navigate
            class="inline-flex w-fit items-center gap-2 text-sm font-medium text-black transition hover:text-[#30AFFF]">
            <span aria-hidden="true">&larr;</span>
            Kembali
        </a>

        <span class="text-sm font-medium text-[#30AFFF]">SDM</span>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Detail Resignation</h1>
        <p class="text-sm text-slate-500">Kelola approval, handover, clearance, dan penyelesaian exit employee.</p>
    </x-wirekit::stack>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @error('action')
        <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $message }}
        </div>
    @enderror

    <x-wirekit::card>
        <x-wirekit::card.body>
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-[#30AFFF]">Employee</p>
                    <h2 class="mt-1 text-xl font-semibold text-slate-900">
                        {{ $resignation->employee?->user?->name ?? '—' }}
                    </h2>
                    <p class="text-sm text-slate-500">
                        {{ $resignation->employee?->employee_code ?? '—' }}
                        · {{ $resignation->employee?->position?->name ?? $resignation->employeeContract?->position_name ?? '—' }}
                    </p>
                </div>

                <x-wirekit::badge :intent="match($resignation->status) {
                    'submitted' => 'warning',
                    'approved' => 'info',
                    'rejected' => 'danger',
                    'cancelled' => 'secondary',
                    'completed' => 'success',
                    default => 'secondary',
                }">
                    {{ ucfirst($resignation->status) }}
                </x-wirekit::badge>
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

    <div class="grid gap-4 lg:grid-cols-3">
        <x-wirekit::card>
            <x-wirekit::card.body>
                <p class="text-xs font-medium text-slate-400">Tanggal Pengajuan</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">
                    {{ $resignation->submitted_at?->translatedFormat('d M Y H:i') ?? '—' }}
                </p>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <p class="text-xs font-medium text-slate-400">Tanggal yang diajukan</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">
                    {{ $resignation->proposed_last_working_date?->translatedFormat('d M Y') ?? '—' }}
                </p>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <p class="text-xs font-medium text-slate-400">Last Working Day</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">
                    {{ $resignation->approved_last_working_date?->translatedFormat('d M Y') ?? 'Belum ditetapkan' }}
                </p>
            </x-wirekit::card.body>
        </x-wirekit::card>
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <h2 class="text-lg font-semibold text-slate-900">Alasan Pengunduran Diri</h2>
        </x-wirekit::card.header>
        <x-wirekit::card.body>
            <p class="whitespace-pre-line text-sm leading-6 text-slate-700">{{ $resignation->reason }}</p>

            @if ($resignation->notes)
                <div class="mt-5 rounded-xl bg-slate-50 px-4 py-3">
                    <p class="text-xs font-medium text-slate-400">Catatan</p>
                    <p class="mt-1 whitespace-pre-line text-sm text-slate-600">{{ $resignation->notes }}</p>
                </div>
            @endif

            @if ($resignation->rejection_reason)
                <div class="mt-5 rounded-xl bg-red-50 px-4 py-3">
                    <p class="text-xs font-medium text-red-500">Alasan Penolakan</p>
                    <p class="mt-1 whitespace-pre-line text-sm text-red-700">{{ $resignation->rejection_reason }}</p>
                </div>
            @endif
        </x-wirekit::card.body>
    </x-wirekit::card>

    @if ($resignation->status === 'submitted' && auth()->user()->canany(['approve-resignation', 'reject-resignation']))
        <x-wirekit::card>
            <x-wirekit::card.header>
                <h2 class="text-lg font-semibold text-slate-900">Review Pengajuan</h2>
            </x-wirekit::card.header>
            <x-wirekit::card.body>
                <div class="grid gap-5 md:grid-cols-2">
                    <x-wirekit::input
                        type="date"
                        label="Tanggal Terakhir Bekerja"
                        name="approvedLastWorkingDate"
                        wire:model="approvedLastWorkingDate"
                    />

                    <x-wirekit::textarea
                        label="Catatan Review"
                        name="actionNote"
                        wire:model="actionNote"
                        rows="4"
                    />

                    <div class="md:col-span-2 flex flex-wrap justify-end gap-2">
                        @can('reject-resignation')
                            <x-wirekit::button
                            type="button"
                            surface="outline"
                            intent="danger"
                            wire:click="reject"
                        >
                            Tolak
                            </x-wirekit::button>
                        @endcan

                        @can('approve-resignation')
                        <x-wirekit::button
                            type="button"
                            class="bg-[#30AFFF] text-white hover:bg-sky-500"
                            wire:click="approve"
                        >
                            Setujui
                        </x-wirekit::button>
                        @endcan
                    </div>
                </div>
            </x-wirekit::card.body>
        </x-wirekit::card>
    @endif

    @if ($resignation->status === 'approved')
        <div class="grid gap-4 lg:grid-cols-2">
            <x-wirekit::card>
                <x-wirekit::card.header>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Clearance</h2>
                        <p class="text-sm text-slate-500">Semua clearance harus selesai sebelum exit ditutup.</p>
                    </div>
                </x-wirekit::card.header>
                <x-wirekit::card.body>
                    <div class="space-y-3">
                        @foreach ($resignation->clearances as $clearance)
                            <div class="rounded-xl border border-slate-100 p-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold capitalize text-slate-800">{{ $clearance->category }}</p>
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $clearance->notes ?: 'Belum ada catatan.' }}
                                        </p>
                                    </div>

                                    @can('manage-resignation-clearance')
                                    <div class="flex flex-wrap gap-2">
                                        <x-wirekit::button
                                            type="button"
                                            size="sm"
                                            surface="outline"
                                            wire:click="updateClearance({{ $clearance->id }}, 'completed')"
                                        >
                                            Selesai
                                        </x-wirekit::button>

                                        <x-wirekit::button
                                            type="button"
                                            size="sm"
                                            surface="outline"
                                            intent="neutral"
                                            wire:click="updateClearance({{ $clearance->id }}, 'not_applicable')"
                                        >
                                            N/A
                                        </x-wirekit::button>
                                    </div>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-wirekit::card.body>
            </x-wirekit::card>

            <x-wirekit::card>
                <x-wirekit::card.header>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Handover Pekerjaan</h2>
                        <p class="text-sm text-slate-500">Pekerjaan aktif employee ditampilkan dari Work Management.</p>
                    </div>
                </x-wirekit::card.header>
                <x-wirekit::card.body>
                    @forelse ($resignation->handoverItems as $item)
                        <div class="rounded-xl border border-slate-100 p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $item->title }}</p>
                                    @if ($item->task)
                                        <p class="mt-1 text-xs text-slate-400">
                                            Status task: {{ str_replace('_', ' ', $item->task->status) }}
                                        </p>
                                    @endif
                                </div>

                                <x-wirekit::badge :intent="$item->status === 'completed' ? 'success' : 'warning'">
                                    {{ str_replace('_', ' ', $item->status) }}
                                </x-wirekit::badge>
                            </div>

                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <x-wirekit::select
                                    label="Dihandover ke"
                                    name="handoverRecipient-{{ $item->id }}"
                                    placeholder="Pilih employee"
                                    :options="$handoverEmployees"
                                    wire:model="handoverRecipients.{{ $item->id }}"
                                />

                                @can('manage-resignation-clearance')
                                <div class="flex flex-wrap items-end gap-2">
                                    <x-wirekit::button
                                        type="button"
                                        size="sm"
                                        surface="outline"
                                        wire:click="updateHandover({{ $item->id }}, 'in_progress')"
                                    >
                                        Proses
                                    </x-wirekit::button>

                                    <x-wirekit::button
                                        type="button"
                                        size="sm"
                                        surface="outline"
                                        wire:click="updateHandover({{ $item->id }}, 'completed')"
                                    >
                                        Selesai
                                    </x-wirekit::button>

                                    <x-wirekit::button
                                        type="button"
                                        size="sm"
                                        surface="outline"
                                        intent="neutral"
                                        wire:click="updateHandover({{ $item->id }}, 'not_applicable')"
                                    >
                                        N/A
                                    </x-wirekit::button>
                                </div>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Tidak ada pekerjaan aktif yang terdeteksi.</p>
                    @endforelse
                </x-wirekit::card.body>
            </x-wirekit::card>
        </div>

        <x-wirekit::card>
            <x-wirekit::card.header>
                <h2 class="text-lg font-semibold text-slate-900">Final Payroll</h2>
                <p class="text-sm text-slate-500">Hubungkan payroll yang sudah dibayar sebagai bukti penyelesaian finansial.</p>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                @can('manage-resignation-clearance')
                <div class="flex flex-col gap-4 md:flex-row md:items-end">
                    <div class="flex-1">
                        <x-wirekit::select
                            label="Payroll Paid"
                            name="selectedPayrollId"
                            wire:model="selectedPayrollId"
                            :options="$payrolls->mapWithKeys(fn($payroll) => [
                                $payroll->id => ($payroll->period?->name ?? 'Payroll') . ' · Rp ' . number_format((float) $payroll->net_amount, 0, ',', '.'),
                            ])->all()"
                        />
                    </div>

                    <x-wirekit::button
                        type="button"
                        class="bg-[#30AFFF] text-white hover:bg-sky-500"
                        wire:click="linkFinalPayroll"
                    >
                        Hubungkan Payroll
                    </x-wirekit::button>
                </div>
                @endcan

                <div class="mt-4 space-y-2">
                    @forelse ($resignation->finalPayrolls as $payroll)
                        <div class="flex items-center justify-between rounded-xl bg-emerald-50 px-4 py-3 text-sm">
                            <span class="text-emerald-700">
                                {{ $payroll->period?->name ?? 'Payroll' }}
                            </span>
                            <span class="font-semibold text-emerald-700">
                                Paid
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada payroll akhir yang dihubungkan.</p>
                    @endforelse
                </div>
            </x-wirekit::card.body>
        </x-wirekit::card>

        @if ($resignation->status === 'approved' || $resignation->status === 'completed')
            <x-wirekit::card>
                <x-wirekit::card.header>
                    <h2 class="text-lg font-semibold text-slate-900">Exit Interview</h2>
                    <p class="text-sm text-slate-500">
                        Catatan percakapan akhir antara HR dan employee.
                    </p>
                </x-wirekit::card.header>

                <x-wirekit::card.body>
                    @if ($resignation->status === 'approved')
                        @can('manage-resignation-clearance')
                            <x-wirekit::textarea
                                label="Catatan Exit Interview"
                                name="exitInterviewNotes"
                                wire:model="exitInterviewNotes"
                                rows="5"
                            />

                            <div class="mt-4 flex justify-end">
                                <x-wirekit::button
                                    type="button"
                                    class="bg-[#30AFFF] text-white hover:bg-sky-500"
                                    wire:click="saveExitInterview"
                                >
                                    Simpan Catatan
                                </x-wirekit::button>
                            </div>
                        @endcan
                    @endif

                    @if ($resignation->exit_interview_notes)
                        <div class="{{ $resignation->status === 'approved' ? 'mt-5' : '' }} rounded-xl bg-slate-50 px-4 py-3">
                            <p class="whitespace-pre-line text-sm leading-6 text-slate-700">
                                {{ $resignation->exit_interview_notes }}
                            </p>

                            @if ($resignation->exitInterviewer)
                                <p class="mt-3 text-xs text-slate-400">
                                    Diisi oleh {{ $resignation->exitInterviewer->name }}
                                    pada {{ $resignation->exit_interview_at?->translatedFormat('d M Y H:i') }}
                                </p>
                            @endif
                        </div>
                    @elseif ($resignation->status === 'completed')
                        <p class="text-sm text-slate-500">
                            Belum ada catatan exit interview.
                        </p>
                    @endif
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endif

        @php($readiness = $this->readiness())

        <x-wirekit::card>
            <x-wirekit::card.header>
                <h2 class="text-lg font-semibold text-slate-900">Kesiapan Penyelesaian</h2>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                    @foreach ([
                        'clearance' => 'Clearance',
                        'handover' => 'Handover',
                        'final_payroll' => 'Final Payroll',
                        'organization' => 'Organization',
                        'leave' => 'Leave',
                        'last_working_date' => 'Last Working Day',
                    ] as $key => $label)
                        <div class="rounded-xl border border-slate-100 px-4 py-3">
                            <p class="text-xs text-slate-400">{{ $label }}</p>
                            <p class="mt-1 text-sm font-semibold {{ $readiness[$key] ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $readiness[$key] ? 'Ready' : 'Pending' }}
                            </p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 flex justify-end">
                    <x-wirekit::button
                        type="button"
                        class="bg-[#30AFFF] text-white hover:bg-sky-500"
                        wire:click="complete"
                        @disabled(!$readiness['ready'])
                    >
                        Selesaikan Resignation
                    </x-wirekit::button>
                </div>
            </x-wirekit::card.body>
        </x-wirekit::card>
    @endif

    <x-wirekit::card>
        <x-wirekit::card.header>
            <h2 class="text-lg font-semibold text-slate-900">Timeline</h2>
        </x-wirekit::card.header>
        <x-wirekit::card.body>
            <div class="space-y-4">
                @forelse ($resignation->histories->sortByDesc('created_at') as $history)
                    <div class="border-l-2 border-sky-100 pl-4">
                        <p class="text-sm font-semibold text-slate-800">
                            {{ ucfirst($history->to_status) }}
                        </p>
                        <p class="mt-1 text-xs text-slate-400">
                            {{ $history->created_at?->translatedFormat('d M Y H:i') }}
                            · {{ $history->actor?->name ?? 'System' }}
                        </p>
                        @if ($history->note)
                            <p class="mt-1 whitespace-pre-line text-sm text-slate-600">{{ $history->note }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada aktivitas.</p>
                @endforelse
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

</x-wirekit::stack>
