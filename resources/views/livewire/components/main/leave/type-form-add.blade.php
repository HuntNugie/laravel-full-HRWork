<x-wirekit::modal name="create-leave-type">

    {{-- =====================================================
        TRIGGER
    ====================================================== --}}
    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>


    {{-- =====================================================
        HEADER
    ====================================================== --}}
    <x-wirekit::modal.header>

        <x-wirekit::stack gap="xs">

            <h2 class="text-lg font-semibold text-slate-900">
                Tambah Jenis Cuti
            </h2>

            <p class="text-sm text-slate-500">
                Tambahkan jenis cuti baru yang dapat digunakan dalam sistem.
            </p>

        </x-wirekit::stack>

    </x-wirekit::modal.header>


    {{-- =====================================================
        BODY
    ====================================================== --}}
    <x-wirekit::form wire:submit='save'>

        <x-wirekit::modal.body>

            <div class="space-y-5">

                {{-- Nama Cuti --}}
                <x-wirekit::input label="Nama Cuti" placeholder="Contoh: Cuti Tahunan" wire:model='nameLeave'
                    name='nameLeave' />


                {{-- Jatah Default --}}


                <x-wirekit::input type="number" label="Jatah Harian Default" placeholder="12" min="0"
                    wire:model='defaultDay' name="defaultDay" />





                {{-- Berlaku Untuk --}}
                <x-wirekit::select label="Berlaku Untuk" wire:model='genderLeave' name="genderLeave">

                    <option value="all">
                        Semua Karyawan
                    </option>

                    <option value="male">
                        Laki-laki
                    </option>

                    <option value="female">
                        Perempuan
                    </option>

                </x-wirekit::select>


                {{-- Deskripsi --}}
                <x-wirekit::textarea label="Deskripsi" placeholder="Jelaskan ketentuan atau penggunaan jenis cuti ini."
                    rows="4" wire:model='descriptionLeave' name="descriptionLeave" />


                {{-- Status --}}
                <x-wirekit::toggle name="status" label="Status cuti" wire:model='status' />

            </div>

        </x-wirekit::modal.body>


        {{-- =====================================================
        FOOTER
    ====================================================== --}}
        <x-wirekit::modal.footer>

            <x-wirekit::row justify="end" gap="sm">

                <x-wirekit::modal.close>

                    <x-wirekit::button type="button" variant="outline">
                        Batal
                    </x-wirekit::button>

                </x-wirekit::modal.close>


                <x-wirekit::button type="submit">
                    <span wire:loading.remove wire:target='save'>
                        Simpan Jenis Cuti
                    </span>
                    <span wire:loading wire:target='save'>
                        <x-wirekit::spinner />
                        Tunggu sebentar
                    </span>
                </x-wirekit::button>

            </x-wirekit::row>

        </x-wirekit::modal.footer>
    </x-wirekit::form>


</x-wirekit::modal>
