<x-wirekit::stack gap="md">
    @php
        $totalDivisions = $masterProject->divisionProjects->count();
        $completedDivisions = $masterProject->divisionProjects->where('status', 'completed')->count();
        $allComplete = $totalDivisions > 0 && $completedDivisions === $totalDivisions;
    @endphp

    <div>
        <div class="mb-2 flex items-center gap-2">
            <a href="{{ route('work-management.master-projects.show', $masterProject) }}" wire:navigate
                class="text-sm text-[#30AFFF] hover:underline">Master Project</a>
            <span class="text-sm text-slate-400">/ Finalisasi</span>
        </div>

        <x-wirekit::stack gap="1">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Finalisasi Master Project</h1>
            <p class="text-sm text-slate-500">
                Tandai project selesai setelah seluruh Division Project berstatus complete.
            </p>
        </x-wirekit::stack>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <x-wirekit::card>
        <x-wirekit::card.header>
            <div class="flex items-center justify-between gap-3">
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Checklist Division Project</h2>
                    <p class="text-sm text-slate-500">Project belum dianggap selesai sebelum semua division selesai.</p>
                </x-wirekit::stack>
                <span class="text-sm font-semibold text-slate-700">{{ $completedDivisions }} / {{ $totalDivisions }} complete</span>
            </div>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="space-y-3">
                @foreach ($masterProject->divisionProjects as $project)
                    @php
                        $done = $project->status === 'completed';
                    @endphp

                    <div class="flex flex-col gap-3 rounded-xl border {{ $done ? 'border-emerald-100 bg-emerald-50/40' : 'border-slate-100 bg-white' }} p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded border
                                {{ $done ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-300 bg-white text-transparent' }}">
                                <x-wirekit::icon name="check" class="size-3" />
                            </span>

                            <div>
                                <a href="{{ route('work-management.division-projects.show', $project) }}"
                                    wire:navigate class="text-sm font-semibold text-[#168ED1] hover:underline">
                                    {{ $project->name }}
                                </a>
                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $project->division?->name ?? '-' }} · {{ $project->manager?->user?->name ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <span class="text-xs font-medium {{ $done ? 'text-emerald-700' : ($project->status === 'submitted_to_gm' ? 'text-violet-700' : ($project->status === 'revision_required' ? 'text-amber-700' : 'text-slate-500')) }}">
                            @switch($project->status)
                                @case('completed') Complete @break
                                @case('submitted_to_gm') Menunggu Approval GM @break
                                @case('revision_required') Perlu Revisi @break
                                @default Belum Complete
                            @endswitch
                        </span>
                    </div>
                @endforeach
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

    <x-wirekit::card>
        <x-wirekit::card.body>
            @if ($masterProject->status === 'completed')
                <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-700">
                    Project sudah selesai. Tidak ada approval/revisi master project lagi.
                </div>
            @elseif ($allComplete)
                <div class="space-y-4">
                    <div class="rounded-xl border border-sky-100 bg-sky-50 p-4">
                        <p class="text-sm font-semibold text-sky-800">Semua Division Project sudah complete.</p>
                        <p class="mt-1 text-sm text-sky-700">
                            GM sekarang dapat menandai Master Project sebagai selesai.
                        </p>
                    </div>

                    <x-wirekit::button
                        type="button"
                        wire:click="complete"
                        class="bg-[#30AFFF] text-white hover:bg-[#1599E8]"
                    >
                        Tandai Project Selesai
                    </x-wirekit::button>
                </div>
            @else
                <div class="rounded-xl border border-amber-100 bg-amber-50 p-4 text-sm text-amber-800">
                    Belum dapat difinalisasi. Pastikan seluruh Division Project sudah di-approve GM dan ditandai complete.
                </div>
            @endif
        </x-wirekit::card.body>
    </x-wirekit::card>
</x-wirekit::stack>
