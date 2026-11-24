<x-wirekit::stack gap="md">
    <div class="flex flex-wrap justify-between gap-3">
        <div><h1 class="text-2xl font-bold text-slate-900">Manajemen Akun HRD</h1><p class="text-sm text-slate-500">Kelola akun aplikasi, terpisah dari data karyawan.</p></div>
        @can('create-user')
            <x-wirekit::button wire:click="$toggle('showCreate')">Tambah akun HRD</x-wirekit::button>
        @endcan
    </div>
    @if ($showCreate)
        <x-wirekit::card><x-wirekit::card.body>
            <form wire:submit="createAccount" class="space-y-4">
                <x-wirekit::input label="Nama" name="name" wire:model="name" />
                <x-wirekit::input label="Email" name="email" type="email" wire:model="email" />
                <x-wirekit::password-input label="Password" name="password" wire:model="password" />
                <x-wirekit::password-input label="Konfirmasi password" name="password_confirmation" wire:model="password_confirmation" />
                <x-wirekit::button type="submit">Buat akun</x-wirekit::button>
            </form>
        </x-wirekit::card.body></x-wirekit::card>
    @endif
    <x-wirekit::card><x-wirekit::card.body>
        <x-wirekit::input placeholder="Cari nama atau email" wire:model.live.debounce.300ms="search" />
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="p-3">Nama</th><th class="p-3">Email</th><th class="p-3">Status</th><th class="p-3">Aksi</th></tr></thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="border-b border-slate-200"><td class="p-3">{{ $user->name }}</td><td class="p-3">{{ $user->email }}</td><td class="p-3">{{ $user->status }}</td><td class="p-3"><a class="text-sky-600" wire:navigate href="{{ route('user.show', $user->id) }}">Detail</a></td></tr>
                    @empty
                        <tr><td colspan="4" class="p-4 text-slate-500">Belum ada akun HRD.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $users->links() }}</div>
    </x-wirekit::card.body></x-wirekit::card>
</x-wirekit::stack>
