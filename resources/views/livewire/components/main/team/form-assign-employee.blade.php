<x-wirekit::modal name="assign-employee">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>

    {{-- =====================================================
        HEADER
    ====================================================== --}}
    <x-wirekit::modal.header>
        <x-wirekit::stack gap="1">
            <h2 class="text-lg font-semibold text-slate-900">
                Tambah Karyawan
            </h2>

            <p class="text-sm text-slate-500">
                Tambahkan satu atau beberapa karyawan ke dalam Team ini.
            </p>
        </x-wirekit::stack>
    </x-wirekit::modal.header>


    {{-- =====================================================
        BODY
    ====================================================== --}}
    <x-wirekit::modal.body>

        <x-wirekit::form wire:submit="addEmployees">

            <x-wirekit::stack gap="md">

                {{-- =================================================
                    EMPLOYEE
                ================================================== --}}
                <x-wirekit::field>

                    <x-wirekit::label for="employee-search" class="text-black">
                        Karyawan
                    </x-wirekit::label>

                    {{-- SEARCH --}}
                    <input
                        id="employee-search"
                        type="text"
                        wire:model.live.400ms="search"
                        placeholder="Cari nama..."
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm
                               text-slate-900 outline-none transition
                               placeholder:text-slate-400
                               focus:border-sky-400 focus:ring-2 focus:ring-sky-100"
                    >

                    {{-- EMPLOYEE LIST --}}
                    <div class="mt-2 max-h-60 overflow-y-auto rounded-lg border border-slate-200 bg-white">

                        @forelse ($employees as $employee)

                            <label
                                class="flex cursor-pointer items-center gap-3 border-b border-slate-100 px-3 py-2.5
                                       last:border-b-0 hover:bg-slate-50"
                            >
                                <input
                                    type="checkbox"
                                    value="{{ $employee->id }}"
                                    wire:model.live="employeeId"
                                    class="h-4 w-4 rounded border-slate-300 text-sky-500
                                           focus:ring-sky-400"
                                >

                                <div>
                                    <div class="text-sm font-medium text-slate-700">
                                        {{ $employee->user->name }}
                                    </div>

                                    <div class="text-xs text-slate-400">
                                        {{ $employee->position->name }}
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        {{ $employee->profile->nik }}
                                    </div>
                                </div>
                            </label>

                        @empty

                            <div class="px-3 py-8 text-center text-sm text-slate-400">
                                Karyawan tidak ditemukan.
                            </div>

                        @endforelse
                    </div>

                    {{-- SELECTED COUNT --}}
                    <p class="mt-1 text-xs text-slate-400">
                        {{ count($employeeId) }} karyawan dipilih
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Pilih satu atau beberapa karyawan yang akan ditambahkan ke Team ini.
                    </p>

                </x-wirekit::field>


                {{-- =================================================
                    INFO
                ================================================== --}}
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">

                    <div class="flex items-start gap-3">

                        <div class="mt-0.5 shrink-0">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                class="h-5 w-5 text-slate-400"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M18 10a8 8 0 1 1-16 0ZM9 6a1 1 0 1 2 2 0 0 1-2 0V6Zm0 4a1 1 0 1 1 2 0 0 1-2 0v4a1 1 0 1 1-2 0v-4Z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>

                        <p class="text-xs leading-5 text-slate-500">
                            Hanya karyawan yang belum terdaftar pada Team lain yang dapat dipilih.
                        </p>

                    </div>

                </div>


                {{-- =================================================
                    FOOTER
                ================================================== --}}
                <div class="flex justify-end gap-2 pt-2">

                    <x-wirekit::modal.close>
                        <x-wirekit::button type="button" size="sm">
                            Cancel
                        </x-wirekit::button>
                    </x-wirekit::modal.close>

                    <x-wirekit::button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="addEmployees"
                        size="sm"
                        class="bg-[#30AFFF] text-white hover:bg-sky-500"
                        :disabled="!$this->canSubmit()"
                    >
                        <span wire:loading.remove wire:target="addEmployees">
                            Add Employees
                        </span>

                        <span wire:loading wire:target="addEmployees">
                            Saving...
                        </span>
                    </x-wirekit::button>

                </div>

            </x-wirekit::stack>

        </x-wirekit::form>

    </x-wirekit::modal.body>

</x-wirekit::modal>
