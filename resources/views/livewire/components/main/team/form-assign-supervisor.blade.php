<x-wirekit::modal name="update-supervisor">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>

    {{-- =====================================================
        HEADER
    ====================================================== --}}
    <x-wirekit::modal.header>
        <x-wirekit::stack gap="1">

            <h2 class="text-lg font-semibold text-slate-900">
                {{ $team->supervisor ? 'Ubah' : 'Tambah' }} Supervisor
            </h2>

            <p class="text-sm text-slate-500">
                {{ $team->supervisor ? 'Ubah' : 'Tambah' }} supervisor yang bertanggung jawab atas Team ini.
            </p>

        </x-wirekit::stack>
    </x-wirekit::modal.header>


    {{-- =====================================================
        BODY
    ====================================================== --}}
    <x-wirekit::modal.body>

        <x-wirekit::form wire:submit="updateForm">
            <x-wirekit::stack gap="md">


                {{-- =================================================
                CURRENT SUPERVISOR
            ================================================== --}}
                @if ($team->supervisor)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">

                        <p class="mb-3 text-xs font-medium uppercase tracking-wide text-slate-400">
                            Supervisor Saat Ini
                        </p>

                        <div class="flex items-center gap-3">

                            {{-- Avatar --}}
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                               bg-sky-100 text-sm font-semibold text-sky-600">
                                AP
                            </div>

                            <div class="min-w-0">

                                <p class="truncate text-sm font-semibold text-slate-800">
                                    {{ $team->supervisor->user->name }}
                                </p>

                                <p class="text-xs text-slate-500">
                                    Kode Pegawai: {{ $team->supervisor->employee_code }}
                                </p>

                            </div>

                        </div>

                    </div>
                @endif


                {{-- =================================================
                NEW SUPERVISOR
            ================================================== --}}
                <x-wirekit::field>

                    <x-wirekit::label for="supervisor">
                        Supervisor Baru
                    </x-wirekit::label>

                    {{-- Search --}}
                    <div class="relative">

                        <input id="supervisor-search" type="text" placeholder="Cari nama atau Kode Pegawai atau jabatan..."
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm
                               text-slate-900 outline-none transition
                               placeholder:text-slate-400
                               focus:border-sky-400 focus:ring-2 focus:ring-sky-100"
                            wire:model.live.debounce.400ms="search" />

                    </div>


                    {{-- Supervisor List --}}
                    <div class="mt-2 max-h-64 overflow-y-auto rounded-lg border border-slate-200 bg-white">

                        @forelse ($supervisors as $supervisor)
                            <label
                                class="flex cursor-pointer items-center gap-3
                   border-b border-slate-100 px-3 py-3
                   hover:bg-slate-50">

                                <input type="radio" name="supervisor" wire:model.live="employeeId"
                                    value="{{ $supervisor->id }}"
                                    class="h-4 w-4 border-slate-300 text-sky-500
                       focus:ring-sky-400">

                                <div class="min-w-0 flex-1">

                                    <div class="flex items-center gap-2">

                                        <p class="truncate text-sm font-medium text-slate-700">
                                            {{ $supervisor->user->name }}
                                        </p>

                                        @if ($supervisor->position?->name === 'supervisor')
                                            <span
                                                class="rounded-full bg-sky-50 px-2 py-0.5
                                   text-[10px] font-medium text-sky-600">
                                                Supervisor
                                            </span>
                                        @endif

                                    </div>

                                    <p class="text-xs text-slate-400">
                                        {{ $supervisor->position?->name }}
                                        ·
                                        Kode Pegawai: {{ $supervisor->employee_code }}
                                    </p>

                                </div>

                            </label>

                        @empty

                            <div class="px-4 py-8 text-center">

                                <p class="text-sm font-medium text-slate-500">
                                    Tidak ada kandidat supervisor
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Tidak ada employee yang tersedia untuk dipilih.
                                </p>

                            </div>
                        @endforelse

                    </div>

                </x-wirekit::field>


                {{-- =================================================
                WARNING
            ================================================== --}}
                @if ($team->supervisor)
                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">

                        <div class="flex items-start gap-3">

                            <div class="mt-0.5 shrink-0">

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="h-5 w-5 text-amber-500">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 1 1-16 0ZM9 6a1 1 0 0 1 2 0v1a1 1 0 1 1-2 0V6Zm0 4a1 1 0 1 1 2 0v4a1 1 0 1 1-2 0v-4Z"
                                        clip-rule="evenodd" />
                                </svg>

                            </div>

                            <div>

                                <p class="text-sm font-medium text-amber-800">
                                    Perhatian
                                </p>

                                <p class="mt-1 text-xs leading-5 text-amber-700">
                                    Supervisor saat ini akan dilepas dari Team ini dan
                                    supervisor baru akan ditetapkan sebagai penanggung jawab Team.
                                </p>

                            </div>

                        </div>

                    </div>
                @endif


                {{-- =================================================
                FOOTER
            ================================================== --}}
                <div class="flex justify-end gap-2 pt-2">

                    <x-wirekit::modal.close>

                        <x-wirekit::button type="button" size="sm">
                            Cancel
                        </x-wirekit::button>

                    </x-wirekit::modal.close>


                    <x-wirekit::button type="submit" size="sm" class="bg-[#30AFFF] text-white hover:bg-sky-500">
                        {{ $team->supervisor ? 'Ubah' : 'Tambah' }} Supervisor
                    </x-wirekit::button>

                </div>

            </x-wirekit::stack>
        </x-wirekit::form>

    </x-wirekit::modal.body>

</x-wirekit::modal>
