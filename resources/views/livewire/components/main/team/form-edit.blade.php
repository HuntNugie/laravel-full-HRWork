<x-wirekit::modal name="update-team">

    <x-slot:trigger>

        {{ $slot }}

    </x-slot:trigger>



{{-- =====================================================
    HEADER
====================================================== --}}

    <x-wirekit::modal.header>

        <x-wirekit::stack gap="1">

            <h2 class="text-lg font-semibold text-slate-900">

                Edit Team

            </h2>

            <p class="text-sm text-slate-500">

                Edit Team yang ada di dalam struktur organisasi.

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

                    <x-wirekit::label for="division-name" class="text-black">

                        Nama Team

                    </x-wirekit::label>

                    <x-wirekit::input id="division-name" type="text" name="name" class="text-black"

                        wire:model.live.debounce.500ms="name" placeholder="Contoh: Human Resources" />



                </x-wirekit::field>



{{-- =================================================
                DESCRIPTION
                ================================================== --}}

                <x-wirekit::field>

                    <x-wirekit::label for="Team-description" class="text-black">

                        Deskripsi Team

                    </x-wirekit::label>

                    <x-wirekit::textarea id="Team-description" wire:model.live.debounce.500ms="desc" name="desc"

                        class="text-black" rows="4" placeholder="Deskripsi mengenai team..." />



                </x-wirekit::field>

                <x-wirekit::field>

                    <x-wirekit::label for="divisi" class="text-black">

                         Divisi

                    </x-wirekit::label>

                    <x-wirekit::select id="divisi" class="text-black" name="Divisi" placeholder="Pilih Divisi..." :options="$divisis" wire:model.live="divisiId" />

                </x-wirekit::field>



{{-- =================================================
                SUPERVISOR
                ================================================== --}}

                <x-wirekit::field>

                    <x-wirekit::label for="supervisor" class="text-black">

                        Supervisor

                    </x-wirekit::label>

                    <x-wirekit::select
                        id="supervisor"
                        class="text-black"
                        name="supervisorId"
                        placeholder="Pilih Supervisor..."
                        :options="$supervisors"
                        wire:model.live="supervisorId"
                    />

                    <p class="mt-1 text-xs text-slate-400">

                        Supervisor harus merupakan karyawan dengan jabatan Supervisor.

                    </p>

                </x-wirekit::field>



{{-- =================================================
                STATUS
                ================================================== --}}

                <div class="flex items-center justify-between rounded-lg border border-slate-200 p-4">

                    <div>

                        <p class="text-sm font-medium text-slate-700">

                            Status Team

                        </p>

                        <p class="mt-1 text-xs text-slate-400">

                            Tentukan apakah Team aktif digunakan.

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



                    <x-wirekit::button type="submit" wire:loading.attr="disabled" wire:target="update" size="sm"

                        class="bg-[#30AFFF] text-white hover:bg-sky-500" :disabled="!$this->canSubmit()">

                        <span wire:loading.remove wire:target="update">

                            Edit Team

                        </span>

                        <span wire:loading wire:target="update">

                            Saving...

                        </span>

                    </x-wirekit::button>

                </div>

            </x-wirekit::stack>

        </x-wirekit::form>

    </x-wirekit::modal.body>

</x-wirekit::modal>
