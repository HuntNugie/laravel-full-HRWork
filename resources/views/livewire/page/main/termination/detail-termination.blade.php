<x-wirekit::stack gap="md">

    <x-wirekit::stack gap="sm">
        <a href="{{ route('termination.view') }}" wire:navigate
            class="inline-flex w-fit items-center gap-2 text-sm font-medium text-black transition hover:text-[#30AFFF]">
            <span aria-hidden="true">&larr;</span>
            Kembali
        </a>

        <span class="text-sm font-medium text-[#30AFFF]">SDM</span>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Detail PHK</h1>
        <p class="text-sm text-slate-500">Kelola handover, clearance, dan penyelesaian PHK employee.</p>
    </x-wirekit::stack>

    <?php if (session('success')): ?>
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    <?php endif; ?>

    <?php if ($errors->has('action')): ?>
        <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('action') }}
        </div>
    <?php endif; ?>

    <x-wirekit::card>
        <x-wirekit::card.body>
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-[#30AFFF]">Employee</p>
                    <h2 class="mt-1 text-xl font-semibold text-slate-900">
                        {{ $termination->employee?->user?->name ?? '—' }}
                    </h2>
                    <p class="text-sm text-slate-500">
                        {{ $termination->employee?->employee_code ?? '—' }}
                        · {{ $termination->employee?->position?->name ?? '—' }}
                    </p>
                </div>

                <?php
                    $statusIntent = match ($termination->status) {
                        'in_progress' => 'info',
                        'cancelled' => 'secondary',
                        'completed' => 'success',
                        default => 'secondary',
                    };
                ?>

                <x-wirekit::badge :intent="$statusIntent">
                    {{ ucfirst($termination->status) }}
                </x-wirekit::badge>
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

    <div class="grid gap-4 lg:grid-cols-3">
        <x-wirekit::card>
            <x-wirekit::card.body>
                <p class="text-xs font-medium text-slate-400">Tanggal Pengajuan</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">
                    {{ $termination->initiated_at?->translatedFormat('d M Y H:i') ?? '—' }}
                </p>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <p class="text-xs font-medium text-slate-400">Tanggal yang diajukan</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">
                    {{ $termination->effective_date?->translatedFormat('d M Y') ?? '—' }}
                </p>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <p class="text-xs font-medium text-slate-400">Tanggal Efektif PHK</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">
                    {{ $termination->effective_date?->translatedFormat('d M Y') ?? '—' }}
                </p>
            </x-wirekit::card.body>
        </x-wirekit::card>
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Informasi PHK</h2>
                <p class="text-sm text-slate-500">Alasan dan informasi utama yang tercatat pada proses PHK.</p>
            </div>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="grid gap-5 md:grid-cols-3">
                <div>
                    <p class="text-xs font-medium text-slate-400">Jenis Alasan</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $reasonTypes[$termination->reason_type] ?? ucfirst($termination->reason_type) }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium text-slate-400">Contract</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $termination->employeeContract?->contract_number ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium text-slate-400">Dibuat Oleh</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $termination->initiator?->name ?? '—' }}
                    </p>
                </div>
            </div>

            <div class="mt-5 rounded-xl bg-slate-50 px-4 py-4">
                <p class="text-xs font-medium text-slate-400">Detail Alasan</p>
                <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $termination->reason }}</p>
            </div>

            <?php if ($termination->notes): ?>
                <div class="mt-4 rounded-xl bg-slate-50 px-4 py-4">
                    <p class="text-xs font-medium text-slate-400">Catatan Tambahan</p>
                    <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $termination->notes }}</p>
                </div>
            <?php endif; ?>
        </x-wirekit::card.body>
    </x-wirekit::card>

    <?php if ($termination->status === 'in_progress' && auth()->user()->can('cancel-termination')): ?>
        <x-wirekit::card>
            <x-wirekit::card.header>
                <h2 class="text-lg font-semibold text-slate-900">Batalkan Proses</h2>
            </x-wirekit::card.header>
            <x-wirekit::card.body>
                <x-wirekit::textarea
                    label="Alasan Pembatalan"
                    name="cancellationReason"
                    wire:model="cancellationReason"
                    rows="3"
                    placeholder="Jelaskan alasan pembatalan..."
                />

                <div class="mt-4 flex justify-end">
                    <x-wirekit::button
                        type="button"
                        surface="outline"
                        intent="danger"
                        wire:click="cancel"
                    >
                        Batalkan PHK
                    </x-wirekit::button>
                </div>
            </x-wirekit::card.body>
        </x-wirekit::card>
    <?php endif; ?>

    <?php if ($termination->status === 'in_progress'): ?>
        <div class="grid gap-4 lg:grid-cols-2">
            <x-wirekit::card>
                <x-wirekit::card.header>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Clearance</h2>
                        <p class="text-sm text-slate-500">Semua clearance harus selesai sebelum PHK ditutup.</p>
                    </div>
                </x-wirekit::card.header>

                <x-wirekit::card.body>
                    <div class="space-y-3">
                        <?php foreach ($termination->clearances as $clearance): ?>
                            <div class="rounded-xl border border-slate-100 p-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold capitalize text-slate-800">{{ $clearance->category }}</p>
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $clearance->notes ?: 'Belum ada catatan.' }}
                                        </p>
                                    </div>

                                    <?php if ($clearance->status === 'completed'): ?>
                                        <div class="flex flex-col items-start gap-1 sm:items-end">
                                            <x-wirekit::badge intent="success">Selesai</x-wirekit::badge>
                                            <?php if ($clearance->verified_at): ?>
                                                <p class="text-xs text-slate-400">
                                                    {{ $clearance->verifier?->name ?? '—' }} ·
                                                    {{ \Carbon\Carbon::parse($clearance->verified_at)->translatedFormat('d M Y H:i') }}
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    <?php elseif ($clearance->status === 'not_applicable'): ?>
                                        <div class="flex flex-col items-start gap-1 sm:items-end">
                                            <x-wirekit::badge intent="neutral">Tidak Berlaku</x-wirekit::badge>
                                            <?php if ($clearance->verified_at): ?>
                                                <p class="text-xs text-slate-400">
                                                    {{ $clearance->verifier?->name ?? '—' }} ·
                                                    {{ \Carbon\Carbon::parse($clearance->verified_at)->translatedFormat('d M Y H:i') }}
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    <?php elseif (auth()->user()->can('manage-termination-clearance')): ?>
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
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </x-wirekit::card.body>
            </x-wirekit::card>

            <x-wirekit::card>
                <x-wirekit::card.header>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Handover Pekerjaan</h2>
                        <p class="text-sm text-slate-500">Pekerjaan aktif employee diambil dari Work Management.</p>
                    </div>
                </x-wirekit::card.header>

                <x-wirekit::card.body>
                    <?php if ($termination->handoverItems->isNotEmpty()): ?>
                        <div class="space-y-3">
                            <?php foreach ($termination->handoverItems as $item): ?>
                                <div class="rounded-xl border border-slate-100 p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">{{ $item->title }}</p>
                                            <?php if ($item->task): ?>
                                                <p class="mt-1 text-xs text-slate-400">
                                                    Status task: {{ str_replace('_', ' ', $item->task->status) }}
                                                </p>
                                            <?php endif; ?>
                                        </div>

                                        <x-wirekit::badge :intent="$item->status === 'completed' ? 'success' : ($item->status === 'not_applicable' ? 'neutral' : 'warning')">
                                            {{ str_replace('_', ' ', $item->status) }}
                                        </x-wirekit::badge>
                                    </div>

                                    <?php if ($item->status !== 'completed' && $item->status !== 'not_applicable'): ?>
                                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                            <x-wirekit::select
                                                label="Dihandover ke"
                                                name="handoverRecipient-{{ $item->id }}"
                                                placeholder="Pilih employee"
                                                :options="$handoverEmployees"
                                                wire:model="handoverRecipients.{{ $item->id }}"
                                            />

                                            <?php if (auth()->user()->can('manage-termination-clearance')): ?>
                                                <div class="flex flex-wrap items-end gap-2">
                                                    <?php if ($item->status === 'pending'): ?>
                                                        <x-wirekit::button
                                                            type="button"
                                                            size="sm"
                                                            surface="outline"
                                                            wire:click="updateHandover({{ $item->id }}, 'in_progress')"
                                                        >
                                                            Proses
                                                        </x-wirekit::button>
                                                    <?php endif; ?>

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
                                            <?php endif; ?>
                                        </div>
                                    <?php elseif ($item->status === 'completed' || $item->status === 'not_applicable'): ?>
                                        <?php if ($item->handoverTo || $item->verified_at): ?>
                                            <p class="mt-3 text-xs text-slate-400">
                                                {{ $item->handoverTo?->user?->name ?? 'Tidak ada penerima' }}
                                                <?php if ($item->verified_at): ?>
                                                    · {{ \Carbon\Carbon::parse($item->verified_at)->translatedFormat('d M Y H:i') }}
                                                <?php endif; ?>
                                            </p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-slate-500">Tidak ada pekerjaan aktif yang perlu dihandover.</p>
                    <?php endif; ?>
                </x-wirekit::card.body>
            </x-wirekit::card>
        </div>

        <?php $readiness = $this->readiness(); ?>
        <x-wirekit::card>
            <x-wirekit::card.header>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Kesiapan Penyelesaian</h2>
                    <p class="text-sm text-slate-500">Semua indikator harus siap sebelum employee dinyatakan terminated.</p>
                </div>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                    <?php foreach ([
                        'clearance' => 'Clearance',
                        'handover' => 'Handover',
                        'organization' => 'Organization',
                        'leave' => 'Leave',
                        'effective_date' => 'Tanggal Efektif',
                    ] as $key => $label): ?>
                        <div class="rounded-xl border border-slate-100 px-4 py-3">
                            <p class="text-xs text-slate-400">{{ $label }}</p>
                            <p class="mt-1 text-sm font-semibold {{ $readiness[$key] ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $readiness[$key] ? 'Ready' : 'Pending' }}
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (auth()->user()->can('complete-termination')): ?>
                    <div class="mt-5 flex justify-end">
                        <x-wirekit::button
                            type="button"
                            class="bg-[#30AFFF] text-white hover:bg-sky-500"
                            wire:click="complete"
                            :disabled="!$readiness['ready']"
                        >
                            Selesaikan PHK
                        </x-wirekit::button>
                    </div>
                <?php endif; ?>
            </x-wirekit::card.body>
        </x-wirekit::card>
    <?php endif; ?>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <h2 class="text-lg font-semibold text-slate-900">Timeline</h2>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="space-y-4">
                <?php if ($termination->histories->isNotEmpty()): ?>
                    <?php foreach ($termination->histories->sortByDesc('created_at') as $history): ?>
                        <div class="border-l-2 border-sky-100 pl-4">
                            <p class="text-sm font-semibold text-slate-800">{{ ucfirst($history->to_status) }}</p>
                            <p class="mt-1 text-xs text-slate-400">
                                {{ $history->created_at?->translatedFormat('d M Y H:i') }}
                                · {{ $history->actor?->name ?? 'System' }}
                            </p>

                            <?php if ($history->note): ?>
                                <p class="mt-1 whitespace-pre-line text-sm text-slate-600">{{ $history->note }}</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-sm text-slate-500">Belum ada aktivitas.</p>
                <?php endif; ?>
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

</x-wirekit::stack>
