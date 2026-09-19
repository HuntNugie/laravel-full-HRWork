<x-wirekit::modal name="global-payroll-item-{{ $period->id }}-{{ $item?->id ?? 'create' }}" size="md">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>


    <x-wirekit::modal.header>

        {{ $editing ? 'Edit Komponen Payroll Global' : 'Tambah Komponen Payroll Global' }}

    </x-wirekit::modal.header>


    <x-wirekit::form wire:submit="save">

        <x-wirekit::modal.body>

            <x-wirekit::stack gap="md">

                <div class="rounded-xl border border-sky-100 bg-sky-50 p-4">

                    <p class="text-sm font-semibold text-sky-900">
                        Berlaku untuk seluruh payroll
                    </p>

                    <p class="mt-1 text-sm leading-6 text-sky-700">
                        Komponen ini akan diterapkan dengan nominal yang sama
                        kepada seluruh karyawan yang memiliki payroll pada
                        periode ini.
                    </p>

                </div>


                <x-wirekit::select label="Tipe" wire:model="type">
                    <option value="earning">
                        Penghasilan
                    </option>

                    <option value="deduction">
                        Potongan
                    </option>
                </x-wirekit::select>


                <x-wirekit::input label="Nama Komponen" wire:model="name" placeholder="Contoh: Bonus Kehadiran" />


                <x-wirekit::input type="number" min="0.01" step="0.01" label="Nominal per Karyawan"
                    wire:model="amount" placeholder="Contoh: 150000" />


                <x-wirekit::textarea label="Keterangan" wire:model="description" rows="4"
                    placeholder="Keterangan komponen payroll..." />

            </x-wirekit::stack>

        </x-wirekit::modal.body>


        <x-wirekit::modal.footer>

            <x-wirekit::modal.close>

                <x-wirekit::button type="button" variant="outline">
                    Batal
                </x-wirekit::button>

            </x-wirekit::modal.close>


            @if ($editing)
                <x-wirekit::button type="button" variant="outline" intent="danger" wire:click="delete"
                    wire:confirm="Hapus komponen global ini dari seluruh payroll karyawan pada periode ini?">
                    <x-wirekit::icon name="trash" />
                    Hapus
                </x-wirekit::button>
            @endif


            <x-wirekit::button type="submit" class="bg-[#30AFFF] text-white hover:bg-sky-500" loading-target="save">
                <x-wirekit::icon name="check" />

                {{ $editing ? 'Simpan Perubahan' : 'Tambah Komponen' }}
            </x-wirekit::button>

        </x-wirekit::modal.footer>

    </x-wirekit::form>

</x-wirekit::modal>
