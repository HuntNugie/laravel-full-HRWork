<x-wirekit::stack gap="md">

    {{-- =====================================================
    PAGE HEADING
    ====================================================== --}}
    <x-wirekit::stack gap="sm">

        <a href="{{ route('employee.view') }}" wire:navigate
            class="inline-flex w-fit items-center gap-2 text-sm font-medium text-black transition hover:text-[#30AFFF]">
            <span aria-hidden="true">&larr;</span>
            Kembali
        </a>

        <span class="text-sm font-medium text-[#30AFFF]">
            Manajemen Karyawan
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-black">
            Edit Data Karyawan
        </h1>

        <p class="text-sm text-black">
            Perbarui informasi pribadi dan alamat karyawan.
        </p>

    </x-wirekit::stack>


    {{-- =====================================================
    MAIN FORM
    ====================================================== --}}
    <x-wirekit::form wire:submit="update">

        <x-wirekit::stack gap="md">

            {{-- =================================================
            1. PERSONAL INFORMATION
            ================================================== --}}
            <x-wirekit::card>

                <x-wirekit::card.header>

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-black">
                            Informasi Pribadi
                        </h2>

                        <p class="text-sm text-black/60">
                            Informasi dasar dan kontak karyawan.
                        </p>

                    </x-wirekit::stack>

                </x-wirekit::card.header>


                <x-wirekit::card.body>

                    <div class="grid gap-5 md:grid-cols-2">

                        {{-- =================================================
                        Nama Lengkap
                        ================================================== --}}
                        <div class="md:col-span-2">

                            <div>

                                <x-wirekit::input class="text-black" label="Nama Lengkap" name="form.fullname"
                                    wire:model.live.debounce.500ms="form.fullname" maxlength="100"
                                    placeholder="Contoh: Nugie Pratama" />

                            </div>

                        </div>


                        {{-- =================================================
                        NIK
                        ================================================== --}}
                        <div>

                            <div x-data="{ text: $wire.entangle('form.nik').live, max: 16 }">

                                <x-wirekit::input class="text-black" label="NIK" name="form.nik" x-model="text"
                                    maxlength="16" inputmode="numeric" placeholder="Masukkan NIK" />

                                <div
                                    style="display: flex; justify-content: flex-end; align-items: center; margin-top: 0.375rem; font-size: var(--text-wk-sm);">
                                    <span
                                        style="color: var(--color-wk-text-muted); flex-shrink: 0; white-space: nowrap;"
                                        x-text="`${text.length}/${max}`">
                                        0/16
                                    </span>
                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                        Jenis Kelamin
                        ================================================== --}}
                        <x-wirekit::select label="Jenis Kelamin" name="form.gender" wire:model.live="form.gender"
                            placeholder="Pilih jenis kelamin..." :options="[
                                'male' => 'Laki-laki',
                                'female' => 'Perempuan',
                            ]" />


                        {{-- =================================================
                        Nomor Telepon
                        ================================================== --}}
                        <div>

                            <div x-data="{ text: $wire.entangle('form.phone').live, max: 15 }">

                                <x-wirekit::input class="text-black" label="Nomor Telepon" name="form.phone"
                                    x-model="text" maxlength="15" inputmode="numeric" placeholder="08xxxxxxxxxx" />

                                <div
                                    style="display: flex; justify-content: flex-end; align-items: center; margin-top: 0.375rem; font-size: var(--text-wk-sm);">
                                    <span
                                        style="color: var(--color-wk-text-muted); flex-shrink: 0; white-space: nowrap;"
                                        x-text="`${text.length}/${max}`">
                                        0/15
                                    </span>
                                </div>

                            </div>

                        </div>

                    </div>

                </x-wirekit::card.body>

            </x-wirekit::card>


            {{-- =================================================
            2. ADDRESS INFORMATION
            ================================================== --}}
            <x-wirekit::card>

                <x-wirekit::card.header>

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-black">
                            Informasi Alamat
                        </h2>

                        <p class="text-sm text-black/60">
                            Perbarui alamat tempat tinggal karyawan.
                        </p>

                    </x-wirekit::stack>

                </x-wirekit::card.header>


                <x-wirekit::card.body>

                    <div class="grid gap-5 md:grid-cols-2">

                        {{-- =================================================
                        Provinsi
                        ================================================== --}}
                        <x-wirekit::select label="Provinsi" name="province_code" wire:model.live="form.provinceCode"
                            placeholder="Pilih provinsi..." :options="$this->form->provinceOptions()" />


                        {{-- =================================================
                        Kabupaten / Kota
                        ================================================== --}}
                        <x-wirekit::select label="Kabupaten/Kota" name="regency_code" wire:model.live="form.regencyCode"
                            placeholder="Pilih kabupaten/kota..." :options="$this->form->regencyOptions()" />


                        {{-- =================================================
                        Kecamatan
                        ================================================== --}}
                        <x-wirekit::select label="Kecamatan" name="district_code" wire:model.live="form.districtCode"
                            placeholder="Pilih kecamatan..." :options="$this->form->districtOptions()" />


                        {{-- =================================================
                        Kelurahan / Desa
                        ================================================== --}}
                        <x-wirekit::select label="Kelurahan/Desa" name="village_code" wire:model.live="form.villageCode"
                            placeholder="Pilih kelurahan/desa..." :options="$this->form->villageOptions()" />


                        {{-- =================================================
                        Alamat Lengkap
                        ================================================== --}}
                        <div class="md:col-span-2">

                            <div>

                                <x-wirekit::textarea class="text-black" label="Alamat Lengkap" name="form.detailAddress"
                                    wire:model.live.debounce.500ms="form.detailAddress" maxlength="255"
                                    placeholder="Contoh: Jl. Sukajadi No. 10 RT 03/RW 05" />

                            </div>

                        </div>

                    </div>

                </x-wirekit::card.body>

            </x-wirekit::card>


            {{-- =================================================
            FORM ACTION
            ================================================== --}}
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">


                <x-wirekit::button type="submit" class="bg-[#30AFFF] text-white hover:bg-sky-500">
                    <span wire:loading.remove wire:target='update'>
                        Simpan Perubahan
                    </span>
                    <span wire:loading wire:target='update'>
                        <x-wirekit::spinner />
                        Tunggu sebentar.......
                    </span>
                </x-wirekit::button>

            </div>

        </x-wirekit::stack>

    </x-wirekit::form>

</x-wirekit::stack>
