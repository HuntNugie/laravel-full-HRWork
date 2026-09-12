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

        <x-wirekit::form wire:submit='save'>
            <x-wirekit::stack gap="md">

                <div class="grid gap-5 md:grid-cols-2">

                    {{-- =================================================
                        Provinsi
                        ================================================== --}}
                    <x-wirekit::select label="Provinsi" name="province_code" wire:model.live="provinceCode"
                        placeholder="Pilih provinsi..." :options="$this->provinceOptions()" />


                    {{-- =================================================
                        Kabupaten / Kota
                        ================================================== --}}
                    <x-wirekit::select label="Kabupaten/Kota" name="regency_code" wire:model.live="regencyCode"
                        placeholder="Pilih kabupaten/kota..." :options="$this->regencyOptions()" />


                    {{-- =================================================
                        Kecamatan
                        ================================================== --}}
                    <x-wirekit::select label="Kecamatan" name="district_code" wire:model.live="districtCode"
                        placeholder="Pilih kecamatan..." :options="$this->districtOptions()" />


                    {{-- =================================================
                        Kelurahan / Desa
                        ================================================== --}}
                    <x-wirekit::select label="Kelurahan/Desa" name="village_code" wire:model.live="villageCode"
                        placeholder="Pilih kelurahan/desa..." :options="$this->villageOptions()" />


                    {{-- =================================================
                        Alamat Lengkap
                        ================================================== --}}
                    <div class="md:col-span-2">

                        <div>

                            <x-wirekit::textarea class="text-black" label="Alamat Lengkap" name="detailAddress"
                                wire:model.live.debounce.500ms="detailAddress" maxlength="255"
                                placeholder="Contoh: Jl. Sukajadi No. 10 RT 03/RW 05" />

                        </div>

                    </div>

                </div>
                <div class="flex justify-end pt-2">

                    <x-wirekit::button type="submit">
                        <span wire:loading.remove wire:target='save'>
                            Simpan Perubahan
                        </span>
                        <span wire:loading wire:target='save'>
                            <x-wirekit::spinner />
                            Tunggu Sebentar
                        </span>
                    </x-wirekit::button>

                </div>

            </x-wirekit::stack>
        </x-wirekit::form>
    </x-wirekit::card.body>

</x-wirekit::card>
