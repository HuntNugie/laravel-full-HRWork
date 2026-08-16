<x-wirekit::stack gap="md">

    {{-- =====================================================
    PAGE HEADING
    ====================================================== --}}
    <x-wirekit::stack gap="sm">

        <span class="text-sm font-medium text-[#30AFFF]">
            <a href="{{ route('divisi.view') }}" wire:navigate
                class="mb-3 inline-flex items-center text-sm font-medium text-slate-500 hover:text-[#30AFFF]">
                ← Kembali
            </a>
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Detail Divisi
        </h1>

        <p class="text-sm text-slate-500">
            Informasi divisi dan daftar team yang berada di dalamnya.
        </p>

    </x-wirekit::stack>


    {{-- =====================================================
    DIVISION INFORMATION
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">

                {{-- Division Identity --}}
                <div class="flex items-center gap-4">


                    <x-wirekit::stack gap="sm">

                        <h2 class="text-xl font-semibold text-slate-900 uppercase">
                            {{ $divisi->name }}
                        </h2>



                    </x-wirekit::stack>

                </div>


                {{-- Actions --}}
                <div class="flex gap-2">

                    <livewire:components.main.divisi.form-edit>
                        <x-wirekit::button type="button" class="px-3 py-2" wire:click="$dispatch('open-edit',{id:{{ $divisi->id }}})">
                            Edit Division
                        </x-wirekit::button>
                    </livewire:components.main.divisi.form-edit>

                    <x-wirekit::button type="button" class="bg-[#30AFFF] text-white hover:bg-sky-500">
                        + Add Team
                    </x-wirekit::button>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
    DIVISION DETAILS
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Division Information
                </h2>

                <p class="text-sm text-slate-500">
                    Informasi detail mengenai divisi ini.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                {{-- Division Name --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Division Name
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $divisi->name }}
                    </p>
                </div>


                {{-- Status --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Status
                    </span>

                    <p
                        class="mt-1 text-sm font-medium {{ $divisi->is_active === 'active' ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $divisi->is_active }}
                    </p>
                </div>


                {{-- Total Teams --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Total Teams
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{count($divisi->team)}}
                    </p>
                </div>


                {{-- Description --}}
                <div class="sm:col-span-2 lg:col-span-3">

                    <span class="text-xs font-medium text-slate-400">
                        Description
                    </span>

                    <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-600">
                        {{$divisi->description}}
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
    TEAMS
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Teams
                    </h2>

                    <p class="text-sm text-slate-500">
                        Team yang berada di bawah divisi Information Technology.
                    </p>

                </x-wirekit::stack>


                <span class="text-xs font-medium text-slate-400">
                    {{ count($divisi->team) }} Teams
                </span>

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">


                {{-- =================================================
                TEAM 1
                ================================================== --}}
                @forelse ($divisi->team as $team)
                    <x-wirekit::card class="transition hover:-translate-y-0.5 hover:shadow-md">

                        <x-wirekit::card.body>

                            <x-wirekit::stack gap="md">

                                <div class="flex items-start justify-between">


                                    <span class="inline-flex items-center rounded-full
                                                       bg-emerald-50 px-2.5 py-1
                                                       text-xs font-medium text-emerald-600">
                                        Active
                                    </span>

                                </div>


                                <x-wirekit::stack gap="1">

                                    <h3 class="text-base font-semibold text-slate-900">
                                        Backend Team
                                    </h3>

                                    <p class="text-sm leading-5 text-slate-500">
                                        Mengembangkan dan memelihara layanan backend
                                        serta API perusahaan.
                                    </p>

                                </x-wirekit::stack>


                                <div class="flex items-center justify-between border-t border-slate-100 pt-3">

                                    <span class="text-xs text-slate-400">
                                        Employees
                                    </span>

                                    <span class="text-sm font-semibold text-slate-700">
                                        8
                                    </span>

                                </div>

                            </x-wirekit::stack>

                        </x-wirekit::card.body>

                    </x-wirekit::card>
                @empty
                    <div class="col-span-full"> <x-wirekit::card> <x-wirekit::card.body>
                                <div class="flex flex-col items-center justify-center px-6 py-12 text-center">
                                    <div
                                        class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                        <x-wirekit::icon name="users" class="h-7 w-7" />
                                    </div>
                                    <h3 class="mt-4 text-base font-semibold text-slate-900"> Belum ada team </h3>
                                    <p class="mt-1 max-w-md text-sm text-slate-500"> Belum ada team yang terdaftar pada
                                        divisi
                                        ini. Tambahkan team untuk mulai mengelola anggota divisi. </p>
                                </div>
                            </x-wirekit::card.body> </x-wirekit::card> </div>
                @endforelse



            </div>


        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>