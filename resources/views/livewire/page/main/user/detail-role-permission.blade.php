<x-wirekit::stack gap="md">
    <a href="{{ route('user.show', $user->id) }}" wire:navigate class="text-sm text-sky-600">Kembali ke detail akun</a>
    <h1 class="text-2xl font-bold text-slate-900">Hak Akses — {{ $user->name }}</h1>
    <form wire:submit="save" class="space-y-4">
        @foreach ($groups as $label => $items)
            <x-wirekit::card><x-wirekit::card.body>
                <h2 class="mb-3 font-semibold text-slate-900">{{ $label }}</h2>
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($items as $permission)
                        <label wire:key="{{ $permission }}" class="flex items-center gap-2 text-sm"><input type="checkbox" value="{{ $permission }}" wire:model="permissions" class="rounded border-slate-300 text-sky-500" />{{ str($permission)->replace('-', ' ')->headline() }}</label>
                    @endforeach
                </div>
            </x-wirekit::card.body></x-wirekit::card>
        @endforeach
        @error('permissions.*')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        <x-wirekit::button type="submit">Simpan Hak Akses</x-wirekit::button>
    </form>
</x-wirekit::stack>
