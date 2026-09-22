<x-wirekit::stack gap="md">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <x-wirekit::stack gap="sm">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-[#30AFFF]">Work Management</span>
                <span class="text-sm text-slate-400">/ Tasks</span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tasks</h1>
            <p class="text-sm text-slate-500">Task yang berada dalam scope akses Anda.</p>
        </x-wirekit::stack>
    </div>

    @php
        $taskCards = [
            ['title' => 'Total Task', 'description' => 'Task dalam scope Anda', 'value' => $tasks->count(), 'icon' => 'check', 'class' => 'text-sky-600 bg-sky-50'],
            ['title' => 'In Progress', 'description' => 'Sedang dikerjakan', 'value' => $tasks->where('status', 'in_progress')->count(), 'icon' => 'clock', 'class' => 'text-amber-600 bg-amber-50'],
            ['title' => 'In Review', 'description' => 'Menunggu review', 'value' => $tasks->where('status', 'in_review')->count(), 'icon' => 'warning', 'class' => 'text-violet-600 bg-violet-50'],
            ['title' => 'Done', 'description' => 'Task selesai', 'value' => $tasks->where('status', 'done')->count(), 'icon' => 'calendar', 'class' => 'text-emerald-600 bg-emerald-50'],
        ];
    @endphp

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($taskCards as $card)
            <x-wirekit::card>
                <x-wirekit::card.body>
                    <x-wirekit::stack gap="sm">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-500">{{ $card['title'] }}</span>
                            <div class="flex size-9 items-center justify-center rounded-lg {{ $card['class'] }}">
                                <x-wirekit::icon name="{{ $card['icon'] }}" />
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-slate-900">{{ $card['value'] }}</p>
                        <p class="text-xs text-slate-500">{{ $card['description'] }}</p>
                    </x-wirekit::stack>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endforeach
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <div class="flex items-center justify-between gap-3">
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Daftar Task</h2>
                    <p class="text-sm text-slate-500">Task yang dapat Anda lihat dan tindak sesuai permission.</p>
                </x-wirekit::stack>
                <span class="text-sm text-slate-500">{{ $tasks->count() }} task</span>
            </div>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="wk-scrollbar overflow-x-auto">
                <x-wirekit::table hoverable>
                    <x-wirekit::table.head>
                        <x-wirekit::table.row>
                            <x-wirekit::table.th>Task</x-wirekit::table.th>
                            <x-wirekit::table.th>Project</x-wirekit::table.th>
                            <x-wirekit::table.th>Assignee</x-wirekit::table.th>
                            <x-wirekit::table.th>Progress</x-wirekit::table.th>
                            <x-wirekit::table.th>Status</x-wirekit::table.th>
                            <x-wirekit::table.th>Deadline</x-wirekit::table.th>
                            <x-wirekit::table.th align="right">Aksi</x-wirekit::table.th>
                        </x-wirekit::table.row>
                    </x-wirekit::table.head>

                    <x-wirekit::table.body>
                        @forelse ($tasks as $task)
                            @php
                                $statusClass = match ($task->status) {
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
                                    <x-wirekit::stack gap="1">
                                        <a href="{{ route('work-management.tasks.show', $task) }}" wire:navigate class="text-sm font-semibold text-[#168ED1] hover:underline">{{ $task->title }}</a>
                                        <span class="text-xs text-slate-400">{{ $task->divisionProject?->masterProject?->name ?? '—' }}</span>
                                    </x-wirekit::stack>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <span class="text-sm text-slate-700">{{ $task->divisionProject?->name ?? '—' }}</span>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <span class="text-sm text-slate-700">{{ $task->assignee?->user?->name ?? '—' }}</span>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <div class="min-w-28">
                                        <div class="mb-1 flex justify-between text-xs text-slate-500"><span>{{ $task->progress }}%</span></div>
                                        <div class="h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-[#30AFFF]" style="width: {{ $task->progress }}%"></div></div>
                                    </div>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">{{ str_replace('_', ' ', $task->status) }}</span>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <span class="text-sm text-slate-700">{{ $task->due_date?->format('d M Y') ?? '—' }}</span>
                                </x-wirekit::table.td>

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
                                <x-wirekit::table.td colspan="7">
                                    <div class="flex flex-col items-center justify-center py-12 text-center">
                                        <p class="text-sm font-medium text-slate-700">Belum ada task.</p>
                                        <p class="mt-1 text-xs text-slate-400">Task sesuai scope permission Anda akan tampil di sini.</p>
                                    </div>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @endforelse
                    </x-wirekit::table.body>
                </x-wirekit::table>
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>
</x-wirekit::stack>