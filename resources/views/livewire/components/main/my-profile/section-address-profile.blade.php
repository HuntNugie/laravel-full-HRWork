<x-wirekit::card>

    <x-wirekit::card.header>

        <x-wirekit::stack gap="1">

            <h2 class="text-lg font-semibold text-slate-900">
                Alamat
            </h2>

            <p class="text-sm text-slate-500">
                Perbarui alamat tempat tinggal Anda.
            </p>

        </x-wirekit::stack>

    </x-wirekit::card.header>


    <x-wirekit::card.body>

        <x-wirekit::stack gap="md">

            {{-- PROVINSI --}}
            <x-wirekit::select label="Provinsi" :options="[
                'jabar' => 'Jawa Barat',
                'jkt' => 'DKI Jakarta',
                'jateng' => 'Jawa Tengah',
            ]" value="jabar" />


            {{-- KABUPATEN / KOTA --}}
            <x-wirekit::select label="Kabupaten / Kota" :options="[
                'bandung' => 'Kota Bandung',
                'cimahi' => 'Kota Cimahi',
                'kab-bandung' => 'Kabupaten Bandung',
            ]" value="bandung" />


            {{-- KECAMATAN --}}
            <x-wirekit::select label="Kecamatan" :options="[
                'sukasari' => 'Sukasari',
                'coblong' => 'Coblong',
                'antapani' => 'Antapani',
            ]" value="sukasari" />


            {{-- KELURAHAN --}}
            <x-wirekit::select label="Kelurahan / Desa" :options="[
                'geger-kalong' => 'Gegerkalong',
                'sukagalih' => 'Sukagalih',
                'sarijadi' => 'Sarijadi',
            ]" value="geger-kalong" />


            {{-- ALAMAT LENGKAP --}}
            <x-wirekit::textarea label="Alamat Lengkap" placeholder="Masukkan alamat lengkap">
                Jl. Contoh No. 123, Bandung
            </x-wirekit::textarea>


            <div class="flex justify-end pt-2">

                <x-wirekit::button type="button">
                    Simpan Perubahan
                </x-wirekit::button>

            </div>

        </x-wirekit::stack>

    </x-wirekit::card.body>

</x-wirekit::card>
