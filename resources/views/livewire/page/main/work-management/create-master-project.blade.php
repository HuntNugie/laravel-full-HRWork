<x-wirekit::stack gap="md">
    <div>
        <div class="mb-2 flex items-center gap-2">
            <a href="{{ route('work-management.master-projects') }}" wire:navigate class="text-sm text-[#30AFFF] hover:underline">Master Projects</a>
            <span class="text-sm text-slate-400">/ Create</span>
        </div>

        <x-wirekit::stack gap="1">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Create Master Project</h1>
            <p class="text-sm text-slate-500">Buat project utama perusahaan sebagai induk division project.</p>
        </x-wirekit::stack>
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Informasi Project</h2>
                <p class="text-sm text-slate-500">Lengkapi informasi dasar project sebelum disimpan.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <form wire:submit="save" class="space-y-5">
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nama project</label>
                    <x-wirekit::input id="name" type="text" wire:model="name" name="name" placeholder="Masukkan nama master project" />
                    @error('name') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi</label>
                    <textarea id="description" wire:model="description" rows="5" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20" placeholder="Jelaskan tujuan dan ruang lingkup project"></textarea>
                    @error('description') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label for="start_date" class="mb-2 block text-sm font-medium text-slate-700">Tanggal mulai</label>
                        <input id="start_date" type="date" wire:model="start_date" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20" />
                        @error('start_date') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="due_date" class="mb-2 block text-sm font-medium text-slate-700">Deadline</label>
                        <input id="due_date" type="date" wire:model="due_date" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20" />
                        @error('due_date') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <x-wirekit::button
                        type="button"
                        variant="outline"
                        href="{{ route('work-management.master-projects') }}"
                        wire:navigate
                    >
                        Batal
                    </x-wirekit::button>

                    <x-wirekit::button type="submit" class="bg-[#30AFFF] text-white hover:bg-[#1599E8]">
                        Simpan Master Project
                    </x-wirekit::button>
                </div>
            </form>
        </x-wirekit::card.body>
    </x-wirekit::card>
</x-wirekit::stack>