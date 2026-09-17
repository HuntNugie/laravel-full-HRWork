<x-wirekit::modal name="cancel-leave">
    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>
    <x-wirekit::modal.header>Konfirmasi Batalkan</x-wirekit::modal.header>
    <x-wirekit::modal.body>Apakah Anda yakin ingin membatalkan cuti ini ?</x-wirekit::modal.body>
    <x-wirekit::modal.footer>
        <x-wirekit::modal.close>
            <x-wirekit::button intent="neutral" surface="ghost" size="sm">Batal</x-wirekit::button>
        </x-wirekit::modal.close>
        <x-wirekit::button intent="danger" size="sm" wire:click="cancel">Ya, Batalkan</x-wirekit::button>
    </x-wirekit::modal.footer>
</x-wirekit::modal>
