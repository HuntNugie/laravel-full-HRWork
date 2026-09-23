<x-wirekit::stack gap="md">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <x-wirekit::stack gap="sm">
            <div class="flex items-center gap-2">
                <a href="{{ route('work-management.master-projects') }}" wire:navigate class="text-sm text-[#30AFFF] hover:underline">Master Projects</a>
                <span class="text-sm text-slate-400">/ Detail</span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $masterProject->name }}</h1>
            <p class="max-w-3xl text-sm text-slate-500">{{ $masterProject->description ?: 'Tanpa deskripsi.' }}</p>
        </x-wirekit::stack>

        <div class="flex flex-wrap gap-2">
            @if ($masterProject->status === 'ready_for_review')
                @can('approve', $masterProject)
                    <x-wirekit::button
                        type="button"
                        class="bg-[#30AFFF] text-white hover:bg-[#1599E8]"
                        href="{{ route('work-management.master-projects.approve', $masterProject) }}"
                        wire:navigate
                    >
                        Final Approval GM
                    </x-wirekit::button>
                @endcan
            @endif

            @can('create', \App\Models\DivisionProject::class)
                <x-wirekit::button
                    type="button"
                    variant="outline"
                    href="{{ route('work-management.master-projects.division-projects.create', $masterProject) }}"
                    wire:navigate
                >
                    Tambah Division Project
                </x-wirekit::button>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $statusClass = match ($masterProject->status) {
                'completed' => 'bg-emerald-50 text-emerald-600',
                'ready_for_review' => 'bg-violet-50 text-violet-600',
                'in_progress' => 'bg-sky-50 text-sky-600',
                'rejected' => 'bg-rose-50 text-rose-600',
                default => 'bg-slate-100 text-slate-600',
            };
        @endphp

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    <span class="text-sm font-medium text-slate-500">Status</span>
                    <span class="inline-flex w-fit items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">{{ str_replace('_', ' ', $masterProject->status) }}</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Tanggal Mulai</span>
                    <span class="text-lg font-bold text-slate-900">{{ $masterProject->start_date?->format('d M Y') ?? '—' }}</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Deadline</span>
                    <span class="text-lg font-bold text-slate-900">{{ $masterProject->due_date?->format('d M Y') ?? '—' }}</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Division Project</span>
                    <span class="text-lg font-bold text-slate-900">{{ $masterProject->divisionProjects->count() }}</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Division Projects</h2>
                <p class="text-sm text-slate-500">Progress otomatis dihitung dari penyelesaian task di setiap division.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="space-y-4">
                @forelse ($masterProject->divisionProjects as $project)
                    @php
                        $projectStatusClass = match ($project->status) {
                            'completed' => 'bg-emerald-50 text-emerald-600',
                            'ready_for_review' => 'bg-violet-50 text-violet-600',
                            'in_progress' => 'bg-sky-50 text-sky-600',
                            'rejected' => 'bg-rose-50 text-rose-600',
                            default => 'bg-slate-100 text-slate-600',
                        };
                        $automaticProgress = $project->automaticProgress();
                    @endphp

                    <div class="rounded-xl border border-slate-100 p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <x-wirekit::stack gap="1">
                                <a href="{{ route('work-management.division-projects.show', $project) }}" wire:navigate class="text-sm font-semibold text-[#168ED1] hover:underline">
                                    {{ $project->name }}
                                </a>
                                <span class="text-xs text-slate-400">
                                    {{ $project->division?->name ?? '-' }} · Manager: {{ $project->manager?->user?->name ?? '-' }}
                                </span>
                            </x-wirekit::stack>

                            <span class="inline-flex w-fit items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $projectStatusClass }}">
                                {{ str_replace('_', ' ', $project->status) }}
                            </span>
                        </div>

                        @php
                            $latestProgress = $project->progressUpdates->sortByDesc('id')->first();
                            $managerReport = $project->reports
                                ->where('report_level', 'manager')
                                ->sortByDesc('id')
                                ->first();
                        @endphp

                        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
                            <div class="rounded-xl bg-slate-50 p-3">
                                <div class="flex items-center justify-between text-xs text-slate-500">
                                    <span>Automatic progress</span>
                                    <span class="font-semibold text-slate-700">{{ $automaticProgress }}%</span>
                                </div>
                                <div class="mt-2 h-2 rounded-full bg-slate-200">
                                    <div class="h-2 rounded-full bg-[#30AFFF]" style="width: {{ $automaticProgress }}%"></div>
                                </div>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-3">
                                <div class="flex items-center justify-between text-xs text-slate-500">
                                    <span>Manual progress</span>
                                    <span class="font-semibold text-slate-700">{{ $project->manual_progress }}%</span>
                                </div>
                                <div class="mt-2 h-2 rounded-full bg-slate-200">
                                    <div class="h-2 rounded-full bg-[#30AFFF]" style="width: {{ $project->manual_progress }}%"></div>
                                </div>
                                @if ($latestProgress)
                                    <p class="mt-2 text-xs leading-5 text-slate-600">{{ $latestProgress->note ?: 'Tidak ada catatan progress.' }}</p>
                                    <p class="mt-1 text-[11px] text-slate-400">
                                        {{ $latestProgress->reporter?->user?->name ?? '-' }} · {{ $latestProgress->created_at?->format('d M Y H:i') }}
                                    </p>
                                @else
                                    <p class="mt-2 text-xs text-slate-400">Belum ada catatan progress manual.</p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3 rounded-xl border border-slate-100 p-3">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Laporan Manager</div>
                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">
                                {{ $managerReport?->content ?? 'Belum ada laporan Manager ke GM.' }}
                            </p>
                            @if ($managerReport)
                                <div class="mt-1 text-[11px] text-slate-400">
                                    {{ $managerReport->reporter?->user?->name ?? '-' }} · status {{ $managerReport->status }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <p class="text-sm font-medium text-slate-700">Belum ada Division Project.</p>
                        <p class="mt-1 text-xs text-slate-400">Tambahkan division project untuk mulai membangun project hierarchy.</p>
                    </div>
                @endforelse
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>
</x-wirekit::stack>