<div class="space-y-6">
    <div>
        <a href="{{ route('work-management.master-projects.show', $masterProject) }}" wire:navigate class="text-sm text-blue-600">← Kembali</a>
        <h1 class="mt-2 text-2xl font-semibold">Create Division Project</h1>
        <p class="text-sm text-gray-500">Master Project: {{ $masterProject->name }}</p>
    </div>
    <form wire:submit="save" class="space-y-4 rounded-xl border bg-white p-6">
        <div>
            <label for="divisi_id" class="mb-1 block text-sm font-medium">Divisi</label>
            <select id="divisi_id" wire:model="divisi_id" class="w-full rounded-lg border-gray-300">
                <option value="">Pilih divisi</option>
                @foreach ($divisions as $division)
                    <option value="{{ $division->id }}">{{ $division->name }} — Manager: {{ $division->manager?->user?->name ?? '-' }}</option>
                @endforeach
            </select>
            @error('divisi_id') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="name" class="mb-1 block text-sm font-medium">Nama division project</label>
            <input id="name" type="text" wire:model="name" class="w-full rounded-lg border-gray-300" />
            @error('name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="description" class="mb-1 block text-sm font-medium">Deskripsi</label>
            <textarea id="description" wire:model="description" rows="4" class="w-full rounded-lg border-gray-300"></textarea>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div><label for="start_date" class="mb-1 block text-sm font-medium">Tanggal mulai</label><input id="start_date" type="date" wire:model="start_date" class="w-full rounded-lg border-gray-300" /></div>
            <div><label for="due_date" class="mb-1 block text-sm font-medium">Deadline</label><input id="due_date" type="date" wire:model="due_date" class="w-full rounded-lg border-gray-300" /></div>
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="is_required" /> Division project wajib untuk penyelesaian master project</label>
        <div class="flex gap-3">
            <a href="{{ route('work-management.master-projects.show', $masterProject) }}" wire:navigate class="rounded-lg border px-4 py-2 text-sm">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white">Simpan</button>
        </div>
    </form>
</div>
