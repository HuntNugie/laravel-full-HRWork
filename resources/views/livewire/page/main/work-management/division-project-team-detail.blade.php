<x-wirekit::stack gap="md">
    @php
        $canModifyTasks = in_array($divisionProject->status, ['draft', 'in_progress', 'revision_required'], true);
        $todoTasks = $tasks->reject(fn ($task) => in_array($task->status, ['done', 'cancelled'], true));
        $doneTasks = $tasks->where('status', 'done');
        $activeTasks = $tasks->reject(fn ($task) => $task->status === 'cancelled');
    @endphp

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <x-wirekit::stack gap="sm">
            <div class="flex items-center gap-2">
                <a href="{{ route('work-management.division-projects.show', $divisionProject) }}" wire:navigate
                    class="text-sm text-[#30AFFF] hover:underline">Division Project</a>
                <span class="text-sm text-slate-400">/ Team Board</span>
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

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Anggota Team</span>
                    <span class="text-lg font-bold text-slate-900">{{ $members->count() }}</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Total Task</span>
                    <span class="text-lg font-bold text-slate-900">{{ $activeTasks->count() }}</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Belum Selesai</span>
                    <span class="text-lg font-bold text-slate-900">{{ $todoTasks->count() }}</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Selesai</span>
                    <span class="text-lg font-bold text-emerald-700">{{ $doneTasks->count() }}</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Anggota Team</h2>
                <p class="text-sm text-slate-500">Anggota berasal dari Team yang terpilih pada organisasi.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="flex flex-wrap gap-2">
                @forelse ($members as $member)
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-2">
                        <p class="text-sm font-medium text-slate-700">{{ $member->user?->name ?? '-' }}</p>
                        <p class="mt-0.5 text-xs text-slate-400">{{ $member->employee_code }}</p>
                    </div>
                @empty
                    <span class="text-sm text-slate-500">Belum ada anggota aktif.</span>
                @endforelse
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Task Board</h2>
                <p class="text-sm text-slate-500">Board sederhana untuk memantau task Team seperti kanban lite.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="rounded-xl bg-slate-50 p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-800">Perlu Dikerjakan</h3>
                        <span class="rounded-full bg-white px-2.5 py-1 text-xs text-slate-500">{{ $todoTasks->count() }}</span>
                    </div>

                    <div class="space-y-3">
                        @forelse ($todoTasks as $task)
                            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <a href="{{ route('work-management.tasks.show', $task) }}" wire:navigate
                                            class="text-sm font-semibold text-[#168ED1] hover:underline">
                                            {{ $task->title }}
                                        </a>
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $task->assignee?->user?->name ?? 'Belum ada assignee' }}
                                            · Deadline {{ $task->due_date?->format('d M Y') ?? '—' }}
                                        </p>
                                    </div>

                                    @if ($canModifyTasks)
                                        @can('updateOwn', $task)
                                            <button
                                                type="button"
                                                wire:click="toggleTask({{ $task->id }}, true)"
                                                class="flex size-8 shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-400 hover:border-[#30AFFF] hover:text-[#30AFFF]"
                                                title="Tandai selesai"
                                            >
                                                <x-wirekit::icon name="check" class="size-4" />
                                            </button>
                                        @endcan
                                    @endif
                                </div>

                                <p class="mt-3 text-xs leading-5 text-slate-500">
                                    {{ $task->description ?: 'Tidak ada deskripsi.' }}
                                </p>
                            </div>
                        @empty
                            <div class="py-8 text-center text-sm text-slate-500">Tidak ada task yang sedang dikerjakan.</div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-xl bg-emerald-50/50 p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-800">Selesai</h3>
                        <span class="rounded-full bg-white px-2.5 py-1 text-xs text-emerald-600">{{ $doneTasks->count() }}</span>
                    </div>

                    <div class="space-y-3">
                        @forelse ($doneTasks as $task)
                            <div class="rounded-xl border border-emerald-100 bg-white p-4">
                                <div class="flex items-start gap-3">
                                    @if ($canModifyTasks)
                                        @can('updateOwn', $task)
                                            <button
                                                type="button"
                                                wire:click="toggleTask({{ $task->id }}, false)"
                                                class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded border border-emerald-500 bg-emerald-500 text-white"
                                                title="Buka kembali task"
                                            >
                                                <x-wirekit::icon name="check" class="size-3" />
                                            </button>
                                        @endcan
                                    @else
                                        <span class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded border border-emerald-500 bg-emerald-500 text-white">
                                            <x-wirekit::icon name="check" class="size-3" />
                                        </span>
                                    @endif

                                    <div class="min-w-0">
                                        <a href="{{ route('work-management.tasks.show', $task) }}" wire:navigate
                                            class="text-sm font-semibold text-emerald-700 hover:underline">
                                            {{ $task->title }}
                                        </a>
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $task->assignee?->user?->name ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-sm text-slate-500">Belum ada task selesai.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>
</x-wirekit::stack>
