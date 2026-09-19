<x-wirekit::modal name="edit-warning-letter-{{ $warningLetter->id }}" size="lg">

    <x-slot:trigger>
        <div x-on:click="$wire.open()">
            {{ $slot }}
        </div>
    </x-slot:trigger>

    {{-- HEADER --}}
    <x-wirekit::modal.header>

        <x-wirekit::stack gap="xs">

            <h2 class="text-lg font-semibold text-slate-900">
                Edit Surat Peringatan
            </h2>

            <p class="text-sm text-slate-500">
                Perbarui data Surat Peringatan yang masih berstatus draft.
            </p>

        </x-wirekit::stack>

    </x-wirekit::modal.header>

    {{-- FORM --}}
    <x-wirekit::form wire:submit="save">

        <x-wirekit::modal.body>

            <x-wirekit::stack gap="md">

                {{-- EMPLOYEE --}}
                <x-wirekit::select label="Karyawan" wire:model="employeeId" :options="$this->employeeOptions()"
                    placeholder="Pilih karyawan..." />

                {{-- LEVEL --}}
                <x-wirekit::select label="Level Surat Peringatan" wire:model="warningLevel">
                    <option value="SP1">SP1</option>
                    <option value="SP2">SP2</option>
                    <option value="SP3">SP3</option>
                </x-wirekit::select>

                {{-- NOMOR --}}
                <x-wirekit::input label="Nomor Surat" wire:model="letterNumber" readonly disabled
                    hint="Nomor surat dibuat otomatis oleh sistem." />

                {{-- TANGGAL --}}
                <x-wirekit::input type="date" label="Tanggal Surat" wire:model="issuedDate" />

                {{-- ALASAN --}}
                <x-wirekit::textarea label="Alasan" wire:model="reason" rows="3"
                    placeholder="Masukkan alasan diterbitkannya surat peringatan..." />

                {{-- KETERANGAN --}}
                <x-wirekit::textarea label="Keterangan" wire:model="description" rows="5"
                    placeholder="Jelaskan detail pelanggaran atau kejadian..." />

                {{-- STATUS --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Status
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        Draft
                    </p>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Surat belum diterbitkan secara resmi.
                    </p>

                </div>

            </x-wirekit::stack>

        </x-wirekit::modal.body>

        {{-- FOOTER --}}
        <x-wirekit::modal.footer>

            <x-wirekit::row justify="end" gap="sm">

                <x-wirekit::modal.close>
                    <x-wirekit::button variant="outline" type="button">
                        Batal
                    </x-wirekit::button>
                </x-wirekit::modal.close>

                <x-wirekit::button type="submit" wire:loading.attr="disabled" wire:target="save">

                    <span wire:loading.remove wire:target="save">
                        Simpan Perubahan
                    </span>

                    <span wire:loading wire:target="save">
                        mohon tunggu sebentar
                    </span>

                </x-wirekit::button>

            </x-wirekit::row>

        </x-wirekit::modal.footer>

    </x-wirekit::form>

</x-wirekit::modal>
