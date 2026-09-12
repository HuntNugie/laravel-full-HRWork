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
                        <img src="{{ asset($user->getFirstMediaUrl('avatar')) }}" alt="Foto profil {{ $user->name }}"
                            class="size-full object-cover rounded-full">
                    </div>

                    <div class="min-w-0">

                        <div class="flex flex-wrap items-center gap-2">

                            <h2 class="text-xl font-semibold text-slate-900">
                                {{ $user?->name }}
                            </h2>

                            <x-wirekit::badge variant="success">
                                {{ $user?->status }}
                            </x-wirekit::badge>

                        </div>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $user?->email }}
                        </p>

                        <div class="mt-2 flex flex-wrap gap-2">

                            @foreach ($user?->roles as $role)
                                <span
                                    class="inline-flex items-center rounded-full
                                       bg-sky-50 px-2.5 py-1
                                       text-xs font-medium text-sky-600">
                                    {{ strtoupper($role?->name) }}
                                </span>
                            @endforeach


                            <span
                                class="inline-flex items-center rounded-full
                                       bg-slate-100 px-2.5 py-1
                                       text-xs font-medium text-slate-600">
                                akun dibuat sejak {{ $user->created_at->format('d F Y') }}
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
    <livewire:components.main.my-profile.section-address-profile :user="$user" />


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
                        {{ $user->employees->profile->nik }}
                    </p>

                </div>


                {{-- JABATAN --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Jabatan
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $user->employees?->position?->name ?? 'Belum mempunyai jabatan' }}
                    </p>

                </div>


                {{-- TEAM --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Team
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $user->employees?->team?->name ?? 'Belum mempunyai team' }}
                    </p>

                </div>


                {{-- DIVISI --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Divisi
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $user->employees?->team?->divisi?->name ?? 'Tidak ada divisi' }}
                    </p>

                </div>


                {{-- STATUS --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Status Kepegawaian
                    </p>

                    <p
                        class="mt-1 text-sm font-medium {{ $user->employees->status_employee === 'active' ? 'text-emerald-600' : 'text-danger-600' }}">
                        {{ $user->employees->status_employee }}
                    </p>

                </div>


                {{-- TANGGAL BERGABUNG --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Tanggal Akun dibuat
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $user->created_at->format('d F Y') }}
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


                <livewire:components.main.my-profile.modal-change-password :user="$user">
                    <x-wirekit::button type="button" variant="ghost" intent="danger">
                        Ubah Password
                    </x-wirekit::button>
                </livewire:components.main.my-profile.modal-change-password>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


</x-wirekit::stack>
