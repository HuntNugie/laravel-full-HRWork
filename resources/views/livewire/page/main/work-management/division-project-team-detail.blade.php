<x-wirekit::stack gap="md">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <x-wirekit::stack gap="sm">
            <div class="flex items-center gap-2">
                <a href="{{ route('work-management.division-projects.show', $divisionProject) }}" wire:navigate class="text-sm text-[#30AFFF] hover:underline">Division Project</a>
                <span class="text-sm text-slate-400">/ Team</span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $team->name }}</h1>
            <p class="text-sm text-slate-500">
                {{ $divisionProject->name }} · Supervisor: {{ $team->supervisor?->user?->name ?? '—' }}
            </p>
        </x-wirekit::stack>

        @can('create', \App\Models\Task::class)
            @if (in_array($divisionProject->status, ['draft', 'in_progress', 'revision_required'], true))
                <x-wirekit::button
                    href="{{ route('work-management.division-projects.tasks.create', ['divisionProject' => $divisionProject, 'team' => $team]) }}"
                    wire:navigate
                    class="bg-[#30AFFF] text-white hover:bg-[#1599E8]"
                >
                    Buat Task
                </x-wirekit::button>
            @endif
        @endcan
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @php
            $taskCounts = [
                'done' => $tasks->where('status', 'done')->count(),
                'in_progress' => $tasks->where('status', 'in_progress')->count(),
                'in_review' => $tasks->where('status', 'in_review')->count(),
                'blocked' => $tasks->where('status', 'blocked')->count(),
            ];
        @endphp

        <x-wirekit::card><x-wirekit::card.body><x-wirekit::stack gap="1">
            <span class="text-sm font-medium text-slate-500">Progress Team</span>
            <span class="text-lg font-bold text-slate-900">{{ $this->automaticProgress }}%</span>
        </x-wirekit::stack></x-wirekit::card.body></x-wirekit::card>

        <x-wirekit::card><x-wirekit::card.body><x-wirekit::stack gap="1">
            <span class="text-sm font-medium text-slate-500">Total Task</span>
            <span class="text-lg font-bold text-slate-900">{{ $tasks->count() }}</span>
        </x-wirekit::stack></x-wirekit::card.body></x-wirekit::card>

        @foreach ($taskCounts as $status => $count)
            <x-wirekit::card><x-wirekit::card.body><x-wirekit::stack gap="1">
                <span class="text-sm font-medium text-slate-500">{{ str_replace('_', ' ', ucfirst($status)) }}</span>
                <span class="text-lg font-bold text-slate-900">{{ $count }}</span>
            </x-wirekit::stack></x-wirekit::card.body></x-wirekit::card>
        @endforeach
    </div>

    @can('submitSupervisorReport', $divisionProject)
        @if ((int) $team->supervisor_id === (int) auth()->user()?->employees?->id && in_array($divisionProject->status, ['ready_for_review', 'revision_required'], true))
            <x-wirekit::card>
                <x-wirekit::card.header>
                    <x-wirekit::stack gap="1">
                        <h2 class="text-lg font-semibold text-slate-900">Laporan Supervisor</h2>
                        <p class="text-sm text-slate-500">Kirim hasil pekerjaan {{ $team->name }} kepada Manager.</p>
                    </x-wirekit::stack>
                </x-wirekit::card.header>
                <x-wirekit::card.body>
                    <form wire:submit="submitSupervisorReport" class="space-y-4">
                        <textarea
                            wire:model="supervisorReport"
                            rows="6"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20"
                            placeholder="Ringkasan hasil pekerjaan Team, kendala, dan catatan penting."
                        ></textarea>
                        @error('supervisorReport') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                        <x-wirekit::button type="submit" class="bg-[#30AFFF] text-white hover:bg-[#1599E8]">Kirim ke Manager</x-wirekit::button>
                    </form>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endif
    @endcan

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Perkembangan {{ $team->name }}</h2>
                <p class="text-sm text-slate-500">Semua task pada Team ini untuk Division Project yang sedang dibuka.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>
        <x-wirekit::card.body>
            <div class="wk-scrollbar overflow-x-auto">
                <x-wirekit::table hoverable>
                    <x-wirekit::table.head>
                        <x-wirekit::table.row>
                            <x-wirekit::table.th>Task</x-wirekit::table.th>
                            <x-wirekit::table.th>Employee</x-wirekit::table.th>
                            <x-wirekit::table.th>Progress</x-wirekit::table.th>
                            <x-wirekit::table.th>Status</x-wirekit::table.th>
                            <x-wirekit::table.th>Deadline</x-wirekit::table.th>
                        </x-wirekit::table.row>
                    </x-wirekit::table.head>
                    <x-wirekit::table.body>
                        @forelse ($tasks as $task)
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
                                <x-wirekit::table.td><span class="text-sm text-slate-700">{{ $task->assignee?->user?->name ?? '—' }}</span></x-wirekit::table.td>
                                <x-wirekit::table.td><span class="text-sm font-medium text-slate-700">{{ $task->progress }}%</span></x-wirekit::table.td>
                                <x-wirekit::table.td><span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $taskStatusClass }}">{{ str_replace('_', ' ', $task->status) }}</span></x-wirekit::table.td>
                                <x-wirekit::table.td><span class="text-sm text-slate-700">{{ $task->due_date?->format('d M Y') ?? '—' }}</span></x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @empty
                            <x-wirekit::table.row>
                                <x-wirekit::table.td colspan="5"><div class="py-10 text-center text-sm text-slate-500">Belum ada task untuk Team ini.</div></x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @endforelse
                    </x-wirekit::table.body>
                </x-wirekit::table>
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Laporan Supervisor</h2>
                <p class="text-sm text-slate-500">Laporan terbaru dari Supervisor Team ini.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>
        <x-wirekit::card.body>
            @if ($latestSupervisorReport)
                <div class="space-y-3">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <span class="inline-flex w-fit items-center rounded-full px-2.5 py-1 text-xs font-medium
                            {{ match ($latestSupervisorReport->status) {
                                'approved' => 'bg-emerald-50 text-emerald-600',
                                'rejected' => 'bg-rose-50 text-rose-600',
                                default => 'bg-violet-50 text-violet-600',
                            } }}">
                            {{ str_replace('_', ' ', $latestSupervisorReport->status) }}
                        </span>
                        <span class="text-xs text-slate-400">{{ $latestSupervisorReport->created_at?->format('d M Y H:i') }}</span>
                    </div>
                    <p class="whitespace-pre-line text-sm leading-6 text-slate-700">{{ $latestSupervisorReport->content }}</p>
                </div>
            @else
                <div class="py-8 text-center text-sm text-slate-500">Belum ada laporan Supervisor.</div>
            @endif
        </x-wirekit::card.body>
    </x-wirekit::card>
</x-wirekit::stack>
