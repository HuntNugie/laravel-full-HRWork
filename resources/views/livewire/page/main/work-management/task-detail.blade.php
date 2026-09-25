<x-wirekit::stack gap="md">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <x-wirekit::stack gap="sm">
            <div class="flex items-center gap-2">
                <a href="{{ route('work-management.tasks') }}" wire:navigate
                    class="text-sm text-[#30AFFF] hover:underline">Tasks</a>
                <span class="text-sm text-slate-400">/ Detail</span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $task->title }}</h1>
            <p class="text-sm text-slate-500">
                {{ $task->divisionProject?->name ?? '-' }} · {{ $task->team?->name ?? '-' }} ·
                {{ $task->assignee?->user?->name ?? '-' }}
            </p>
        </x-wirekit::stack>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @php
        $isDone = $task->status === 'done';
        $isCancelled = $task->status === 'cancelled';
        $statusClass = match (true) {
            $isDone => 'bg-emerald-50 text-emerald-600',
            $isCancelled => 'bg-rose-50 text-rose-600',
            default => 'bg-sky-50 text-sky-600',
        };
        $statusLabel = $isDone ? 'Selesai' : ($isCancelled ? 'Dibatalkan' : 'Belum selesai');
    @endphp

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    <span class="text-sm font-medium text-slate-500">Status</span>
                    <span class="inline-flex w-fit items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Deadline</span>
                    <span class="text-lg font-bold text-slate-900">
                        {{ $task->due_date?->format('d M Y') ?? '—' }}
                    </span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Supervisor</span>
                    <span class="text-sm font-semibold text-slate-900">
                        {{ $task->team?->supervisor?->user?->name ?? '—' }}
                    </span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Task</h2>
                <p class="text-sm text-slate-500">
                    Satu checkbox menjadi penanda utama apakah pekerjaan sudah selesai.
                </p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="flex items-start gap-4 rounded-xl border border-slate-100 bg-slate-50 p-5">
                @can('updateOwn', $task)
                    @if (! $isCancelled)
                        <input
                            type="checkbox"
                            wire:click="toggleCompletion($event.target.checked)"
                            @checked($isDone)
                            class="mt-1 size-5 rounded border-slate-300 text-[#30AFFF] focus:ring-[#30AFFF]/20"
                        >
                    @endif
                @else
                    <span class="mt-1 flex size-5 items-center justify-center rounded border border-slate-300 {{ $isDone ? 'bg-emerald-500 border-emerald-500' : 'bg-white' }}">
                        @if ($isDone)
                            <x-wirekit::icon name="check" class="size-3 text-white" />
                        @endif
                    </span>
                @endcan

                <div class="min-w-0 flex-1">
                    <p class="text-base font-semibold {{ $isDone ? 'text-emerald-700 line-through' : 'text-slate-900' }}">
                        {{ $task->title }}
                    </p>

                    <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-600">
                        {{ $task->description ?: 'Tidak ada deskripsi task.' }}
                    </p>

                    <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-400">
                        <span>Team: {{ $task->team?->name ?? '—' }}</span>
                        <span>·</span>
                        <span>Employee: {{ $task->assignee?->user?->name ?? '—' }}</span>

                        @if ($task->completed_at)
                            <span>·</span>
                            <span>Selesai: {{ $task->completed_at->format('d M Y H:i') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            @can('updateOwn', $task)
                @if (! $isCancelled && in_array($task->divisionProject?->status, ['draft', 'in_progress', 'revision_required'], true))
                    <p class="mt-3 text-xs text-slate-400">
                        {{ $isDone ? 'Hapus centang untuk membuka kembali task.' : 'Centang checkbox setelah pekerjaan benar-benar selesai.' }}
                    </p>
                @elseif ($task->divisionProject?->status === 'submitted_to_gm')
                    <p class="mt-3 text-xs text-amber-600">
                        Division Project sedang menunggu approval GM. Task dikunci sampai ada keputusan.
                    </p>
                @elseif ($task->divisionProject?->status === 'completed')
                    <p class="mt-3 text-xs text-emerald-600">
                        Division Project sudah complete. Task tidak dapat diubah.
                    </p>
                @endif
            @endcan
        </x-wirekit::card.body>
    </x-wirekit::card>
</x-wirekit::stack>
