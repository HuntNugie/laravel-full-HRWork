<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold">Create Master Project</h1>
        <p class="text-sm text-gray-500">Buat project utama perusahaan.</p>
    </div>
    <form wire:submit="save" class="space-y-4 rounded-xl border bg-white p-6">
        <div>
            <label for="name" class="mb-1 block text-sm font-medium">Nama project</label>
            <input id="name" type="text" wire:model="name" class="w-full rounded-lg border-gray-300" />
            @error('name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="description" class="mb-1 block text-sm font-medium">Deskripsi</label>
            <textarea id="description" wire:model="description" rows="4" class="w-full rounded-lg border-gray-300"></textarea>
            @error('description') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="start_date" class="mb-1 block text-sm font-medium">Tanggal mulai</label>
                <input id="start_date" type="date" wire:model="start_date" class="w-full rounded-lg border-gray-300" />
                @error('start_date') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="due_date" class="mb-1 block text-sm font-medium">Deadline</label>
                <input id="due_date" type="date" wire:model="due_date" class="w-full rounded-lg border-gray-300" />
                @error('due_date') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('work-management.master-projects') }}" wire:navigate class="rounded-lg border px-4 py-2 text-sm">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white">Simpan</button>
        </div>
    </form>
</div>
