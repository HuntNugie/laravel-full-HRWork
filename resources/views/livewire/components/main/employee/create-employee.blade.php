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
            Tambah Karyawan Baru
        </h1>

        <p class="text-sm text-black">
            Tambahkan data karyawan beserta informasi akun,
            pribadi, pekerjaan, dan rekening bank.
        </p>

    </x-wirekit::stack>


    {{-- =====================================================
    MAIN FORM
    ====================================================== --}}
    <x-wirekit::form wire:submit="store">

        <x-wirekit::stack gap="md">


            {{-- =================================================
            1. ACCOUNT INFORMATION
            ================================================== --}}
            <x-wirekit::card>

                <x-wirekit::card.header>

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-black">
                            Informasi Akun
                        </h2>

                        <p class="text-sm text-black/60">
                            Informasi akun yang akan digunakan karyawan
                            untuk masuk ke dalam sistem.
                        </p>

                    </x-wirekit::stack>

                </x-wirekit::card.header>


                <x-wirekit::card.body>

                    <div class="grid gap-5 md:grid-cols-2">


                        {{-- =================================================
                        Full Name
                        ================================================== --}}
                        <div class="md:col-span-2">

                            <div x-data="{ text: '', max: 100, min: 2 }">

                                <x-wirekit::input class="text-black" label="Nama Lengkap" name="form.fullname"
                                    x-model="text" wire:model.live.debounce.500ms="form.fullname" maxlength="100"
                                    placeholder="Contoh: Nugie Pratama" />

                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.375rem; font-size: var(--text-wk-sm); gap: 0.5rem;">
                                    <div
                                        style="min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">


                                        <span x-cloak x-show="text.length >= max"
                                            style="color: var(--color-wk-danger-text);">
                                            Batas maksimal karakter tercapai.
                                        </span>

                                    </div>

                                    <span
                                        style="color: var(--color-wk-text-muted); flex-shrink: 0; white-space: nowrap;"
                                        x-text="`${text.length}/${max}`">
                                        0/100
                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                        Email
                        ================================================== --}}
                        <x-wirekit::input class="text-black" label="Email" name="form.email" type="email"
                            wire:model.live.debounce.500ms="form.email" placeholder="karyawan@perusahaan.com" />


                        {{-- =================================================
                        Password
                        ================================================== --}}
                        <x-wirekit::password-input label="Kata Sandi Baru" name="password"
                            wire:model.live.debounce.500ms="form.password" :strength-meter="true"
                            hint="Gunakan minimal 8 karakter dengan kombinasi huruf, angka, dan simbol." />
                        {{-- =================================================
                        Password
                        ================================================== --}}
                        <x-wirekit::password-input label="Konfirmasi password" name="form.password_confirmation"
                            wire:model.live.debounce.500ms="form.password_confirmation" />

                    </div>

                </x-wirekit::card.body>

            </x-wirekit::card>



            {{-- =================================================
            2. PERSONAL INFORMATION
            ================================================== --}}
            <x-wirekit::card>

                <x-wirekit::card.header>

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-black">
                            Informasi Pribadi
                        </h2>

                        <p class="text-sm text-black/60">
                            Informasi pribadi dan kontak karyawan.
                        </p>

                    </x-wirekit::stack>

                </x-wirekit::card.header>


                <x-wirekit::card.body>

                    <div class="grid gap-5 md:grid-cols-2">


                        {{-- =================================================
                        NIK
                        ================================================== --}}
                        <div>

                            <div x-data="{ text: '', max: 16 }">

                                <x-wirekit::input class="text-black" label="NIK" name="form.nik" x-model="text"
                                    wire:model.live.debounce.500ms="form.nik" maxlength="16" inputmode="numeric"
                                    placeholder="Masukkan NIK" />

                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.375rem; font-size: var(--text-wk-sm); gap: 0.5rem;">


                                    <span
                                        style="color: var(--color-wk-text-muted); flex-shrink: 0; white-space: nowrap;"
                                        x-text="`${text.length}/${max}`">
                                        0/16
                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                        Gender
                        ================================================== --}}
                        <x-wirekit::select label="Jenis Kelamin" name="form.gender" wire:model.live="form.gender"
                            placeholder="Pilih jenis kelamin..." :options="[
        'male' => 'Laki-laki',
        'female' => 'Perempuan',
    ]" />


                        {{-- =================================================
                        Phone Number
                        ================================================== --}}
                        <div>

                            <x-wirekit::input class="text-black" label="Nomor Telepon" name="form.phone" x-model="text"
                                wire:model.live.debounce.500ms="form.phone" maxlength="15" inputmode="numeric"
                                placeholder="08xxxxxxxxxx" />


                        </div>

                        <div class="md:col-span-2">

                            <div class="grid gap-5 md:grid-cols-2">




                                {{-- Provinsi --}}
                                <x-wirekit::select label="Provinsi" name="province_code"
                                    wire:model.live="form.provinceCode" placeholder="Pilih provinsi..."
                                    :options="$this->form->provinceOptions()" />


                                {{-- Kabupaten/Kota --}}
                                <x-wirekit::select label="Kabupaten/Kota" name="regency_code"
                                    wire:model.live="form.regencyCode" placeholder="Pilih kabupaten/kota..."
                                    :options="$this->form->regencyOptions()" />


                                {{-- Kecamatan --}}
                                <x-wirekit::select label="Kecamatan" name="district_code"
                                    wire:model.live="form.districtCode" placeholder="Pilih kecamatan..."
                                    :options="$this->form->districtOptions()" />


                                {{-- Kelurahan/Desa --}}
                                <x-wirekit::select label="Kelurahan/Desa" name="village_code"
                                    wire:model.live="form.villageCode" placeholder="Pilih kelurahan/desa..."
                                    :options="$this->form->villageOptions()" />


                                {{-- Detail Alamat --}}
                                <div class="md:col-span-2">

                                    <x-wirekit::input class="text-black" label="Detail Alamat" name="form.detailAddress"
                                        wire:model.live.debounce.500ms="form.detailAddress" maxlength="255"
                                        placeholder="Contoh: Jl. Sukajadi No. 10" />

                                </div>

                            </div>


                        </div>

                    </div>


                    {{-- =================================================
                    Address Detail
                    ================================================== --}}




                </x-wirekit::card.body>

            </x-wirekit::card>



            {{-- =================================================
            3. JOB ASSIGNMENT
            ================================================== --}}
            <x-wirekit::card>

                <x-wirekit::card.header>

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-black">
                            Penempatan Kerja
                        </h2>

                        <p class="text-sm text-black/60">
                            Tentukan posisi dan tim tempat karyawan bekerja.
                        </p>

                    </x-wirekit::stack>

                </x-wirekit::card.header>


                <x-wirekit::card.body>

                    <div class="grid gap-5 md:grid-cols-2">


                        {{-- Team --}}
                        <x-wirekit::select label="Tim" name="form.teamId" wire:model.live="form.teamId"
                            placeholder="Pilih tim..." :options="$this->teams" />


                        {{-- Position --}}
                        <x-wirekit::select label="Posisi" name="form.positionId" wire:model.live="form.positionId"
                            placeholder="Pilih posisi..." :options="$this->positions" />

                    </div>

                </x-wirekit::card.body>

            </x-wirekit::card>




            {{-- =================================================
            5. BANK ACCOUNT
            ================================================== --}}
            <x-wirekit::card>

                <x-wirekit::card.header>

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-black">
                            Rekening Bank
                        </h2>

                        <p class="text-sm text-black/60">
                            Informasi rekening bank yang digunakan
                            untuk kebutuhan penggajian karyawan.
                        </p>

                    </x-wirekit::stack>

                </x-wirekit::card.header>


                <x-wirekit::card.body>

                    <div class="grid gap-5 md:grid-cols-2">


                        {{-- Bank --}}
                        <x-wirekit::select label="Bank" name="bank_id" wire:model.live="form.bankId"
                            placeholder="Pilih bank..." :options="$this->banks" />


                        {{-- Account Number --}}
                        <div>

                            <div x-data="{ text: '', max: 30, min: 5 }">

                                <x-wirekit::input class="text-black" label="Nomor Rekening" name="form.accountNumber"
                                    x-model="text" wire:model.live.debounce.500ms="form.accountNumber" maxlength="30"
                                    inputmode="numeric" placeholder="Masukkan nomor rekening" />

                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.375rem; font-size: var(--text-wk-sm); gap: 0.5rem;">
                                    <div
                                        style="min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">

                                        <span x-cloak x-show="text.length > 0 && text.length < min"
                                            style="color: var(--color-wk-danger-text);">
                                            Minimal 5 karakter.
                                        </span>

                                        <span x-cloak x-show="text.length >= max"
                                            style="color: var(--color-wk-danger-text);">
                                            Batas maksimal karakter tercapai.
                                        </span>

                                    </div>

                                    <span
                                        style="color: var(--color-wk-text-muted); flex-shrink: 0; white-space: nowrap;"
                                        x-text="`${text.length}/${max}`">
                                        0/30
                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- Account Holder --}}
                        <div class="md:col-span-2">

                            <div x-data="{ text: '', max: 100, min: 3 }">

                                <x-wirekit::input class="text-black" label="Nama Pemilik Rekening" name="form.accountHolder"
                                    x-model="text" wire:model.live.debounce.500ms="form.accountHolder" maxlength="100"
                                    placeholder="Nama yang terdaftar pada rekening" />

                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.375rem; font-size: var(--text-wk-sm); gap: 0.5rem;">
                                    <div
                                        style="min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">


                                        <span x-cloak x-show="text.length >= max"
                                            style="color: var(--color-wk-danger-text);">
                                            Batas maksimal karakter tercapai.
                                        </span>

                                    </div>

                                    <span
                                        style="color: var(--color-wk-text-muted); flex-shrink: 0; white-space: nowrap;"
                                        x-text="`${text.length}/${max}`">
                                        0/100
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </x-wirekit::card.body>

            </x-wirekit::card>



            {{-- =================================================
            FORM ACTION
            ================================================== --}}
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">

                <x-wirekit::button type="button" class="border border-slate-200 bg-white text-black hover:bg-slate-50"
                    wire:click="cancel">
                    Batal
                </x-wirekit::button>


                <x-wirekit::button type="submit" class="bg-[#30AFFF] text-white hover:bg-sky-500">
                    Tambah Karyawan
                </x-wirekit::button>

            </div>

        </x-wirekit::stack>

    </x-wirekit::form>

</x-wirekit::stack>
