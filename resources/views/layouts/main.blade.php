{{-- resources/views/livewire/page/main/dashboard.blade.php --}}

<x-layouts::app :title="$title">
<x-wirekit::toast-region />
@if (session('success'))
    <div
        x-data
        x-init="
            $dispatch('wirekit-toast', {
                variant: 'success',
                title: 'Saved',
                message: @js(session('success'))
            })
        "
    ></div>
@endif
    <x-wirekit::app-shell>

        {{-- =========================================================
        HEADER
        ========================================================== --}}
        <x-slot:header>

            <x-wirekit::header>

                {{-- Mobile only --}}
                <x-wirekit::sidebar.toggle class="lg:hidden" />

                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2">
                    <x-wirekit::image class="h-10 w-10" src="/assets/logo-inovindo.webp" alt="A product shot"
                        ratio="1/1" fit="contain" rounded />

                    <span class="font-bold tracking-tight text-slate-900">
                        {{ config('app.name') }}
                    </span>
                </a>

                <div class="flex-1"></div>

                <div class="hidden items-center gap-3 sm:flex">
                    <x-wirekit::stack gap="0" class="text-right">

                        <p class="text-sm font-semibold text-slate-900">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="text-xs text-slate-500">
                            {{ auth()->user()->getRoleNames()->first() }}
                        </p>

                    </x-wirekit::stack>

                    {{-- ini tempat ntar gambar --}}

                    <x-wirekit::dropdown>
                        <x-wirekit::dropdown.trigger>
                             <x-wirekit::image class="flex h-9 w-9 items-center justify-center
                       rounded-full bg-[#92EEFF]/60 h-10 w-10" src="/assets/logo-inovindo.webp" alt="A product shot"
                        ratio="1/1" fit="contain" rounded />
                        </x-wirekit::dropdown.trigger>
                        <x-wirekit::dropdown.panel>
                            <x-wirekit::dropdown.item href="{{ route('my-profile') }}" wire:navigate>Profile</x-wirekit::dropdown.item>
                            <x-wirekit::dropdown.separator />
                            <x-wirekit::dropdown.item class="px-4">
                                <livewire:components.main.btn-logout/>
                            </x-wirekit::dropdown.item>
                        </x-wirekit::dropdown.panel>
                    </x-wirekit::dropdown>
                </div>

            </x-wirekit::header>

        </x-slot:header>



        {{-- =========================================================
        SIDEBAR
        ========================================================== --}}
        <x-partials.dashboard.sidebar />



        {{-- =========================================================
        MAIN CONTENT
        ========================================================== --}}
        <x-wirekit::main :container="true">

            {{ $slot }}

        </x-wirekit::main>

    </x-wirekit::app-shell>

</x-layouts::app>
