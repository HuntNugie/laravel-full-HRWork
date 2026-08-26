<x-wirekit::modal name="edit-benefit">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>


    {{-- =====================================================
    HEADER
    ====================================================== --}}
    <x-wirekit::modal.header>

        <x-wirekit::stack gap="1">

            <h2 class="text-lg font-semibold text-slate-900">
                Update Benefit
            </h2>

            <p class="text-sm text-slate-500">
                Update Benefit/tunjangan untuk employee.
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
                NAME
                ================================================== --}}
                <x-wirekit::field>

                    <x-wirekit::label for="benefit-name" class="text-black">
                        Nama Benefit/Tunjangan
                    </x-wirekit::label>

                    <x-wirekit::input id="benefit-name" type="text" name="name" class="text-black"
                        wire:model.live.debounce.500ms="name" placeholder="Contoh: Trasnportasi" />



                </x-wirekit::field>


                {{-- =================================================
                DESCRIPTION
                ================================================== --}}
                <x-wirekit::field>

                    <x-wirekit::label for="benefit-description" class="text-black">
                        Deskripsi benefit
                    </x-wirekit::label>

                    <x-wirekit::textarea id="benefit-description" wire:model.live.debounce.500ms="desc" name="desc"
                        class="text-black" rows="4" placeholder="Deskripsi mengenai team..." />



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
                            Ubah Benefit
                        </span>

                        <span wire:loading wire:target="store">
                            Simpan...
                        </span>

                    </x-wirekit::button>

                </div>

            </x-wirekit::stack>

        </x-wirekit::form>

    </x-wirekit::modal.body>

</x-wirekit::modal>
