<x-wirekit::stack gap="md">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <x-wirekit::stack gap="sm">

        <span class="text-sm font-medium text-[#30AFFF]">
            Account
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            My Profile
        </h1>

        <p class="text-sm text-slate-500">
            Kelola informasi profil dan akun Anda.
        </p>

    </x-wirekit::stack>


    {{-- =====================================================
        PROFILE HEADER
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-6 sm:flex-row sm:items-center">

                {{-- Avatar --}}
                <div class="shrink-0">

                    @if ($avatar)
                        {{-- Livewire temporary preview --}}
                        <img
                            src="{{ $avatar->temporaryUrl() }}"
                            alt="Preview profile"
                            class="h-24 w-24 rounded-full object-cover
                                   border-4 border-white shadow-md
                                   ring-1 ring-slate-200"
                        >
                    @else
                        {{-- Existing avatar / fallback --}}
                        <div
                            class="flex h-24 w-24 items-center justify-center
                                   rounded-full bg-[#92EEFF]
                                   text-3xl font-bold text-sky-700
                                   ring-1 ring-slate-200"
                        >
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif

                </div>


                {{-- User Information --}}
                <x-wirekit::stack gap="1">

                    <h2 class="text-xl font-semibold text-slate-900">
                        {{ auth()->user()->name }}
                    </h2>

                    <p class="text-sm text-slate-500">
                        {{ auth()->user()->email }}
                    </p>

                    <div class="mt-2 flex items-center gap-2">

                        <span
                            class="inline-flex items-center rounded-full
                                   bg-sky-50 px-2.5 py-1
                                   text-xs font-medium text-sky-600"
                        >
                            {{ auth()->user()->getRoleNames()->first() ?? 'User' }}
                        </span>

                        <span
                            class="inline-flex items-center rounded-full
                                   bg-emerald-50 px-2.5 py-1
                                   text-xs font-medium text-emerald-600"
                        >
                            Active
                        </span>

                    </div>

                </x-wirekit::stack>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        PROFILE CONTENT
    ====================================================== --}}
    <div class="grid gap-6 lg:grid-cols-3">


        {{-- =================================================
            PERSONAL INFORMATION
        ================================================== --}}
        <x-wirekit::card class="lg:col-span-2">

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Personal Information
                    </h2>

                    <p class="text-sm text-slate-500">
                        Informasi dasar yang digunakan dalam sistem.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::stack gap="md">

                    {{-- Full Name --}}
                    <div class="grid gap-2">

                        <label class="text-sm font-medium text-slate-700">
                            Full Name
                        </label>

                        <input
                            type="text"
                            wire:model="name"
                            class="w-full rounded-lg border border-slate-200
                                   bg-white px-3 py-2.5 text-sm
                                   text-slate-900 outline-none
                                   transition
                                   focus:border-[#30AFFF]
                                   focus:ring-2 focus:ring-[#30AFFF]/20"
                        >

                        @error('name')
                            <span class="text-xs text-red-500">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- Email --}}
                    <div class="grid gap-2">

                        <label class="text-sm font-medium text-slate-700">
                            Email
                        </label>

                        <input
                            type="email"
                            wire:model="email"
                            class="w-full rounded-lg border border-slate-200
                                   bg-white px-3 py-2.5 text-sm
                                   text-slate-900 outline-none
                                   transition
                                   focus:border-[#30AFFF]
                                   focus:ring-2 focus:ring-[#30AFFF]/20"
                        >

                        @error('email')
                            <span class="text-xs text-red-500">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- Phone --}}
                    <div class="grid gap-2">

                        <label class="text-sm font-medium text-slate-700">
                            Phone Number
                        </label>

                        <input
                            type="text"
                            wire:model="phone"
                            placeholder="08xxxxxxxxxx"
                            class="w-full rounded-lg border border-slate-200
                                   bg-white px-3 py-2.5 text-sm
                                   text-slate-900 outline-none
                                   transition
                                   focus:border-[#30AFFF]
                                   focus:ring-2 focus:ring-[#30AFFF]/20"
                        >

                        @error('phone')
                            <span class="text-xs text-red-500">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- Save --}}
                    <div class="flex justify-end pt-2">

                        <x-wirekit::button
                            type="button"
                            wire:click="updateProfile"
                            class="bg-[#30AFFF] text-white hover:bg-sky-500"
                        >
                            Save Changes
                        </x-wirekit::button>

                    </div>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>



        {{-- =================================================
            PROFILE PHOTO
        ================================================== --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Profile Photo
                    </h2>

                    <p class="text-sm text-slate-500">
                        Gunakan foto yang jelas dan profesional.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::stack gap="md">

                    {{-- Large Preview --}}
                    <div class="flex justify-center">

                        @if ($avatar)

                            <img
                                src="{{ $avatar->temporaryUrl() }}"
                                alt="Profile preview"
                                class="h-40 w-40 rounded-full object-cover
                                       border-4 border-white shadow-lg
                                       ring-1 ring-slate-200"
                            >

                        @else

                            <div
                                class="flex h-40 w-40 items-center justify-center
                                       rounded-full bg-slate-100
                                       text-5xl font-bold text-slate-400
                                       ring-1 ring-slate-200"
                            >
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>

                        @endif

                    </div>


                    {{-- Upload --}}
                    <div>

                        <label
                            for="avatar"
                            class="flex cursor-pointer flex-col
                                   items-center justify-center
                                   rounded-xl border-2 border-dashed
                                   border-slate-200 bg-slate-50
                                   px-4 py-6 text-center
                                   transition
                                   hover:border-[#30AFFF]
                                   hover:bg-sky-50"
                        >

                            <span class="text-sm font-medium text-slate-700">
                                Choose profile photo
                            </span>

                            <span class="mt-1 text-xs text-slate-400">
                                JPG, JPEG, PNG atau WEBP · Maks. 2MB
                            </span>

                            <input
                                id="avatar"
                                type="file"
                                wire:model="avatar"
                                accept="image/jpeg,image/png,image/webp"
                                class="hidden"
                            >

                        </label>

                        @error('avatar')
                            <p class="mt-2 text-xs text-red-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Upload Progress --}}
                    <div
                        wire:loading
                        wire:target="avatar"
                        class="text-xs text-slate-500"
                    >
                        Uploading image...
                    </div>


                    {{-- Save Avatar --}}
                    <x-wirekit::button
                        type="button"
                        wire:click="updateAvatar"
                        wire:loading.attr="disabled"
                        wire:target="avatar,updateAvatar"
                        class="w-full bg-[#30AFFF] text-white hover:bg-sky-500"
                    >
                        Save Photo
                    </x-wirekit::button>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =====================================================
        ACCOUNT INFORMATION
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Account Information
                </h2>

                <p class="text-sm text-slate-500">
                    Informasi akun yang dikelola oleh sistem.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-5 sm:grid-cols-3">

                {{-- Role --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Role
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ auth()->user()->getRoleNames()->first() ?? 'User' }}
                    </p>
                </div>


                {{-- Status --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Status
                    </span>

                    <p class="mt-1 text-sm font-medium text-emerald-600">
                        Active
                    </p>
                </div>


                {{-- Joined --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Member Since
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ auth()->user()->created_at?->format('d F Y') }}
                    </p>
                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>