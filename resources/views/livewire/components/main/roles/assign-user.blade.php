<x-wirekit::modal name="assign-role">

    {{-- =====================================================
        TRIGGER
    ====================================================== --}}
    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>


    {{-- =====================================================
        HEADER
    ====================================================== --}}
    <x-wirekit::modal.header>

        <x-wirekit::stack gap="1">

            <h2 class="text-lg font-semibold text-slate-900">
                Assign Users
            </h2>

            <p class="text-sm text-slate-500">
                Pilih user yang ingin diberikan role
                <span class="font-medium text-slate-700">
                    HR Manager
                </span>.
            </p>

        </x-wirekit::stack>

    </x-wirekit::modal.header>


    {{-- =====================================================
        BODY
    ====================================================== --}}
    <x-wirekit::form wire:submit="submitAssign">
        <x-wirekit::modal.body>

            <x-wirekit::stack gap="md">


                {{-- Search --}}
                <x-wirekit::input label="Search users" placeholder="Search by name or email..."
                    wire:model.live.debounce.400ms="search" name="search" />



                {{-- Selected Summary --}}
                <div
                    class="flex items-center justify-between rounded-lg
                       border border-slate-200 bg-slate-50 px-4 py-3">

                    <div>

                        <p class="text-sm font-medium text-slate-800">
                            Select users
                        </p>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Users already assigned to this role are not shown.
                        </p>

                    </div>

                    <span
                        class="inline-flex items-center rounded-full
                           bg-sky-100 px-2.5 py-1
                           text-xs font-semibold text-sky-700">
                        {{ count($userSelect) }} selected
                    </span>

                </div>


                {{-- User List --}}
                <div class="max-h-80 overflow-y-auto rounded-lg
                       border border-slate-200">

                    <x-wirekit::stack gap="0">

                        @forelse ($users as $user)
                            {{-- User 1 --}}
                            <label
                                class="flex cursor-pointer items-center gap-3
                               px-4 py-3 transition
                               hover:bg-slate-50">

                                <x-wirekit::checkbox value="{{ $user->id }}" label=""
                                    wire:model.live="userSelect" />

                                <div
                                    class="flex size-9 shrink-0 items-center
                                    justify-center rounded-full bg-sky-100">
                                    <span class="text-sm font-semibold text-sky-600">
                                        AF
                                    </span>
                                </div>

                                <div class="min-w-0 flex-1">

                                    <p class="truncate text-sm font-medium text-slate-900">
                                        {{ $user->name }}
                                    </p>

                                    <p class="truncate text-xs text-slate-500">
                                        {{ $user->email }}
                                    </p>

                                </div>

                            </label>


                            <x-wirekit::divider />



                        @empty
                            <div class="px-4 py-8 text-center">
                                <p class="text-sm font-medium text-slate-700">
                                    Tidak ada user yang tersedia.
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Semua user sudah diberikan role ini atau belum ada data user.
                                </p>
                            </div>
                        @endforelse

                    </x-wirekit::stack>

                </div>

            </x-wirekit::stack>

        </x-wirekit::modal.body>


        {{-- =====================================================
        FOOTER
    ====================================================== --}}
        <x-wirekit::modal.footer>

            <x-wirekit::modal.close>
                <x-wirekit::button type="button" intent="neutral" surface="outline">
                    Cancel
                </x-wirekit::button>
            </x-wirekit::modal.close>

            <x-wirekit::button type="submit" intent="primary">
                Assign Users
            </x-wirekit::button>

        </x-wirekit::modal.footer>
    </x-wirekit::form>
</x-wirekit::modal>
