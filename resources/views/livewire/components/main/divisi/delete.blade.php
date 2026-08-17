<x-wirekit::modal name="delete-divisi">
    <x-slot:trigger>
       {{ $slot }}
    </x-slot:trigger>
    <x-wirekit::modal.header class="text-black">Konfirmasi Hapus</x-wirekit::modal.header>
    <x-wirekit::modal.body class="text-black">Kamu yakin akan menghapus Divisi ini ? </x-wirekit::modal.body>
    <x-wirekit::modal.footer>
        <x-wirekit::modal.close>
            <x-wirekit::button intent="neutral" surface="ghost" size="sm" class="text-black">Tidak</x-wirekit::button>
        </x-wirekit::modal.close>
        <x-wirekit::button intent="danger" size="sm" wire:click="delete">Hapus</x-wirekit::button>
    </x-wirekit::modal.footer>
</x-wirekit::modal>