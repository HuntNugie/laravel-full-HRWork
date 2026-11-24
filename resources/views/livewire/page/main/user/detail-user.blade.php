<x-wirekit::stack gap="md">
    <a wire:navigate href="{{ route('user.view') }}" class="text-sm text-sky-600">Kembali ke akun HRD</a>
    <h1 class="text-2xl font-bold text-slate-900">Detail Akun HRD</h1>
    <x-wirekit::card><x-wirekit::card.body>
        <form wire:submit="save" class="space-y-4">
            <p class="text-sm text-slate-500">Role: HRD · Status: {{ $user->status }}</p>
            <x-wirekit::input label="Nama" name="name" wire:model="name" />
            <x-wirekit::input label="Email" name="email" wire:model="email" />
            @can('update-user')<x-wirekit::button type="submit">Simpan</x-wirekit::button>@endcan
        </form>
        <div class="mt-4 flex flex-wrap gap-3">
            @can('change-user-status')
                <x-wirekit::button wire:click="actionStatusAccount" wire:confirm="Ubah status akun HRD?">{{ $user->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}</x-wirekit::button>
            @endcan
            @can('manage-hr-access')
                <x-wirekit::button href="{{ route('user.role.show', $user->id) }}" wire:navigate>Atur hak akses</x-wirekit::button>
            @endcan
        </div>
        @can('reset-user-password')
            <div class="mt-4">
                <livewire:components.main.user.modal-change-password :user="$user">
                    <x-wirekit::button>Reset password</x-wirekit::button>
                </livewire:components.main.user.modal-change-password>
            </div>
        @endcan
    </x-wirekit::card.body></x-wirekit::card>
</x-wirekit::stack>
