<x-wirekit::modal name="delete-role">
    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>
    <x-wirekit::modal.header>Konfirmasi Hapus</x-wirekit::modal.header>
    <x-wirekit::modal.body>Apakah Anda yakin ingin menghapus role <strong>{{ $role->name }}</strong> ?</x-wirekit::modal.body>
    <x-wirekit::modal.footer>
        <x-wirekit::modal.close>
            <x-wirekit::button intent="neutral" surface="ghost" size="sm">Batal</x-wirekit::button>
        </x-wirekit::modal.close>
        <x-wirekit::button intent="danger" size="sm" wire:click="deleteRole">Hapus</x-wirekit::button>
    </x-wirekit::modal.footer>
</x-wirekit::modal>
