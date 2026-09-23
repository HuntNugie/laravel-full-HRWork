<x-wirekit::stack gap="md">

    <x-wirekit::stack gap="sm">
        <a
            href="{{ route('termination.view') }}"
            wire:navigate
            class="inline-flex w-fit items-center gap-2 text-sm font-medium text-black transition hover:text-[#30AFFF]"
        >
            <span aria-hidden="true">&larr;</span>
            Kembali
        </a>

        <span class="text-sm font-medium text-[#30AFFF]">SDM</span>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Buat PHK</h1>
        <p class="text-sm text-slate-500">Buat pengajuan PHK untuk karyawan aktif dan kirim ke General Manager untuk persetujuan.</p>
    </x-wirekit::stack>

    @if ($errors->has('action'))
        <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('action') }}
        </div>
    @endif

    <x-wirekit::card>
        <x-wirekit::card.header>
            <h2 class="text-lg font-semibold text-slate-900">Informasi PHK</h2>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="grid gap-5 md:grid-cols-2">

                <x-wirekit::select
                    name="employeeId"
                    label="Employee"
                    placeholder="Pilih employee"
                    :options="$employees"
                    wire:model="employeeId"
                />

                <x-wirekit::select
                    name="reasonType"
                    label="Jenis Alasan"
                    placeholder="Pilih jenis alasan"
                    :options="$reasonTypes"
                    wire:model="reasonType"
                />

                <x-wirekit::input
                    type="date"
                    name="proposedEffectiveDate"
                    label="Tanggal Efektif PHK"
                    wire:model="proposedEffectiveDate"
                />

                <div class="rounded-xl bg-slate-50 px-4 py-3">
                    <p class="text-xs font-medium text-slate-400">Catatan Proses</p>
                    <p class="mt-1 text-sm leading-6 text-slate-600">
                        Employee tetap berstatus aktif sampai PHK disetujui, seluruh proses exit selesai, dan tanggal efektif tercapai.
                    </p>
                </div>

                <div class="md:col-span-2">
                    <x-wirekit::textarea
                        name="reason"
                        label="Detail Alasan PHK"
                        placeholder="Jelaskan alasan PHK secara lengkap..."
                        rows="6"
                        wire:model="reason"
                    />
                </div>

                <div class="md:col-span-2">
                    <x-wirekit::textarea
                        name="notes"
                        label="Catatan Tambahan"
                        placeholder="Catatan tambahan untuk proses PHK (opsional)..."
                        rows="4"
                        wire:model="notes"
                    />
                </div>

            </div>

            <div class="mt-6 flex justify-end">
                <x-wirekit::button
                    type="button"
                    class="bg-[#30AFFF] text-white hover:bg-sky-500"
                    wire:click="save"
                >
                    Ajukan PHK
                </x-wirekit::button>
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

</x-wirekit::stack>
