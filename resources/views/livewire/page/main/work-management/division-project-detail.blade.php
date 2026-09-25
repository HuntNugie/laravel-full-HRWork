<x-wirekit::stack gap="md">
    @php
        $statusLabels = [
            'draft' => 'Draft',
            'in_progress' => 'Berjalan',
            'ready_for_review' => 'Siap Diajukan',
            'manager_approved' => 'Siap Diajukan',
            'submitted_to_gm' => 'Menunggu Approval GM',
            'revision_required' => 'Perlu Revisi',
            'completed' => 'Complete',
        ];

        $statusClass = match ($divisionProject->status) {
            'completed' => 'bg-emerald-50 text-emerald-600',
            'submitted_to_gm' => 'bg-violet-50 text-violet-600',
            'revision_required' => 'bg-amber-50 text-amber-600',
            default => 'bg-sky-50 text-sky-600',
        };

        $activeTasks = $divisionProject->tasks->reject(
            fn ($task) => $task->status === 'cancelled'
        );

        $completedTasks = $activeTasks->where('status', 'done')->count();
        $totalTasks = $activeTasks->count();
        $allTasksDone = $totalTasks > 0 && $completedTasks === $totalTasks;
    @endphp

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <x-wirekit::stack gap="sm">
            <div class="flex items-center gap-2">
                <a href="{{ route('work-management.master-projects.show', $divisionProject->masterProject) }}"
                    wire:navigate class="text-sm text-[#30AFFF] hover:underline">Master Project</a>
                <span class="text-sm text-slate-400">/ Division Project</span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $divisionProject->name }}</h1>
            <p class="text-sm text-slate-500">
                {{ $divisionProject->division?->name ?? '-' }} · Manager:
                {{ $divisionProject->manager?->user?->name ?? '-' }}
            </p>
        </x-wirekit::stack>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-rose-100 bg-rose-50 p-3 text-sm text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    <span class="text-sm font-medium text-slate-500">Status</span>
                    <span class="inline-flex w-fit items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">
                        {{ $statusLabels[$divisionProject->status] ?? ucfirst(str_replace('_', ' ', $divisionProject->status)) }}
                    </span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Progress Task</span>
                    <span class="text-lg font-bold text-slate-900">{{ $completedTasks }} / {{ $totalTasks }}</span>
                    <span class="text-xs text-slate-400">Task selesai</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Team</span>
                    <span class="text-lg font-bold text-slate-900">{{ $divisionProject->teams->count() }}</span>
                    <span class="text-xs text-slate-400">Team terpilih</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Deadline</span>
                    <span class="text-lg font-bold text-slate-900">
                        {{ $divisionProject->due_date?->format('d M Y') ?? '—' }}
                    </span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
    </div>

    @can('assignTeam', $divisionProject)
        @if (in_array($divisionProject->status, ['draft', 'in_progress', 'revision_required'], true) && $availableTeams->isNotEmpty())
            <x-wirekit::card>
                <x-wirekit::card.header>
                    <x-wirekit::stack gap="1">
                        <h2 class="text-lg font-semibold text-slate-900">Pilih Team</h2>
                        <p class="text-sm text-slate-500">
                            GM dapat menyiapkan Team saat awal. Manager dapat menambahkan Team lain selama project berjalan.
                        </p>
                    </x-wirekit::stack>
                </x-wirekit::card.header>

                <x-wirekit::card.body>
                    <form wire:submit="assignTeam">
                        <div class="flex flex-col gap-3 md:flex-row md:items-end">
                            <div class="flex-1">
                                <label class="mb-2 block text-sm font-medium text-slate-700">Team</label>
                                <select wire:model="team_id"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20">
                                    <option value="">Pilih team</option>
                                    @foreach ($availableTeams as $team)
                                        <option value="{{ $team->id }}">
                                            {{ $team->name }} · Supervisor: {{ $team->supervisor?->user?->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('team_id')
                                    <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <x-wirekit::button type="submit" class="bg-[#30AFFF] text-white hover:bg-[#1599E8]">
                                Tambahkan Team
                            </x-wirekit::button>
                        </div>
                    </form>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endif
    @endcan

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Team pada Division Project</h2>
                <p class="text-sm text-slate-500">Anggota otomatis mengikuti Team yang dipilih.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="space-y-3">
                @forelse ($divisionProject->teams as $team)
                    <div class="rounded-xl border border-slate-100 p-4">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <a href="{{ route('work-management.division-projects.teams.show', ['divisionProject' => $divisionProject, 'team' => $team]) }}"
                                    wire:navigate class="text-sm font-semibold text-[#168ED1] hover:underline">
                                    {{ $team->name }}
                                </a>
                                <p class="mt-1 text-xs text-slate-400">
                                    Supervisor: {{ $team->supervisor?->user?->name ?? '-' }}
                                    · {{ $team->employees->count() }} anggota
                                </p>
                            </div>
                            <x-wirekit::button
                                variant="outline"
                                size="sm"
                                href="{{ route('work-management.division-projects.teams.show', ['divisionProject' => $divisionProject, 'team' => $team]) }}"
                                wire:navigate
                            >
                                Lihat Board
                            </x-wirekit::button>
                        </div>

                        @if ($team->employees->isNotEmpty())
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($team->employees as $member)
                                    <span class="rounded-full bg-slate-50 px-3 py-1 text-xs text-slate-600">
                                        {{ $member->user?->name ?? '-' }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-10 text-center text-sm text-slate-500">
                        Belum ada Team yang dipilih.
                    </div>
                @endforelse
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <div class="flex items-center justify-between gap-3">
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Task</h2>
                    <p class="text-sm text-slate-500">Penyelesaian Division Project ditentukan dari task yang selesai.</p>
                </x-wirekit::stack>

                <span class="text-sm text-slate-500">{{ $completedTasks }} / {{ $totalTasks }} selesai</span>
            </div>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="wk-scrollbar overflow-x-auto">
                <x-wirekit::table hoverable>
                    <x-wirekit::table.head>
                        <x-wirekit::table.row>
                            <x-wirekit::table.th>Task</x-wirekit::table.th>
                            <x-wirekit::table.th>Team</x-wirekit::table.th>
                            <x-wirekit::table.th>Employee</x-wirekit::table.th>
                            <x-wirekit::table.th>Status</x-wirekit::table.th>
                            <x-wirekit::table.th>Deadline</x-wirekit::table.th>
                        </x-wirekit::table.row>
                    </x-wirekit::table.head>

                    <x-wirekit::table.body>
                        @forelse ($divisionProject->tasks as $task)
                            @php
                                $done = $task->status === 'done';
                                $cancelled = $task->status === 'cancelled';
                                $taskClass = match (true) {
                                    $done => 'bg-emerald-50 text-emerald-600',
                                    $cancelled => 'bg-rose-50 text-rose-600',
                                    default => 'bg-sky-50 text-sky-600',
                                };
                            @endphp

                            <x-wirekit::table.row>
                                <x-wirekit::table.td>
                                    <a href="{{ route('work-management.tasks.show', $task) }}" wire:navigate
                                        class="text-sm font-semibold text-[#168ED1] hover:underline">
                                        {{ $task->title }}
                                    </a>
                                </x-wirekit::table.td>
                                <x-wirekit::table.td>{{ $task->team?->name ?? '-' }}</x-wirekit::table.td>
                                <x-wirekit::table.td>{{ $task->assignee?->user?->name ?? '-' }}</x-wirekit::table.td>
                                <x-wirekit::table.td>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $taskClass }}">
                                        {{ $done ? 'Selesai' : ($cancelled ? 'Dibatalkan' : 'Belum selesai') }}
                                    </span>
                                </x-wirekit::table.td>
                                <x-wirekit::table.td>{{ $task->due_date?->format('d M Y') ?? '—' }}</x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @empty
                            <x-wirekit::table.row>
                                <x-wirekit::table.td colspan="5">
                                    <div class="py-10 text-center text-sm text-slate-500">Belum ada task.</div>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @endforelse
                    </x-wirekit::table.body>
                </x-wirekit::table>
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

    @can('submitForCompletion', $divisionProject)
        @if ($allTasksDone && !in_array($divisionProject->status, ['submitted_to_gm', 'completed'], true))
            <x-wirekit::card>
                <x-wirekit::card.body>
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Semua task sudah selesai.</p>
                            <p class="mt-1 text-sm text-slate-500">
                                Manager dapat mengajukan Division Project ini kepada GM.
                            </p>
                        </div>

                        <x-wirekit::button type="button"
                            wire:click="submitForCompletion"
                            class="bg-[#30AFFF] text-white hover:bg-[#1599E8]">
                            Ajukan Selesai ke GM
                        </x-wirekit::button>
                    </div>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endif
    @endcan

    @can('reviewCompletion', $divisionProject)
        @if ($divisionProject->status === 'submitted_to_gm')
            <x-wirekit::card>
                <x-wirekit::card.header>
                    <x-wirekit::stack gap="1">
                        <h2 class="text-lg font-semibold text-slate-900">Review General Manager</h2>
                        <p class="text-sm text-slate-500">
                            Tentukan apakah Division Project dapat ditandai complete atau perlu revisi.
                        </p>
                    </x-wirekit::stack>
                </x-wirekit::card.header>

                <x-wirekit::card.body>
                    <div class="flex flex-wrap gap-2">
                        <x-wirekit::button type="button"
                            wire:click="approveCompletion"
                            class="bg-[#30AFFF] text-white hover:bg-[#1599E8]">
                            Tandai Division Complete
                        </x-wirekit::button>

                        <x-wirekit::button type="button" variant="outline"
                            wire:click="$set('reviewFeedback', '')">
                            Isi Feedback Revisi
                        </x-wirekit::button>
                    </div>

                    <div class="mt-4">
                        <label class="mb-2 block text-sm font-medium text-slate-700">Feedback Revisi</label>
                        <textarea wire:model="reviewFeedback" rows="4"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20"
                            placeholder="Wajib diisi ketika meminta revisi."></textarea>
                        @error('reviewFeedback')
                            <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>
                        @enderror

                        <x-wirekit::button type="button" variant="outline" intent="danger"
                            class="mt-3"
                            wire:click="requestRevision">
                            Minta Revisi
                        </x-wirekit::button>
                    </div>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @elseif ($divisionProject->status === 'completed')
            <x-wirekit::card>
                <x-wirekit::card.body>
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-emerald-700">Division Project sudah complete.</p>
                            <p class="mt-1 text-sm text-slate-500">
                                GM masih dapat membuka kembali project ini untuk revisi bila diperlukan.
                            </p>
                        </div>

                    </div>

                    <div class="mt-4">
                        <textarea wire:model="reviewFeedback" rows="3"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20"
                            placeholder="Jelaskan alasan membuka kembali Division Project."></textarea>
                        <x-wirekit::button type="button" variant="outline" intent="danger"
                            class="mt-2"
                            wire:click="requestRevision">
                            Buka Revisi Division
                        </x-wirekit::button>
                    </div>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endif
    @endcan

    @if ($divisionProject->status === 'revision_required')
        <x-wirekit::card>
            <x-wirekit::card.body>
                <div class="flex items-start gap-3">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-amber-50">
                        <x-wirekit::icon name="warning" class="size-5 text-amber-600" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Division Project perlu direvisi.</p>
                        <p class="mt-1 text-sm text-slate-500">
                            Manager dapat menambah/mengubah Team dan Supervisor dapat membuat task revisi. Setelah semua task selesai, Manager dapat mengajukan kembali ke GM.
                        </p>
                    </div>
                </div>
            </x-wirekit::card.body>
        </x-wirekit::card>
    @endif
</x-wirekit::stack>
