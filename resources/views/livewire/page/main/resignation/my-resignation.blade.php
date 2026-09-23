<x-wirekit::stack gap="md">

    <x-wirekit::stack gap="sm">
        <span class="text-sm font-medium text-[#30AFFF]">
            Layanan Karyawan
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Pengunduran Diri Saya
        </h1>

        <p class="text-sm text-slate-500">
            Ajukan pengunduran diri dan pantau proses exit Anda.
        </p>
    </x-wirekit::stack>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @error('form')
        <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $message }}
        </div>
    @enderror

    @php($resignation = $this->resignation)

    @if ($resignation)
        <x-wirekit::card>
            <x-wirekit::card.header>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Status Pengunduran Diri</h2>
                        <p class="text-sm text-slate-500">Riwayat pengajuan terakhir Anda.</p>
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
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <p class="text-xs font-medium text-slate-400">Diajukan</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $resignation->submitted_at?->translatedFormat('d M Y H:i') ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-slate-400">Tanggal yang diajukan</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $resignation->proposed_last_working_date?->translatedFormat('d M Y') ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-slate-400">Tanggal terakhir bekerja</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $resignation->approved_last_working_date?->translatedFormat('d M Y') ?? 'Menunggu review' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-slate-400">Alasan</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $resignation->reason }}
                        </p>
                    </div>
                </div>

                @if ($resignation->status === 'approved')
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <x-wirekit::card>
                            <x-wirekit::card.body>
                                <p class="text-sm font-semibold text-slate-800">Progress Exit</p>

                                <div class="mt-4 space-y-2">
                                    @foreach ($resignation->clearances as $clearance)
                                        <div class="flex items-center justify-between gap-4 text-sm">
                                            <span class="capitalize text-slate-600">{{ $clearance->category }}</span>
                                            <x-wirekit::badge
                                                :intent="in_array($clearance->status, ['completed', 'not_applicable'], true) ? 'success' : 'warning'">
                                                {{ $clearance->status === 'not_applicable' ? 'N/A' : ucfirst($clearance->status) }}
                                            </x-wirekit::badge>
                                        </div>
                                    @endforeach

                                    <div class="flex items-center justify-between gap-4 border-t border-slate-100 pt-3 text-sm">
                                        <span class="text-slate-600">Final Payroll</span>
                                        <x-wirekit::badge :intent="$resignation->finalPayrolls->contains(fn($payroll) => $payroll->status === 'paid') ? 'success' : 'warning'">
                                            {{ $resignation->finalPayrolls->contains(fn($payroll) => $payroll->status === 'paid') ? 'Paid' : 'Pending' }}
                                        </x-wirekit::badge>
                                    </div>
                                </div>
                            </x-wirekit::card.body>
                        </x-wirekit::card>

                        <x-wirekit::card>
                            <x-wirekit::card.body>
                                <p class="text-sm font-semibold text-slate-800">Handover</p>

                                @forelse ($resignation->handoverItems as $item)
                                    <div class="border-b border-slate-100 py-3 last:border-b-0">
                                        <p class="text-sm font-medium text-slate-800">{{ $item->title }}</p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $item->status === 'not_applicable' ? 'Tidak berlaku' : ucfirst(str_replace('_', ' ', $item->status)) }}
                                        </p>
                                    </div>
                                @empty
                                    <p class="py-4 text-sm text-slate-500">Tidak ada pekerjaan aktif yang perlu dihandover.</p>
                                @endforelse
                            </x-wirekit::card.body>
                        </x-wirekit::card>
                    </div>

                    <div class="mt-6 rounded-xl border border-sky-100 bg-sky-50 px-4 py-3 text-sm text-sky-700">
                        Selama proses notice period, akun dan aktivitas kerja Anda tetap berjalan sampai tanggal terakhir bekerja.
                    </div>
                @endif

                @if (in_array($resignation->status, ['submitted', 'approved'], true))
                    <div class="mt-6 flex justify-end">
                        <x-wirekit::button
                            type="button"
                            surface="outline"
                            intent="danger"
                            wire:click="cancel"
                            wire:confirm="Batalkan pengajuan resign ini?"
                        >
                            Batalkan Pengajuan
                        </x-wirekit::button>
                    </div>
                @endif
            </x-wirekit::card.body>
        </x-wirekit::card>
    @if (!$resignation || in_array($resignation->status, ['rejected', 'cancelled'], true))
        <div class="mt-6">
        <x-wirekit::card>
            <x-wirekit::card.header>
                <h2 class="text-lg font-semibold text-slate-900">Ajukan Pengunduran Diri</h2>
                <p class="text-sm text-slate-500">Isi rencana tanggal terakhir bekerja dan alasan pengunduran diri.</p>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <div class="grid gap-5 md:grid-cols-2">
                    <x-wirekit::input
                        type="date"
                        label="Rencana Tanggal Terakhir Bekerja"
                        name="proposedLastWorkingDate"
                        wire:model="proposedLastWorkingDate"
                    />

                    <div class="md:col-span-2">
                        <x-wirekit::textarea
                            label="Alasan Pengunduran Diri"
                            name="reason"
                            wire:model="reason"
                            rows="5"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <x-wirekit::textarea
                            label="Catatan Tambahan"
                            name="notes"
                            wire:model="notes"
                            rows="4"
                        />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-wirekit::button
                        type="button"
                        class="bg-[#30AFFF] text-white hover:bg-sky-500"
                        wire:click="submit"
                    >
                        Ajukan Pengunduran Diri
                    </x-wirekit::button>
                </div>
            </x-wirekit::card.body>
        </x-wirekit::card>
        </div>
    @endif
</x-wirekit::stack>
