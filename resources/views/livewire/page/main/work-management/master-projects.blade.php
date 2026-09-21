<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Master Projects</h1>
            <p class="text-sm text-gray-500">Project utama perusahaan sesuai scope akses Anda.</p>
        </div>
        @can('create-master-project')
            <a href="{{ route('work-management.master-projects.create') }}" wire:navigate class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white">Buat Master Project</a>
        @endcan
    </div>

    <div class="overflow-hidden rounded-xl border bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Division Project</th>
                        <th class="px-4 py-3 text-left">Deadline</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($projects as $project)
                        <tr>
                            <td class="px-4 py-3">
                                <a class="font-medium text-blue-600 hover:underline" href="{{ route('work-management.master-projects.show', $project) }}" wire:navigate>{{ $project->name }}</a>
                                <div class="text-xs text-gray-500">{{ $project->creator?->user?->name ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3">{{ str_replace('_', ' ', $project->status) }}</td>
                            <td class="px-4 py-3">{{ $project->divisionProjects->count() }}</td>
                            <td class="px-4 py-3">{{ $project->due_date?->format('d M Y') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada project dalam scope Anda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
