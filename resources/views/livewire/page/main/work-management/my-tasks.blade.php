<x-wirekit::stack gap="md">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <x-wirekit::stack gap="sm">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-[#30AFFF]">Work Management</span>
                <span class="text-sm text-slate-400">/ Tasks</span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">My Tasks</h1>
            <p class="text-sm text-slate-500">Daftar task yang ditugaskan kepada Anda.</p>
        </x-wirekit::stack>
    </div>

    @php
        $pendingTasks = $tasks->filter(fn ($task) => !in_array($task->status, ['done', 'cancelled'], true));
        $doneTasks = $tasks->where('status', 'done');
    @endphp

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach ([
            ['title' => 'Total Task', 'value' => $pendingTasks->count() + $doneTasks->count(), 'description' => 'Task aktif dalam scope'],
            ['title' => 'Belum Selesai', 'value' => $pendingTasks->count(), 'description' => 'Masih perlu dikerjakan'],
            ['title' => 'Selesai', 'value' => $doneTasks->count(), 'description' => 'Sudah dicentang'],
        ] as $card)
            <x-wirekit::card>
                <x-wirekit::card.body>
                    <x-wirekit::stack gap="1">
                        <span class="text-sm font-medium text-slate-500">{{ $card['title'] }}</span>
                        <span class="text-2xl font-bold text-slate-900">{{ $card['value'] }}</span>
                        <span class="text-xs text-slate-500">{{ $card['description'] }}</span>
                    </x-wirekit::stack>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endforeach
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Daftar Task</h2>
                <p class="text-sm text-slate-500">Task Worker cukup mencentang checkbox pada task yang sudah selesai.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="wk-scrollbar overflow-x-auto">
                <x-wirekit::table hoverable>
                    <x-wirekit::table.head>
                        <x-wirekit::table.row>
                            <x-wirekit::table.th>Task</x-wirekit::table.th>
                            <x-wirekit::table.th>Project</x-wirekit::table.th>
                            <x-wirekit::table.th>Status</x-wirekit::table.th>
                            <x-wirekit::table.th>Deadline</x-wirekit::table.th>
                            <x-wirekit::table.th align="right">Aksi</x-wirekit::table.th>
                        </x-wirekit::table.row>
                    </x-wirekit::table.head>

                    <x-wirekit::table.body>
                        @forelse ($tasks as $task)
                            @php
                                $done = $task->status === 'done';
                                $cancelled = $task->status === 'cancelled';
                            @endphp

                            <x-wirekit::table.row>
                                <x-wirekit::table.td>
                                    <x-wirekit::stack gap="1">
                                        <a href="{{ route('work-management.tasks.show', $task) }}" wire:navigate
                                            class="text-sm font-semibold {{ $done ? 'text-emerald-700' : 'text-[#168ED1]' }} hover:underline">
                                            {{ $task->title }}
                                        </a>
                                        <span class="text-xs text-slate-400">
                                            {{ $task->divisionProject?->masterProject?->name ?? '—' }}
                                        </span>
                                    </x-wirekit::stack>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>{{ $task->divisionProject?->name ?? '—' }}</x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium
                                        {{ $done ? 'bg-emerald-50 text-emerald-600' : ($cancelled ? 'bg-rose-50 text-rose-600' : 'bg-sky-50 text-sky-600') }}">
                                        {{ $done ? 'Selesai' : ($cancelled ? 'Dibatalkan' : 'Belum selesai') }}
                                    </span>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>{{ $task->due_date?->format('d M Y') ?? '—' }}</x-wirekit::table.td>

                                <x-wirekit::table.td align="right">
                                    <x-wirekit::button
                                        type="button"
                                        variant="outline"
                                        class="px-3 py-1.5 text-xs"
                                        href="{{ route('work-management.tasks.show', $task) }}"
                                        wire:navigate
                                    >
                                        Detail
                                    </x-wirekit::button>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @empty
                            <x-wirekit::table.row>
                                <x-wirekit::table.td colspan="5">
                                    <div class="py-12 text-center text-sm text-slate-500">Belum ada task.</div>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @endforelse
                    </x-wirekit::table.body>
                </x-wirekit::table>
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>
</x-wirekit::stack>
