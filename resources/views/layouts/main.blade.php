{{-- resources/views/livewire/page/main/dashboard.blade.php --}}

<x-layouts::app :title="$title">
    <x-wirekit::toast-region />
    @if (session('success'))
        <div x-data x-init="$dispatch('wirekit-toast', {
            variant: 'success',
            title: 'Saved',
            message: @js(session('success'))
        })"></div>
    @endif
    <x-wirekit::app-shell>

        {{-- =========================================================
        HEADER
        ========================================================== --}}
        <livewire:components.partials.dashboard.navbar />



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
