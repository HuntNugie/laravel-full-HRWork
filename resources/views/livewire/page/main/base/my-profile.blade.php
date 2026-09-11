<x-wirekit::stack gap="md">

    {{-- PAGE HEADING --}}
    <x-wirekit::stack gap="sm">

        <span class="text-sm font-medium text-[#30AFFF]">
            Account
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            My Profile
        </h1>

        <p class="text-sm text-slate-500">
            Kelola informasi pribadi dan lihat informasi kepegawaian Anda.
        </p>

    </x-wirekit::stack>


    {{-- PROFILE HEADER --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-4">

                    <div
                        class="flex size-16 shrink-0 items-center justify-center
                               rounded-full bg-sky-100">
                        <span class="text-xl font-semibold text-sky-600">
                            N
                        </span>
                    </div>

                    <div class="min-w-0">

                        <div class="flex flex-wrap items-center gap-2">

                            <h2 class="text-xl font-semibold text-slate-900">
                                {{ auth()->user()->name }}
                            </h2>

                            <x-wirekit::badge variant="success">
                                {{ auth()->user()->status }}
                            </x-wirekit::badge>

                        </div>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ auth()->user()->email }}
                        </p>

                        <div class="mt-2 flex flex-wrap gap-2">

                            @foreach (auth()->user()?->roles as $role)
                                <span
                                    class="inline-flex items-center rounded-full
                                       bg-sky-50 px-2.5 py-1
                                       text-xs font-medium text-sky-600">
                                    {{ strtoupper($role->name) }}
                                </span>
                            @endforeach


                            <span
                                class="inline-flex items-center rounded-full
                                       bg-slate-100 px-2.5 py-1
                                       text-xs font-medium text-slate-600">
                                akun dibuat sejak {{ auth()->user()->created_at->format('d F Y') }}
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- PERSONAL INFORMATION --}}
    <livewire:components.main.my-profile.section-profile />


    {{-- PROFILE PHOTO --}}
    <livewire:components.main.my-profile.section-photo-profile />


    {{-- ADDRESS --}}
    <livewire:components.main.my-profile.section-address-profile />


    {{-- EMPLOYMENT INFORMATION --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Informasi Kepegawaian
                </h2>

                <p class="text-sm text-slate-500">
                    Informasi ini dikelola oleh HR dan tidak dapat diubah sendiri.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                {{-- NIK --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        NIK
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        3273XXXXXXXXXXXX
                    </p>

                </div>


                {{-- JABATAN --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Jabatan
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        Backend Developer
                    </p>

                </div>


                {{-- TEAM --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Team
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        Engineering
                    </p>

                </div>


                {{-- DIVISI --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Divisi
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        Technology
                    </p>

                </div>


                {{-- STATUS --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Status Kepegawaian
                    </p>

                    <p class="mt-1 text-sm font-medium text-emerald-600">
                        Aktif
                    </p>

                </div>


                {{-- TANGGAL BERGABUNG --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Tanggal Bergabung
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        12 Agustus 2025
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- ACCOUNT & SECURITY --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Keamanan Akun
                </h2>

                <p class="text-sm text-slate-500">
                    Kelola keamanan akun Anda.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-800">
                        Password
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Ubah password secara berkala untuk menjaga keamanan akun.
                    </p>

                </div>


                <x-wirekit::button type="button" variant="ghost">
                    Ubah Password
                </x-wirekit::button>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


</x-wirekit::stack>
