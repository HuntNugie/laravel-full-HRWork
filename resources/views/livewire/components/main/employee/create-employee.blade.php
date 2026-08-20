<x-wirekit::stack gap="md">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <x-wirekit::stack gap="sm">

        <a
            href="{{ route('employee.view') }}"
            wire:navigate
            class="inline-flex w-fit items-center gap-2 text-sm font-medium text-black transition hover:text-[#30AFFF]"
        >
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
    <x-wirekit::form wire:submit="save">

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

                            <div x-data="{ text: '', max: 100, min: 3 }">

                                <x-wirekit::input
                                    class="text-black"
                                    label="Nama Lengkap"
                                    name="name"
                                    x-model="text"
                                    wire:model.live.debounce.500ms="fullname"
                                    maxlength="100"
                                    placeholder="Contoh: Nugie Pratama"
                                />

                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.375rem; font-size: var(--text-wk-sm); gap: 0.5rem;"
                                >
                                    <div
                                        style="min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                    >

                                        <span
                                            x-cloak
                                            x-show="text.length > 0 && text.length < min"
                                            style="color: var(--color-wk-danger-text);"
                                        >
                                            Minimal 3 karakter.
                                        </span>

                                        <span
                                            x-cloak
                                            x-show="text.length >= max"
                                            style="color: var(--color-wk-danger-text);"
                                        >
                                            Batas maksimal karakter tercapai.
                                        </span>

                                    </div>

                                    <span
                                        style="color: var(--color-wk-text-muted); flex-shrink: 0; white-space: nowrap;"
                                        x-text="`${text.length}/${max}`"
                                    >
                                        0/100
                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                            Email
                        ================================================== --}}
                        <x-wirekit::input
                            class="text-black"
                            label="Email"
                            name="email"
                            type="email"
                            wire:model.live.debounce.500ms="email"
                            placeholder="karyawan@perusahaan.com"
                        />


                        {{-- =================================================
                            Password
                        ================================================== --}}
                        <x-wirekit::password-input
                            label="Kata Sandi Baru"
                            name="password"
                            wire:model.live.debounce.500ms="password"
                            :strength-meter="true"
                            hint="Gunakan minimal 8 karakter dengan kombinasi huruf, angka, dan simbol."
                        />

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

                            <div x-data="{ text: '', max: 16, min: 16 }">

                                <x-wirekit::input
                                    class="text-black"
                                    label="NIK"
                                    name="nik"
                                    x-model="text"
                                    wire:model.live.debounce.500ms="nik"
                                    maxlength="16"
                                    inputmode="numeric"
                                    placeholder="Masukkan NIK"
                                />

                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.375rem; font-size: var(--text-wk-sm); gap: 0.5rem;"
                                >
                                    <div
                                        style="min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                    >

                                        <span
                                            x-cloak
                                            x-show="text.length > 0 && text.length < min"
                                            style="color: var(--color-wk-danger-text);"
                                        >
                                            NIK harus terdiri dari 16 karakter.
                                        </span>

                                        <span
                                            x-cloak
                                            x-show="text.length >= max"
                                            style="color: var(--color-wk-danger-text);"
                                        >
                                            Batas maksimal karakter tercapai.
                                        </span>

                                    </div>

                                    <span
                                        style="color: var(--color-wk-text-muted); flex-shrink: 0; white-space: nowrap;"
                                        x-text="`${text.length}/${max}`"
                                    >
                                        0/16
                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                            Gender
                        ================================================== --}}
                        <x-wirekit::select
                            label="Jenis Kelamin"
                            name="gender"
                            wire:model.live="gender"
                            placeholder="Pilih jenis kelamin..."
                            :options="[
                                'male' => 'Laki-laki',
                                'female' => 'Perempuan',
                            ]"
                        />


                        {{-- =================================================
                            Phone Number
                        ================================================== --}}
                        <div>

                            <div x-data="{ text: '', max: 15, min: 10 }">

                                <x-wirekit::input
                                    class="text-black"
                                    label="Nomor Telepon"
                                    name="phone_number"
                                    x-model="text"
                                    wire:model.live.debounce.500ms="phoneNumber"
                                    maxlength="15"
                                    inputmode="numeric"
                                    placeholder="08xxxxxxxxxx"
                                />

                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.375rem; font-size: var(--text-wk-sm); gap: 0.5rem;"
                                >
                                    <div
                                        style="min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                    >

                                        <span
                                            x-cloak
                                            x-show="text.length > 0 && text.length < min"
                                            style="color: var(--color-wk-danger-text);"
                                        >
                                            Minimal 10 karakter.
                                        </span>

                                        <span
                                            x-cloak
                                            x-show="text.length >= max"
                                            style="color: var(--color-wk-danger-text);"
                                        >
                                            Batas maksimal karakter tercapai.
                                        </span>

                                    </div>

                                    <span
                                        style="color: var(--color-wk-text-muted); flex-shrink: 0; white-space: nowrap;"
                                        x-text="`${text.length}/${max}`"
                                    >
                                        0/15
                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                            Address Detail
                        ================================================== --}}
                        <div class="md:col-span-2">

                            <div x-data="{ text: '', max: 255, min: 5 }">

                                <x-wirekit::input
                                    class="text-black"
                                    label="Detail Alamat"
                                    name="address_detail"
                                    x-model="text"
                                    wire:model.live.debounce.500ms="addressDetail"
                                    maxlength="255"
                                    placeholder="Contoh: Jl. Sukajadi No. 10"
                                />

                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.375rem; font-size: var(--text-wk-sm); gap: 0.5rem;"
                                >
                                    <div
                                        style="min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                    >

                                        <span
                                            x-cloak
                                            x-show="text.length > 0 && text.length < min"
                                            style="color: var(--color-wk-danger-text);"
                                        >
                                            Minimal 5 karakter.
                                        </span>

                                        <span
                                            x-cloak
                                            x-show="text.length >= max"
                                            style="color: var(--color-wk-danger-text);"
                                        >
                                            Batas maksimal karakter tercapai.
                                        </span>

                                    </div>

                                    <span
                                        style="color: var(--color-wk-text-muted); flex-shrink: 0; white-space: nowrap;"
                                        x-text="`${text.length}/${max}`"
                                    >
                                        0/255
                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                            Village
                        ================================================== --}}
                        <x-wirekit::input
                            class="text-black"
                            label="Kelurahan / Desa"
                            name="village"
                            wire:model.live.debounce.500ms="village"
                            maxlength="100"
                            placeholder="Contoh: Pasteur"
                        />


                        {{-- =================================================
                            District
                        ================================================== --}}
                        <x-wirekit::input
                            class="text-black"
                            label="Kecamatan"
                            name="district"
                            wire:model.live.debounce.500ms="district"
                            maxlength="100"
                            placeholder="Contoh: Sukajadi"
                        />


                        {{-- =================================================
                            City
                        ================================================== --}}
                        <x-wirekit::input
                            class="text-black"
                            label="Kota / Kabupaten"
                            name="city"
                            wire:model.live.debounce.500ms="city"
                            maxlength="100"
                            placeholder="Contoh: Bandung"
                        />


                        {{-- =================================================
                            Province
                        ================================================== --}}
                        <x-wirekit::input
                            class="text-black"
                            label="Provinsi"
                            name="province"
                            wire:model.live.debounce.500ms="province"
                            maxlength="100"
                            placeholder="Contoh: Jawa Barat"
                        />


                        {{-- =================================================
                            Postal Code
                        ================================================== --}}
                        <div>

                            <div x-data="{ text: '', max: 5, min: 5 }">

                                <x-wirekit::input
                                    class="text-black"
                                    label="Kode Pos"
                                    name="postal_code"
                                    x-model="text"
                                    wire:model.live.debounce.500ms="postalCode"
                                    maxlength="5"
                                    inputmode="numeric"
                                    placeholder="Contoh: 40161"
                                />

                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.375rem; font-size: var(--text-wk-sm); gap: 0.5rem;"
                                >
                                    <div
                                        style="min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                    >

                                        <span
                                            x-cloak
                                            x-show="text.length > 0 && text.length < min"
                                            style="color: var(--color-wk-danger-text);"
                                        >
                                            Kode pos harus terdiri dari 5 karakter.
                                        </span>

                                        <span
                                            x-cloak
                                            x-show="text.length >= max"
                                            style="color: var(--color-wk-danger-text);"
                                        >
                                            Batas maksimal karakter tercapai.
                                        </span>

                                    </div>

                                    <span
                                        style="color: var(--color-wk-text-muted); flex-shrink: 0; white-space: nowrap;"
                                        x-text="`${text.length}/${max}`"
                                    >
                                        0/5
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

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
                        <x-wirekit::select
                            label="Tim"
                            name="team_id"
                            wire:model.live="teamId"
                            placeholder="Pilih tim..."
                            :options="[
                                '1' => 'Development',
                                '2' => 'Design',
                                '3' => 'Marketing',
                                '4' => 'Finance',
                            ]"
                        />


                        {{-- Position --}}
                        <x-wirekit::select
                            label="Posisi"
                            name="position_id"
                            wire:model.live="positionId"
                            placeholder="Pilih posisi..."
                            :options="[
                                '1' => 'Software Engineer',
                                '2' => 'UI/UX Designer',
                                '3' => 'Marketing Staff',
                                '4' => 'Finance Staff',
                            ]"
                        />

                    </div>

                </x-wirekit::card.body>

            </x-wirekit::card>



            {{-- =================================================
                4. EMPLOYMENT INFORMATION
            ================================================== --}}
            <x-wirekit::card>

                <x-wirekit::card.header>

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-black">
                            Informasi Masa Kerja
                        </h2>

                        <p class="text-sm text-black/60">
                            Informasi mengenai masa kerja karyawan.
                        </p>

                    </x-wirekit::stack>

                </x-wirekit::card.header>


                <x-wirekit::card.body>

                    <div class="grid gap-5 md:grid-cols-2">


                        {{-- Join Date --}}
                        <x-wirekit::input
                            class="text-black"
                            label="Tanggal Bergabung"
                            type="date"
                            name="join_date"
                            wire:model.live="joinDate"
                        />


                        {{-- Resign Date --}}
                        <div>

                            <x-wirekit::input
                                class="text-black"
                                label="Tanggal Berhenti"
                                type="date"
                                name="resign_date"
                                wire:model.live="resignDate"
                            />

                            <span class="mt-1 block text-xs text-black/60">
                                Kosongkan apabila karyawan masih aktif.
                            </span>

                        </div>

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
                        <x-wirekit::select
                            label="Bank"
                            name="bank_id"
                            wire:model.live="bankId"
                            placeholder="Pilih bank..."
                            :options="[
                                '1' => 'Bank Central Asia (BCA)',
                                '2' => 'Bank Mandiri',
                                '3' => 'Bank Negara Indonesia (BNI)',
                                '4' => 'Bank Rakyat Indonesia (BRI)',
                                '5' => 'Bank Syariah Indonesia (BSI)',
                            ]"
                        />


                        {{-- Account Number --}}
                        <div>

                            <div x-data="{ text: '', max: 30, min: 5 }">

                                <x-wirekit::input
                                    class="text-black"
                                    label="Nomor Rekening"
                                    name="account_number"
                                    x-model="text"
                                    wire:model.live.debounce.500ms="accountNumber"
                                    maxlength="30"
                                    inputmode="numeric"
                                    placeholder="Masukkan nomor rekening"
                                />

                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.375rem; font-size: var(--text-wk-sm); gap: 0.5rem;"
                                >
                                    <div
                                        style="min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                    >

                                        <span
                                            x-cloak
                                            x-show="text.length > 0 && text.length < min"
                                            style="color: var(--color-wk-danger-text);"
                                        >
                                            Minimal 5 karakter.
                                        </span>

                                        <span
                                            x-cloak
                                            x-show="text.length >= max"
                                            style="color: var(--color-wk-danger-text);"
                                        >
                                            Batas maksimal karakter tercapai.
                                        </span>

                                    </div>

                                    <span
                                        style="color: var(--color-wk-text-muted); flex-shrink: 0; white-space: nowrap;"
                                        x-text="`${text.length}/${max}`"
                                    >
                                        0/30
                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- Account Holder --}}
                        <div class="md:col-span-2">

                            <div x-data="{ text: '', max: 100, min: 3 }">

                                <x-wirekit::input
                                    class="text-black"
                                    label="Nama Pemilik Rekening"
                                    name="account_holder"
                                    x-model="text"
                                    wire:model.live.debounce.500ms="accountHolder"
                                    maxlength="100"
                                    placeholder="Nama yang terdaftar pada rekening"
                                />

                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.375rem; font-size: var(--text-wk-sm); gap: 0.5rem;"
                                >
                                    <div
                                        style="min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                    >

                                        <span
                                            x-cloak
                                            x-show="text.length > 0 && text.length < min"
                                            style="color: var(--color-wk-danger-text);"
                                        >
                                            Minimal 3 karakter.
                                        </span>

                                        <span
                                            x-cloak
                                            x-show="text.length >= max"
                                            style="color: var(--color-wk-danger-text);"
                                        >
                                            Batas maksimal karakter tercapai.
                                        </span>

                                    </div>

                                    <span
                                        style="color: var(--color-wk-text-muted); flex-shrink: 0; white-space: nowrap;"
                                        x-text="`${text.length}/${max}`"
                                    >
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

                <x-wirekit::button
                    type="button"
                    class="border border-slate-200 bg-white text-black hover:bg-slate-50"
                    wire:click="cancel"
                >
                    Batal
                </x-wirekit::button>


                <x-wirekit::button
                    type="submit"
                    class="bg-[#30AFFF] text-white hover:bg-sky-500"
                >
                    Tambah Karyawan
                </x-wirekit::button>

            </div>

        </x-wirekit::stack>

    </x-wirekit::form>

</x-wirekit::stack>