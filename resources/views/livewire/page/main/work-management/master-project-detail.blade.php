<x-wirekit::stack gap="md">
    @php
        $completedDivisions = $divisionProjects->where('status', 'completed')->count();
        $totalDivisions = $divisionProjects->count();
        $submittedDivisions = $divisionProjects->where('status', 'submitted_to_gm')->count();
        $revisionDivisions = $divisionProjects->where('status', 'revision_required')->count();
        $allComplete = $totalDivisions > 0 && $completedDivisions === $totalDivisions;

        $statusClass = match ($masterProject->status) {
            'completed' => 'bg-emerald-50 text-emerald-600',
            'in_progress', 'ready_for_review' => 'bg-sky-50 text-sky-600',
            default => 'bg-slate-100 text-slate-600',
        };
    @endphp

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <x-wirekit::stack gap="sm">
            <div class="flex items-center gap-2">
                <a href="{{ route('work-management.master-projects') }}" wire:navigate
                    class="text-sm text-[#30AFFF] hover:underline">Master Projects</a>
                <span class="text-sm text-slate-400">/ Detail</span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $masterProject->name }}</h1>
            <p class="text-sm text-slate-500">
                {{ $masterProject->description ?: 'Tidak ada deskripsi project.' }}
            </p>
        </x-wirekit::stack>

        <div class="flex flex-wrap gap-2">
            @if ($masterProject->status === 'completed' && auth()->user()?->hasRole('general-manager'))
                <x-wirekit::button
                    type="button"
                    href="{{ route('work-management.master-projects.summary-report', $masterProject) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <x-wirekit::icon name="printer" />
                    Cetak Rekap Master Project
                </x-wirekit::button>
            @endif

            @can('create-division-project')
                @if ($masterProject->status !== 'completed')
                    <x-wirekit::button
                        href="{{ route('work-management.master-projects.division-projects.create', $masterProject) }}"
                        wire:navigate
                        class="bg-[#30AFFF] text-white hover:bg-[#1599E8]"
                    >
                        Buat Division Project
                    </x-wirekit::button>
                @endif
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Status Project</span>
                    <span class="inline-flex w-fit items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">
                        {{ $masterProject->status === 'completed' ? 'Selesai' : ($masterProject->status === 'in_progress' ? 'Berjalan' : 'Draft') }}
                    </span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Division Complete</span>
                    <span class="text-lg font-bold text-slate-900">{{ $completedDivisions }} / {{ $totalDivisions }}</span>
                    <span class="text-xs text-slate-400">Division sudah ditandai complete</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Menunggu GM</span>
                    <span class="text-lg font-bold text-slate-900">{{ $submittedDivisions }}</span>
                    <span class="text-xs text-slate-400">Menunggu approve / revisi</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Perlu Revisi</span>
                    <span class="text-lg font-bold text-amber-700">{{ $revisionDivisions }}</span>
                    <span class="text-xs text-slate-400">Perlu pekerjaan lanjutan</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Alur Project</h2>
                <p class="text-sm text-slate-500">
                    Task diselesaikan Employee → Manager mengajukan Division Project → GM approve atau minta revisi → semua Division Project complete → GM selesaikan Master Project.
                </p>
            </x-wirekit::stack>
        </x-wirekit::card.header>
        <x-wirekit::card.body>
            <div class="grid grid-cols-1 gap-3 md:grid-cols-5">
                @foreach ([
                    ['n' => '1', 't' => 'Task', 'd' => 'Supervisor membuat task dan Employee menyelesaikannya dengan checkbox.'],
                    ['n' => '2', 't' => 'Manager', 'd' => 'Manager memonitor Team dan mengajukan Division Project.'],
                    ['n' => '3', 't' => 'GM Review', 'd' => 'GM menyetujui Division Project atau meminta revisi.'],
                    ['n' => '4', 't' => 'Division Complete', 'd' => 'Division yang disetujui menjadi complete.'],
                    ['n' => '5', 't' => 'Project Complete', 'd' => 'GM menandai Master Project selesai setelah semua division complete.'],
                ] as $step)
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                        <span class="inline-flex size-7 items-center justify-center rounded-full bg-sky-100 text-xs font-bold text-sky-700">{{ $step['n'] }}</span>
                        <p class="mt-3 text-sm font-semibold text-slate-800">{{ $step['t'] }}</p>
                        <p class="mt-1 text-xs leading-5 text-slate-500">{{ $step['d'] }}</p>
                    </div>
                @endforeach
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <div class="flex items-center justify-between gap-3">
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Division Projects</h2>
                    <p class="text-sm text-slate-500">Status penyelesaian setiap Division Project.</p>
                </x-wirekit::stack>
                <span class="text-sm text-slate-500">{{ $totalDivisions }} division</span>
            </div>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="space-y-3">
                @forelse ($divisionProjects as $project)
                    @php
                        $activeTasks = $project->tasks->reject(fn ($task) => $task->status === 'cancelled');
                        $doneTasks = $activeTasks->where('status', 'done')->count();
                        $taskTotal = $activeTasks->count();

                        $projectStatusClass = match ($project->status) {
                            'completed' => 'bg-emerald-50 text-emerald-600',
                            'submitted_to_gm' => 'bg-violet-50 text-violet-600',
                            'revision_required' => 'bg-amber-50 text-amber-600',
                            default => 'bg-sky-50 text-sky-600',
                        };
                    @endphp

                    <div class="rounded-xl border border-slate-100 p-4">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <a href="{{ route('work-management.division-projects.show', $project) }}"
                                    wire:navigate class="text-sm font-semibold text-[#168ED1] hover:underline">
                                    {{ $project->name }}
                                </a>
                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $project->division?->name ?? '-' }} · Manager: {{ $project->manager?->user?->name ?? '-' }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="inline-flex w-fit items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $projectStatusClass }}">
                                    @switch($project->status)
                                        @case('submitted_to_gm') Menunggu Approval GM @break
                                        @case('revision_required') Perlu Revisi @break
                                        @case('completed') Division Complete @break
                                        @default Berjalan
                                    @endswitch
                                </span>

                                <x-wirekit::button variant="outline" size="sm"
                                    href="{{ route('work-management.division-projects.show', $project) }}"
                                    wire:navigate>
                                    Detail
                                </x-wirekit::button>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-400">Task selesai</p>
                                <p class="mt-1 text-lg font-bold text-slate-900">{{ $doneTasks }} / {{ $taskTotal }}</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-400">Team</p>
                                <p class="mt-1 text-lg font-bold text-slate-900">{{ $project->teams->count() }}</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-400">Deadline</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900">{{ $project->due_date?->format('d M Y') ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center text-sm text-slate-500">Belum ada Division Project.</div>
                @endforelse
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

    @can('approve', $masterProject)
        @if ($masterProject->status !== 'completed')
            <x-wirekit::card>
                <x-wirekit::card.body>
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">
                                {{ $allComplete ? 'Semua Division Project sudah complete.' : 'Master Project belum dapat diselesaikan.' }}
                            </p>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $allComplete
                                    ? 'GM dapat membuka halaman finalisasi untuk menandai project selesai.'
                                    : 'Selesaikan seluruh Division Project terlebih dahulu.' }}
                            </p>
                        </div>

                        <x-wirekit::button
                            variant="outline"
                            href="{{ route('work-management.master-projects.approve', $masterProject) }}"
                            wire:navigate
                            :disabled="!$allComplete"
                        >
                            Finalisasi Project
                        </x-wirekit::button>
                    </div>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @else
            <x-wirekit::card>
                <x-wirekit::card.body>
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-700">
                        Master Project ini sudah ditandai selesai oleh General Manager.
                    </div>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endif
    @endcan
</x-wirekit::stack>
