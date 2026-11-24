<x-wirekit::stack gap="md">
    <div><span class="text-sm font-medium text-[#30AFFF]">Kepegawaian</span><h1 class="text-2xl font-bold text-slate-900">Manajemen Kontrak</h1><p class="text-sm text-slate-500">Seluruh kontrak kerja dan riwayatnya.</p></div>
    <x-wirekit::card><x-wirekit::card.body>
        <div class="grid gap-3 sm:grid-cols-3">
            <x-wirekit::input placeholder="Cari nama, kode, nomor kontrak" wire:model.live.debounce.300ms="search" />
            <x-wirekit::select label="Status" :options="['draft'=>'Draft','active'=>'Active','expired'=>'Expired','terminated'=>'Terminated']" placeholder="Semua status" wire:model.live="statusFilter" />
            <x-wirekit::select label="Jenis kontrak" :options="['pkwt'=>'PKWT','pkwtt'=>'PKWTT','intership'=>'Magang','freelance'=>'Freelance']" placeholder="Semua jenis" wire:model.live="employmentTypeFilter" />
        </div>
        <x-wirekit::button wire:click="resetFilters" type="button" variant="outline" class="mt-3">Reset filter</x-wirekit::button>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="p-3">Nomor</th><th class="p-3">Karyawan</th><th class="p-3">Jenis</th><th class="p-3">Mulai</th><th class="p-3">Berakhir</th><th class="p-3">Status</th><th class="p-3">Aksi</th></tr></thead>
                <tbody>
                    @forelse ($contracts as $contract)
                        <tr class="border-b border-slate-200"><td class="p-3">{{ $contract->contract_number }}</td><td class="p-3">{{ $contract->employees->name }}</td><td class="p-3">{{ strtoupper($contract->employement_type) }}</td><td class="p-3">{{ $contract->start_date->format('d M Y') }}</td><td class="p-3">{{ $contract->end_date?->format('d M Y') ?? '—' }}</td><td class="p-3">{{ $contract->status }}</td><td class="p-3">@can('show-contract')<a class="text-sky-600" wire:navigate href="{{ route('contract.show', [$contract->employee_id, $contract->id]) }}">Detail</a>@endcan @can('update-contract')@if ($contract->status === 'draft')<a class="ml-2 text-sky-600" wire:navigate href="{{ route('contract.edit', $contract->employee_id) }}">Edit</a>@endif @endcan</td></tr>
                    @empty
                        <tr><td colspan="7" class="p-4 text-slate-500">Tidak ada kontrak.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $contracts->links() }}</div>
    </x-wirekit::card.body></x-wirekit::card>
</x-wirekit::stack>
