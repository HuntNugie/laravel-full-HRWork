<x-wirekit::modal name="edit-position">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>


    {{-- =====================================================
    HEADER
    ====================================================== --}}
    <x-wirekit::modal.header>

        <x-wirekit::stack gap="1">

            <h2 class="text-lg font-semibold text-slate-900">
                Update Position
            </h2>

            <p class="text-sm text-slate-500">
                Update Position ke dalam struktur organisasi.
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

                    <x-wirekit::label for="Position-name" class="text-black">
                        Nama Jabatan/Position
                    </x-wirekit::label>

                    <x-wirekit::input id="Position-name" type="text" name="name" class="text-black"
                        wire:model.live.debounce.500ms="name" placeholder="Contoh: Human Resources" />



                </x-wirekit::field>


                {{-- =================================================
                DESCRIPTION
                ================================================== --}}
                <x-wirekit::field>

                    <x-wirekit::label for="Position-description" class="text-black">
                        Deskripsi
                    </x-wirekit::label>

                    <x-wirekit::textarea id="Position-description" wire:model.live.debounce.500ms="desc" name="desc"
                        class="text-black" rows="4" placeholder="Deskripsi mengenai team..." />



                </x-wirekit::field>
                <x-wirekit::field>

                    <x-wirekit::label for="Position-description" class="text-black">
                        Masukan JobDesk
                    </x-wirekit::label>

                  <x-wirekit::tags-input  name="jobdesk"  placeholder="Add a JobDesk..." :value="$this->jobdesk" optimistic="saveJobdesk" />


                </x-wirekit::field>
                <x-wirekit::field>

                    <div x-data="{
        value: @entangle('salary').live,

        format(value) {
            if (!value) return '';

            return new Intl.NumberFormat('id-ID').format(value);
        },

        parse(value) {
            return value.replace(/\D/g, '');
        },

        onlyNumber(event) {
            const allowedKeys = [
                'Backspace',
                'Delete',
                'ArrowLeft',
                'ArrowRight',
                'ArrowUp',
                'ArrowDown',
                'Tab',
                'Home',
                'End'
            ];

            if (
                allowedKeys.includes(event.key) ||
                event.ctrlKey ||
                event.metaKey
            ) {
                return;
            }

            if (!/^[0-9]$/.test(event.key)) {
                event.preventDefault();
            }
        }
    }">
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Gaji harian
                        </label>

                        <div class="relative">

                            <span
                                class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">
                                Rp.
                            </span>

                            <input type="text" inputmode="numeric"
                                class="w-full rounded-lg border border-slate-300 py-2 pl-10 pr-3 text-sm"
                                :value="format(value)" @keydown="onlyNumber($event)"
                                @input="value = parse($event.target.value)" placeholder="0">

                            @error('salary')
                                <span class="text-sm text-red-600">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>
                    </div>

                </x-wirekit::field>


                {{-- =================================================
                STATUS
                ================================================== --}}
                <div class="flex items-center justify-between rounded-lg border border-slate-200 p-4">

                    <div>

                        <p class="text-sm font-medium text-slate-700">
                            Status Position
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Tentukan apakah Position aktif digunakan.
                        </p>

                    </div>

                    <label class="inline-flex cursor-pointer items-center">

                        <input type="checkbox" wire:model="isActive" class="peer sr-only">

                        <div class="relative h-6 w-11 rounded-full
                                   bg-slate-200
                                   after:absolute after:left-[2px]
                                   after:top-[2px]
                                   after:h-5 after:w-5
                                   after:rounded-full
                                   after:border after:border-slate-300
                                   after:bg-white
                                   after:transition-all
                                   peer-checked:bg-[#30AFFF]
                                   peer-checked:after:translate-x-full
                                   peer-checked:after:border-white"></div>

                    </label>

                </div>

                @error('is_active')
                    <span class="text-xs text-red-500">
                        {{ $message }}
                    </span>
                @enderror


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
                            Update Position
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
