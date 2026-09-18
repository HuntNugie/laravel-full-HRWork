<x-wirekit::modal name="create-payroll-period">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>


    <x-wirekit::modal.header>

        <x-wirekit::stack gap="xs">

            <h2 class="text-lg font-semibold text-slate-900">
                Buat Periode Penggajian
            </h2>

            <p class="text-sm text-slate-500">
                Buat periode penggajian sebelum payroll karyawan diproses.
            </p>

        </x-wirekit::stack>

    </x-wirekit::modal.header>


    <x-wirekit::form wire:submit="submit">

        <x-wirekit::modal.body>

            <x-wirekit::stack gap="md">

                {{-- NAMA --}}
                <x-wirekit::input label="Nama Periode" placeholder="Contoh: Payroll September 2026" wire:model="name" />


                {{-- TANGGAL --}}
                <div class="grid gap-4 sm:grid-cols-2">

                    <x-wirekit::input type="date" label="Tanggal Mulai" wire:model="startDate" />

                    <x-wirekit::input type="date" label="Tanggal Selesai" wire:model="endDate" />

                </div>


                {{-- PAYMENT DATE --}}
                <x-wirekit::input type="date" label="Tanggal Pembayaran" wire:model="paymentDate" />


                {{-- INFO --}}
                <div class="rounded-xl border border-sky-100 bg-sky-50/70 p-4">

                    <div class="flex items-start gap-3">

                        <x-wirekit::icon name="information-circle" class="mt-0.5 size-5 shrink-0 text-sky-500" />

                        <p class="text-sm leading-5 text-sky-700">
                            Setelah periode dibuat, status awalnya adalah
                            <strong>Draft</strong>. Payroll belum dibuat atau
                            diproses pada tahap ini.
                        </p>

                    </div>

                </div>

            </x-wirekit::stack>

        </x-wirekit::modal.body>


        <x-wirekit::modal.footer>

            <div class="flex justify-end gap-2">

                <x-wirekit::modal.close>

                    <x-wirekit::button type="button" variant="ghost" size="sm">
                        Batal
                    </x-wirekit::button>

                </x-wirekit::modal.close>


                <x-wirekit::button type="submit" size="sm" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        Simpan
                    </span>

                    <span wire:loading>
                        Menyimpan...
                    </span>
                </x-wirekit::button>

            </div>

        </x-wirekit::modal.footer>

    </x-wirekit::form>

</x-wirekit::modal>
