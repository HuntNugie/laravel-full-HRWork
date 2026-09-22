<x-wirekit::stack gap="md">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <x-wirekit::stack gap="sm">
            <div class="flex items-center gap-2">
                <a href="{{ route('work-management.master-projects.show', $divisionProject->masterProject) }}" wire:navigate class="text-sm text-[#30AFFF] hover:underline">Master Project</a>
                <span class="text-sm text-slate-400">/ Division Project</span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $divisionProject->name }}</h1>
            <p class="text-sm text-slate-500">
                {{ $divisionProject->division?->name ?? '-' }} · Manager: {{ $divisionProject->manager?->user?->name ?? '-' }}
            </p>
        </x-wirekit::stack>

    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $statusClass = match ($divisionProject->status) {
                'completed' => 'bg-emerald-50 text-emerald-600',
                'ready_for_review' => 'bg-violet-50 text-violet-600',
                'submitted_to_manager' => 'bg-indigo-50 text-indigo-600',
                'manager_approved' => 'bg-cyan-50 text-cyan-600',
                'submitted_to_gm' => 'bg-blue-50 text-blue-600',
                'revision_required' => 'bg-amber-50 text-amber-600',
                'in_progress' => 'bg-sky-50 text-sky-600',
                'rejected' => 'bg-rose-50 text-rose-600',
                default => 'bg-slate-100 text-slate-600',
            };
        @endphp

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    <span class="text-sm font-medium text-slate-500">Status</span>
                    <span class="inline-flex w-fit items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">{{ str_replace('_', ' ', $divisionProject->status) }}</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Automatic Progress</span>
                    <span class="text-lg font-bold text-slate-900">{{ $divisionProject->automaticProgress() }}%</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Manual Progress</span>
                    <span class="text-lg font-bold text-slate-900">{{ $divisionProject->manual_progress }}%</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Tasks</span>
                    <span class="text-lg font-bold text-slate-900">{{ $divisionProject->tasks->count() }}</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
    </div>

    @can('assignTeam', $divisionProject)
        @if (in_array($divisionProject->status, ['draft', 'in_progress', 'revision_required'], true) && $availableTeams->isNotEmpty())
            <x-wirekit::card>
                <x-wirekit::card.header>
                    <x-wirekit::stack gap="1">
                        <h2 class="text-lg font-semibold text-slate-900">Assign Team</h2>
                        <p class="text-sm text-slate-500">Tambahkan team yang akan menjalankan division project ini.</p>
                    </x-wirekit::stack>
                </x-wirekit::card.header>

                <x-wirekit::card.body>
                    <form wire:submit="assignTeam">
                        <div class="flex flex-col gap-3 md:flex-row md:items-end">
                            <div class="flex-1">
                                <label class="mb-2 block text-sm font-medium text-slate-700">Team</label>
                                <select wire:model="team_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20">
                                    <option value="">Pilih team</option>
                                    @foreach ($availableTeams as $team)
                                        <option value="{{ $team->id }}">{{ $team->name }} — Supervisor: {{ $team->supervisor?->user?->name ?? '-' }}</option>
                                    @endforeach
                                </select>
                                @error('team_id') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                            </div>

                            <x-wirekit::button type="submit" class="bg-[#30AFFF] text-white hover:bg-[#1599E8]">
                                Assign Team
                            </x-wirekit::button>
                        </div>
                    </form>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endif
    @endcan

    @can('reportProgress', $divisionProject)
        <x-wirekit::card>
            <x-wirekit::card.header>
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Manual Progress</h2>
                    <p class="text-sm text-slate-500">Progress manual disimpan terpisah dari progress otomatis task.</p>
                </x-wirekit::stack>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <form wire:submit="reportProgress" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Progress (%)</label>
                            <x-wirekit::input type="number" min="0" max="100" wire:model="manualProgress" name="manualProgress" />
                            @error('manualProgress') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-slate-700">Catatan</label>
                            <x-wirekit::input type="text" wire:model="progressNote" name="progressNote" placeholder="Tambahkan catatan progress" />
                            @error('progressNote') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <x-wirekit::button type="submit" variant="outline">Simpan Progress Manual</x-wirekit::button>
                </form>
            </x-wirekit::card.body>
        </x-wirekit::card>
    @endcan

    <x-wirekit::card>
        <x-wirekit::card.header>
            <div class="flex items-center justify-between gap-3">
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Team</h2>
                    <p class="text-sm text-slate-500">Team yang ditugaskan ke division project.</p>
                </x-wirekit::stack>
                <span class="text-sm text-slate-500">{{ $divisionProject->teams->count() }} team</span>
            </div>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="wk-scrollbar overflow-x-auto">
                <x-wirekit::table hoverable>
                    <x-wirekit::table.head>
                        <x-wirekit::table.row>
                            <x-wirekit::table.th>Team</x-wirekit::table.th>
                            <x-wirekit::table.th>Supervisor</x-wirekit::table.th>
                            <x-wirekit::table.th>Progress</x-wirekit::table.th>
                            <x-wirekit::table.th>Aksi</x-wirekit::table.th>
                        </x-wirekit::table.row>
                    </x-wirekit::table.head>
                    <x-wirekit::table.body>
                        @forelse ($divisionProject->teams as $team)
                            @php
                                $teamTasks = $divisionProject->tasks->where('team_id', $team->id)->reject(fn ($task) => $task->status === 'cancelled');
                                $teamProgress = $teamTasks->isNotEmpty()
                                    ? (int) round($teamTasks->avg(fn ($task) => (int) $task->progress))
                                    : 0;
                            @endphp
                            <x-wirekit::table.row>
                                <x-wirekit::table.td>
                                    <span class="text-sm font-semibold text-slate-800">{{ $team->name }}</span>
                                </x-wirekit::table.td>
                                <x-wirekit::table.td><span class="text-sm text-slate-600">{{ $team->supervisor?->user?->name ?? '-' }}</span></x-wirekit::table.td>
                                <x-wirekit::table.td>
                                    <div class="min-w-32">
                                        <div class="mb-1 flex justify-between text-xs text-slate-500">
                                            <span>{{ $teamProgress }}%</span>
                                            <span>{{ $teamTasks->count() }} task</span>
                                        </div>
                                        <div class="h-2 rounded-full bg-slate-100">
                                            <div class="h-2 rounded-full bg-[#30AFFF]" style="width: {{ $teamProgress }}%"></div>
                                        </div>
                                    </div>
                                </x-wirekit::table.td>
                                <x-wirekit::table.td>
                                    <x-wirekit::button
                                        variant="outline"
                                        size="sm"
                                        href="{{ route('work-management.division-projects.teams.show', ['divisionProject' => $divisionProject, 'team' => $team]) }}"
                                        wire:navigate
                                    >
                                        Lihat Perkembangan
                                    </x-wirekit::button>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @empty
                            <x-wirekit::table.row>
                                <x-wirekit::table.td colspan="4"><div class="py-10 text-center text-sm text-slate-500">Belum ada team yang ditugaskan.</div></x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @endforelse
                    </x-wirekit::table.body>
                </x-wirekit::table>
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <div class="flex items-center justify-between gap-3">
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Tasks</h2>
                    <p class="text-sm text-slate-500">Task yang berada dalam division project ini.</p>
                </x-wirekit::stack>
                <span class="text-sm text-slate-500">{{ $divisionProject->tasks->count() }} task</span>
            </div>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="wk-scrollbar overflow-x-auto">
                <x-wirekit::table hoverable>
                    <x-wirekit::table.head>
                        <x-wirekit::table.row>
                            <x-wirekit::table.th>Task</x-wirekit::table.th>
                            <x-wirekit::table.th>Team</x-wirekit::table.th>
                            <x-wirekit::table.th>Assignee</x-wirekit::table.th>
                            <x-wirekit::table.th>Progress</x-wirekit::table.th>
                            <x-wirekit::table.th>Status</x-wirekit::table.th>
                            <x-wirekit::table.th>Deadline</x-wirekit::table.th>
                        </x-wirekit::table.row>
                    </x-wirekit::table.head>
                    <x-wirekit::table.body>
                        @forelse ($divisionProject->tasks as $task)
                            @php
                                $taskStatusClass = match ($task->status) {
                                    'done' => 'bg-emerald-50 text-emerald-600',
                                    'in_review' => 'bg-violet-50 text-violet-600',
                                    'in_progress' => 'bg-sky-50 text-sky-600',
                                    'blocked' => 'bg-amber-50 text-amber-600',
                                    'cancelled' => 'bg-rose-50 text-rose-600',
                                    default => 'bg-slate-100 text-slate-600',
                                };
                            @endphp

                            <x-wirekit::table.row>
                                <x-wirekit::table.td>
                                    <a href="{{ route('work-management.tasks.show', $task) }}" wire:navigate class="text-sm font-semibold text-[#168ED1] hover:underline">{{ $task->title }}</a>
                                </x-wirekit::table.td>
                                <x-wirekit::table.td><span class="text-sm text-slate-700">{{ $task->team?->name ?? '-' }}</span></x-wirekit::table.td>
                                <x-wirekit::table.td><span class="text-sm text-slate-700">{{ $task->assignee?->user?->name ?? '-' }}</span></x-wirekit::table.td>
                                <x-wirekit::table.td>
                                    <div class="min-w-28">
                                        <div class="mb-1 flex justify-between text-xs text-slate-500"><span>{{ $task->progress }}%</span></div>
                                        <div class="h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-[#30AFFF]" style="width: {{ $task->progress }}%"></div></div>
                                    </div>
                                </x-wirekit::table.td>
                                <x-wirekit::table.td><span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $taskStatusClass }}">{{ str_replace('_', ' ', $task->status) }}</span></x-wirekit::table.td>
                                <x-wirekit::table.td><span class="text-sm text-slate-700">{{ $task->due_date?->format('d M Y') ?? '—' }}</span></x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @empty
                            <x-wirekit::table.row>
                                <x-wirekit::table.td colspan="6"><div class="py-10 text-center text-sm text-slate-500">Belum ada task.</div></x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @endforelse
                    </x-wirekit::table.body>
                </x-wirekit::table>
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>
</x-wirekit::stack>