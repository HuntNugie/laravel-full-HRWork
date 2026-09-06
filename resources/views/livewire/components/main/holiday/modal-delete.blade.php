<x-wirekit::modal name="delete-holiday">
    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>
    <x-wirekit::modal.header>Konfirmasi Hapus</x-wirekit::modal.header>
    <x-wirekit::modal.body>Apakah Anda yakin ingin menghapus Hari libur {{ $holiday->name }} ?</x-wirekit::modal.body>
    <x-wirekit::modal.footer>
        <x-wirekit::modal.close>
            <x-wirekit::button intent="neutral" surface="ghost" size="sm">Batal</x-wirekit::button>
        </x-wirekit::modal.close>
        <x-wirekit::button intent="danger" size="sm" wire:click="delete">Hapus</x-wirekit::button>
    </x-wirekit::modal.footer>
</x-wirekit::modal>
