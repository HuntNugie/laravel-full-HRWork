<div class="space-y-6">
    <div><a href="{{ route('work-management.division-projects.show', $divisionProject) }}" wire:navigate class="text-sm text-blue-600">← Kembali</a><h1 class="mt-2 text-2xl font-semibold">Create Task</h1><p class="text-sm text-gray-500">{{ $divisionProject->name }}</p></div>
    <form wire:submit="save" class="space-y-4 rounded-xl border bg-white p-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div><label class="mb-1 block text-sm font-medium">Team</label><select wire:model.live="team_id" class="w-full rounded-lg border-gray-300"><option value="">Pilih team</option>@foreach ($teams as $team)<option value="{{ $team->id }}">{{ $team->name }}</option>@endforeach</select></div>
            <div><label class="mb-1 block text-sm font-medium">Assignee</label><select wire:model="assignee_id" class="w-full rounded-lg border-gray-300"><option value="">Pilih karyawan</option>@foreach ($assignees as $assignee)<option value="{{ $assignee->id }}">{{ $assignee->user?->name ?? $assignee->id }}</option>@endforeach</select></div>
        </div>
        @error('team_id') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        @error('assignee_id') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        <div><label class="mb-1 block text-sm font-medium">Judul Task</label><input type="text" wire:model="title" class="w-full rounded-lg border-gray-300" />@error('title')<span class="text-sm text-red-600">{{ $message }}</span>@enderror</div>
        <div><label class="mb-1 block text-sm font-medium">Deskripsi</label><textarea wire:model="description" rows="4" class="w-full rounded-lg border-gray-300"></textarea></div>
        <div><label class="mb-1 block text-sm font-medium">Deadline</label><input type="date" wire:model="due_date" class="w-full rounded-lg border-gray-300" /></div>
        <div class="flex gap-3"><a href="{{ route('work-management.division-projects.show', $divisionProject) }}" wire:navigate class="rounded-lg border px-4 py-2 text-sm">Batal</a><button class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white">Simpan</button></div>
    </form>
</div>
