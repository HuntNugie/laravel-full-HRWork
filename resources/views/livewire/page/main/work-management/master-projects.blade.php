<x-wirekit::stack gap="md">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <x-wirekit::stack gap="sm">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-[#30AFFF]">Work Management</span>
                <span class="text-sm text-slate-400">/ Master Projects</span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Master Projects
            </h1>

            <p class="text-sm text-slate-500">
                Kelola project utama perusahaan dan pantau penyelesaian setiap division project.
            </p>
        </x-wirekit::stack>

        @can('create-master-project')
            <a href="{{ route('work-management.master-projects.create') }}" wire:navigate>
                <x-wirekit::button type="button" class="bg-[#30AFFF] text-white hover:bg-[#1599E8]">
                    Buat Master Project
                </x-wirekit::button>
            </a>
        @endcan
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $cards = [
                ['title' => 'Total Project', 'description' => 'Project dalam scope Anda', 'value' => $projects->count(), 'icon' => 'check', 'class' => 'text-sky-600 bg-sky-50'],
                ['title' => 'Berjalan', 'description' => 'Project aktif', 'value' => $projects->where('status', 'in_progress')->count(), 'icon' => 'clock', 'class' => 'text-amber-600 bg-amber-50'],
                ['title' => 'Menunggu Diselesaikan', 'description' => 'Semua division harus complete', 'value' => $projects->filter(fn ($project) => $project->status !== 'completed' && $project->divisionProjects->isNotEmpty() && $project->divisionProjects->every(fn ($division) => $division->status === 'completed'))->count(), 'icon' => 'warning', 'class' => 'text-violet-600 bg-violet-50'],
                ['title' => 'Selesai', 'description' => 'Project complete', 'value' => $projects->where('status', 'completed')->count(), 'icon' => 'calendar', 'class' => 'text-emerald-600 bg-emerald-50'],
            ];
        @endphp

        @foreach ($cards as $card)
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
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Daftar Master Project</h2>
                    <p class="text-sm text-slate-500">Project utama sesuai scope akses akun Anda.</p>
                </x-wirekit::stack>
                <span class="text-sm text-slate-500">{{ $projects->count() }} project</span>
            </div>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="wk-scrollbar overflow-x-auto">
                <x-wirekit::table hoverable>
                    <x-wirekit::table.head>
                        <x-wirekit::table.row>
                            <x-wirekit::table.th>Project</x-wirekit::table.th>
                            <x-wirekit::table.th>Status</x-wirekit::table.th>
                            <x-wirekit::table.th>Division Project</x-wirekit::table.th>
                            <x-wirekit::table.th>Deadline</x-wirekit::table.th>
                            <x-wirekit::table.th align="right">Aksi</x-wirekit::table.th>
                        </x-wirekit::table.row>
                    </x-wirekit::table.head>

                    <x-wirekit::table.body>
                        @forelse ($projects as $project)
                            @php
                                $status = $project->status;
                                $statusClass = match ($status) {
                                    'completed' => 'bg-emerald-50 text-emerald-600',
                                    'in_progress', 'ready_for_review' => 'bg-sky-50 text-sky-600',
                                    'rejected' => 'bg-rose-50 text-rose-600',
                                    default => 'bg-slate-100 text-slate-600',
                                };
                            @endphp

                            <x-wirekit::table.row>
                                <x-wirekit::table.td>
                                    <x-wirekit::stack gap="1">
                                        <a href="{{ route('work-management.master-projects.show', $project) }}" wire:navigate class="text-sm font-semibold text-[#168ED1] hover:underline">
                                            {{ $project->name }}
                                        </a>
                                        <span class="text-xs text-slate-400">
                                            Dibuat oleh {{ $project->creator?->user?->name ?? '-' }}
                                        </span>
                                    </x-wirekit::stack>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">
                                        {{ str_replace('_', ' ', $status) }}
                                    </span>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <span class="text-sm text-slate-700">{{ $project->divisionProjects->count() }} division</span>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <span class="text-sm text-slate-700">{{ $project->due_date?->format('d M Y') ?? '—' }}</span>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td align="right">
                                    <x-wirekit::button
                                        type="button"
                                        variant="outline"
                                        class="px-3 py-1.5 text-xs"
                                        href="{{ route('work-management.master-projects.show', $project) }}"
                                        wire:navigate
                                    >
                                        Detail
                                    </x-wirekit::button>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @empty
                            <x-wirekit::table.row>
                                <x-wirekit::table.td colspan="5">
                                    <div class="flex flex-col items-center justify-center py-12 text-center">
                                        <p class="text-sm font-medium text-slate-700">Belum ada Master Project.</p>
                                        <p class="mt-1 text-xs text-slate-400">Project yang sesuai scope akses akan tampil di sini.</p>
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