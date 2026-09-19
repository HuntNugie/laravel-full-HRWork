<x-wirekit::modal name="edit-unpresent-discipline-rule">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>


    {{-- HEADER --}}
    <x-wirekit::modal.header>

        <x-wirekit::stack gap="xs">

            <h2 class="text-lg font-semibold text-slate-900">
                Edit Aturan Tidak Hadir
            </h2>

            <p class="text-sm text-slate-500">
                Ubah batas ketidakhadiran tanpa keterangan yang berlaku.
            </p>

        </x-wirekit::stack>

    </x-wirekit::modal.header>


    {{-- BODY --}}
    <x-wirekit::form wire:submit="update">

        <x-wirekit::modal.body>

            <x-wirekit::stack gap="md">

                {{-- THRESHOLD --}}
                <div>

                    <x-wirekit::input type="number" label="Batas Ketidakhadiran" wire:model="threshold" min="1"
                        placeholder="Contoh: 3" />

                    <p class="mt-1 text-xs text-slate-400">
                        Jumlah ketidakhadiran tanpa keterangan yang memicu
                        Surat Peringatan.
                    </p>

                </div>


                {{-- PERIODE --}}
                <x-wirekit::select label="Periode Perhitungan" wire:model="periodType">

                    <option value="monthly">
                        Bulanan
                    </option>

                </x-wirekit::select>


                {{-- KONSEKUENSI --}}
                <div>

                    <p class="text-sm font-medium text-slate-700">
                        Konsekuensi
                    </p>

                    <div class="mt-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">

                        <p class="text-sm text-slate-700">
                            Surat Peringatan
                        </p>

                    </div>

                    <p class="mt-1 text-xs text-slate-400">
                        Konsekuensi tetap berupa Surat Peringatan.
                    </p>

                </div>


                {{-- DESKRIPSI --}}
                <x-wirekit::textarea label="Deskripsi" placeholder="Jelaskan aturan ketidakhadiran..." rows="4"
                    wire:model="description" />

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


                <x-wirekit::button type="submit" wire:loading.attr="disabled" wire:target="update">

                    <span wire:loading.remove wire:target="update">
                        Simpan Perubahan
                    </span>

                    <span wire:loading wire:target="update">
                        mohon tunggu sebentar
                    </span>

                </x-wirekit::button>

            </x-wirekit::row>

        </x-wirekit::modal.footer>

    </x-wirekit::form>

</x-wirekit::modal>
