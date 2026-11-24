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
                        @if ($user->getFirstMediaUrl('avatar'))
                            <img src="{{ asset($user->getFirstMediaUrl('avatar')) }}"
                                alt="Foto profil {{ $user->name }}" class="size-full object-cover rounded-full">
                        @else
                            <img src="{{ asset('assets/nonProfile.jpg') }}" alt="Foto profil {{ $user->name }}"
                                class="size-full object-cover rounded-full">
                        @endif
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
