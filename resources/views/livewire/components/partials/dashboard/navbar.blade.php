<div>
    <x-slot:header>

        <x-wirekit::header>

            {{-- Mobile only --}}
            <x-wirekit::sidebar.toggle class="lg:hidden" />

            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2">
                <x-wirekit::image class="h-10 w-10" src="/assets/logo-inovindo.webp" alt="A product shot" ratio="1/1"
                    fit="contain" rounded />

                <span class="font-bold tracking-tight text-slate-900">
                    {{ config('app.name') }}
                </span>
            </a>

            <div class="flex-1"></div>

            <div class="hidden items-center gap-3 sm:flex">
                <x-wirekit::stack gap="0" class="text-right">

                    <p class="text-sm font-semibold text-slate-900">
                        {{ $user->name }}
                    </p>

                    <p class="text-xs text-slate-500">
                        {{ $user->email }}
                    </p>

                </x-wirekit::stack>

                {{-- ini tempat ntar gambar --}}

                @php
                    $avatar = $user->getFirstMedia('avatar');
                @endphp
                <x-wirekit::dropdown>
                    <x-wirekit::dropdown.trigger>
                        @if ($user->getFirstMediaUrl('avatar'))
                            <img wire:key="navbar-avatar-{{ $avatar->uuid }}" src="{{ $avatar->getUrl() }}"
                                alt="gambar dari {{ $user->name }}"
                                class="flex h-9 w-9 items-center justify-center
                       rounded-full bg-[#92EEFF]/60 h-10 w-10">
                        @else
                            <img src="{{ asset('assets/nonProfile.jpg') }}" alt=""
                                class="flex h-9 w-9 items-center justify-center
                      rounded-full bg-[#92EEFF]/60 h-10 w-10">
                        @endif
                    </x-wirekit::dropdown.trigger>
                    <x-wirekit::dropdown.panel>
                        @if ($user?->employees)
                            <x-wirekit::dropdown.item href="{{ route('my-profile') }}"
                                wire:navigate>Profile</x-wirekit::dropdown.item>
                            <x-wirekit::dropdown.separator />
                        @endif
                        <x-wirekit::dropdown.item class="px-4">
                            <livewire:components.main.btn-logout />
                        </x-wirekit::dropdown.item>
                    </x-wirekit::dropdown.panel>
                </x-wirekit::dropdown>
            </div>

        </x-wirekit::header>
    </x-slot:header>

</div>
