<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('work-management.master-projects.show', $divisionProject->masterProject) }}" wire:navigate class="text-sm text-blue-600">← Master Project</a>
            <h1 class="mt-2 text-2xl font-semibold">{{ $divisionProject->name }}</h1>
            <p class="text-sm text-gray-500">{{ $divisionProject->division?->name ?? '-' }} · Manager: {{ $divisionProject->manager?->user?->name ?? '-' }}</p>
        </div>
        @can('create', \App\Models\Task::class)
            @if ($divisionProject->status !== 'completed')
                <a href="{{ route('work-management.division-projects.tasks.create', $divisionProject) }}" wire:navigate class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white">Buat Task</a>
            @endif
        @endcan
    </div>

    @if (session('success')) <div class="rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ session('success') }}</div> @endif

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border bg-white p-4"><div class="text-xs uppercase text-gray-500">Status</div><div class="mt-1 font-semibold">{{ str_replace('_', ' ', $divisionProject->status) }}</div></div>
        <div class="rounded-xl border bg-white p-4"><div class="text-xs uppercase text-gray-500">Automatic</div><div class="mt-1 font-semibold">{{ $divisionProject->automaticProgress() }}%</div></div>
        <div class="rounded-xl border bg-white p-4"><div class="text-xs uppercase text-gray-500">Manual</div><div class="mt-1 font-semibold">{{ $divisionProject->manual_progress }}%</div></div>
        <div class="rounded-xl border bg-white p-4"><div class="text-xs uppercase text-gray-500">Tasks</div><div class="mt-1 font-semibold">{{ $divisionProject->tasks->count() }}</div></div>
    </div>

    @can('assignTeam', $divisionProject)
        @if ($availableTeams->isNotEmpty())
            <form wire:submit="assignTeam" class="rounded-xl border bg-white p-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-end">
                    <div class="flex-1">
                        <label class="mb-1 block text-sm font-medium">Tambah Team</label>
                        <select wire:model="team_id" class="w-full rounded-lg border-gray-300">
                            <option value="">Pilih team</option>
                            @foreach ($availableTeams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }} — {{ $team->supervisor?->user?->name ?? '-' }}</option>
                            @endforeach
                        </select>
                        @error('team_id') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white">Assign Team</button>
                </div>
            </form>
        @endif
    @endcan

    @can('reportProgress', $divisionProject)
        <form wire:submit="reportProgress" class="space-y-3 rounded-xl border bg-white p-4">
            <div><h2 class="font-semibold">Manual Progress</h2><p class="text-xs text-gray-500">Tersimpan terpisah dari automatic progress task.</p></div>
            <div class="grid gap-3 md:grid-cols-3">
                <div><label class="mb-1 block text-sm font-medium">Progress (%)</label><input type="number" min="0" max="100" wire:model="manualProgress" class="w-full rounded-lg border-gray-300" /></div>
                <div class="md:col-span-2"><label class="mb-1 block text-sm font-medium">Catatan</label><input type="text" wire:model="progressNote" class="w-full rounded-lg border-gray-300" /></div>
            </div>
            <button class="rounded-lg border px-4 py-2 text-sm">Simpan Progress Manual</button>
        </form>
    @endcan

    @can('review', $divisionProject)
        @if ($divisionProject->status === 'ready_for_review')
            <form wire:submit="review" class="space-y-3 rounded-xl border bg-white p-4">
                <h2 class="font-semibold">Review Manager</h2>
                <select wire:model="reviewDecision" class="w-full rounded-lg border-gray-300"><option value="">Pilih keputusan</option><option value="approved">Approve</option><option value="rejected">Request Revision</option></select>
                <textarea wire:model="reviewFeedback" rows="3" class="w-full rounded-lg border-gray-300" placeholder="Feedback"></textarea>
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white">Simpan Review</button>
            </form>
        @endif
    @endcan

    <div class="overflow-hidden rounded-xl border bg-white">
        <div class="border-b px-4 py-3"><h2 class="font-semibold">Team</h2></div>
        <div class="divide-y">
            @forelse ($divisionProject->teams as $team)
                <div class="flex items-center justify-between p-4"><div><div class="font-medium">{{ $team->name }}</div><div class="text-sm text-gray-500">Supervisor: {{ $team->supervisor?->user?->name ?? '-' }}</div></div></div>
            @empty
                <div class="p-8 text-center text-gray-500">Belum ada team yang ditugaskan.</div>
            @endforelse
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border bg-white">
        <div class="border-b px-4 py-3"><h2 class="font-semibold">Tasks</h2></div>
        <div class="divide-y">
            @forelse ($divisionProject->tasks as $task)
                <div class="space-y-2 p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div><a href="{{ route('work-management.tasks.show', $task) }}" wire:navigate class="font-medium text-blue-600 hover:underline">{{ $task->title }}</a><div class="text-xs text-gray-500">{{ $task->team?->name ?? '-' }} · {{ $task->assignee?->user?->name ?? '-' }}</div></div>
                        <div class="text-sm">{{ str_replace('_', ' ', $task->status) }}</div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500"><span>Progress</span><span>{{ $task->progress }}%</span></div>
                    <div class="h-2 rounded-full bg-gray-100"><div class="h-2 rounded-full bg-blue-600" style="width: {{ $task->progress }}%"></div></div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">Belum ada task.</div>
            @endforelse
        </div>
    </div>
</div>
