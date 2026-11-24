<x-wirekit::stack gap="md">
    <div>
        <span class="text-sm font-medium text-[#30AFFF]">Overview</span>
        <h1 class="text-2xl font-bold text-slate-900">Dashboard {{ $admin ? 'Administrator' : 'HRD' }}</h1>
        <p class="text-sm text-slate-500">Ringkasan administrasi HRWork.</p>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($metrics as $label => $count)
            <x-wirekit::card><x-wirekit::card.body>
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $count }}</p>
            </x-wirekit::card.body></x-wirekit::card>
        @endforeach
    </div>
    @if (!$admin && auth()->user()->can('view-contract'))
        <x-wirekit::card><x-wirekit::card.body>
            <h2 class="mb-4 text-lg font-semibold text-slate-900">Kontrak berakhir dalam 30 hari</h2>
            @forelse ($expiring as $contract)
                <div class="flex flex-wrap justify-between gap-2 border-b border-slate-200 py-3 text-sm">
                    <span>{{ $contract->employees->name }} — {{ $contract->contract_number }}</span>
                    <span>{{ $contract->end_date->format('d M Y') }}
                        @can('show-contract')
                            <a class="ml-2 text-sky-600" wire:navigate href="{{ route('contract.show', [$contract->employee_id, $contract->id]) }}">Detail</a>
                        @endcan
                    </span>
                </div>
            @empty
                <p class="text-sm text-slate-500">Tidak ada kontrak yang mendekati tanggal berakhir.</p>
            @endforelse
        </x-wirekit::card.body></x-wirekit::card>
    @endif
    @if (!$metrics)
        <p class="text-sm text-slate-500">Hubungi Administrator untuk memperoleh hak akses.</p>
    @endif
</x-wirekit::stack>
