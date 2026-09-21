<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('work-management.master-projects') }}" wire:navigate class="text-sm text-blue-600">← Master Projects</a>
            <h1 class="mt-2 text-2xl font-semibold">{{ $masterProject->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $masterProject->description ?: 'Tanpa deskripsi.' }}</p>
        </div>
        <div class="flex gap-2">
            @if ($masterProject->status === 'ready_for_review')
                @can('approve', $masterProject)
                    <a href="{{ route('work-management.master-projects.approve', $masterProject) }}" wire:navigate class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white">Final Approval GM</a>
                @endcan
            @endif
            @can('create', \App\Models\DivisionProject::class)
                <a href="{{ route('work-management.master-projects.division-projects.create', $masterProject) }}" wire:navigate class="rounded-lg border px-4 py-2 text-sm">Tambah Division Project</a>
            @endcan
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border bg-white p-4"><div class="text-xs uppercase text-gray-500">Status</div><div class="mt-1 font-semibold">{{ str_replace('_', ' ', $masterProject->status) }}</div></div>
        <div class="rounded-xl border bg-white p-4"><div class="text-xs uppercase text-gray-500">Mulai</div><div class="mt-1 font-semibold">{{ $masterProject->start_date?->format('d M Y') ?? '-' }}</div></div>
        <div class="rounded-xl border bg-white p-4"><div class="text-xs uppercase text-gray-500">Deadline</div><div class="mt-1 font-semibold">{{ $masterProject->due_date?->format('d M Y') ?? '-' }}</div></div>
        <div class="rounded-xl border bg-white p-4"><div class="text-xs uppercase text-gray-500">Division</div><div class="mt-1 font-semibold">{{ $masterProject->divisionProjects->count() }}</div></div>
    </div>

    <div class="overflow-hidden rounded-xl border bg-white">
        <div class="border-b px-4 py-3"><h2 class="font-semibold">Division Projects</h2></div>
        <div class="divide-y">
            @forelse ($masterProject->divisionProjects as $project)
                <div class="space-y-3 p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <a href="{{ route('work-management.division-projects.show', $project) }}" wire:navigate class="font-semibold text-blue-600 hover:underline">{{ $project->name }}</a>
                            <div class="text-sm text-gray-500">{{ $project->division?->name ?? '-' }} · Manager: {{ $project->manager?->user?->name ?? '-' }}</div>
                        </div>
                        <div class="text-sm">{{ str_replace('_', ' ', $project->status) }}</div>
                    </div>
                    @php($automaticProgress = $project->automaticProgress())
                    <div>
                        <div class="mb-1 flex justify-between text-xs text-gray-500"><span>Automatic progress</span><span>{{ $automaticProgress }}%</span></div>
                        <div class="h-2 rounded-full bg-gray-100"><div class="h-2 rounded-full bg-blue-600" style="width: {{ $automaticProgress }}%"></div></div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">Belum ada division project.</div>
            @endforelse
        </div>
    </div>
</div>
