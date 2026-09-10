<x-wirekit::modal name="edit-rekening">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>


    {{-- =====================================================
        HEADER
    ====================================================== --}}
    <x-wirekit::modal.header>

        <x-wirekit::stack gap="1">

            <h2 class="text-lg font-semibold text-slate-900">
                Edit Rekening
            </h2>

            <p class="text-sm text-slate-500">
                Perbarui informasi rekening karyawan.
            </p>

        </x-wirekit::stack>

    </x-wirekit::modal.header>


    {{-- =====================================================
        BODY
    ====================================================== --}}
    <x-wirekit::modal.body>

        <x-wirekit::form wire:submit="update">

            <x-wirekit::stack gap="md">

                {{-- =================================================
                    BANK
                ================================================== --}}
                <x-wirekit::field>

                    <x-wirekit::label for="bank" class="text-black">
                        Bank
                    </x-wirekit::label>

                    <select id="bank" name="bank" wire:model.live="bankId"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-black">
                        <option value="">Pilih bank</option>
                        @foreach ($banks as $id => $bank)
                            <option value="{{ $id }}">{{ $bank }}</option>
                        @endforeach
                    </select>

                </x-wirekit::field>


                {{-- =================================================
                    NOMOR REKENING
                ================================================== --}}
                <x-wirekit::field>

                    <x-wirekit::label for="account-number" class="text-black">
                        Nomor Rekening
                    </x-wirekit::label>

                    <x-wirekit::input id="account-number" type="text" name="noRek" class="text-black"
                        wire:model.live.debounce.500ms="noRek" placeholder="Masukkan nomor rekening" />

                </x-wirekit::field>


                <x-wirekit::field>

                    <x-wirekit::label for="account-name" class="text-black">
                        Nama Pemilik Rekening
                    </x-wirekit::label>

                    <x-wirekit::input id="account-name" type="text" name="holder" class="text-black"
                        wire:model.live.debounce.500ms="holder" placeholder="Masukkan nama pemilik rekening" />

                </x-wirekit::field>


                {{-- =================================================
                    FOOTER
                ================================================== --}}
                <div class="flex justify-end gap-2 pt-2">

                    <x-wirekit::modal.close>

                        <x-wirekit::button type="button" size="sm">
                            Cancel
                        </x-wirekit::button>

                    </x-wirekit::modal.close>


                    <x-wirekit::button type="submit" wire:loading.attr="disabled" wire:target="store" size="sm"
                        :disabled="!$this->canSubmit()" class="bg-[#30AFFF] text-white hover:bg-sky-500">

                        <span wire:loading.remove wire:target="store">
                            Simpan Perubahan
                        </span>

                        <span wire:loading wire:target="store">
                            Saving...
                        </span>

                    </x-wirekit::button>

                </div>

            </x-wirekit::stack>

        </x-wirekit::form>

    </x-wirekit::modal.body>

</x-wirekit::modal>
