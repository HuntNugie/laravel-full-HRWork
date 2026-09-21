<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Master Projects</h1>
            <p class="text-sm text-gray-500">Daftar proyek utama perusahaan.</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Nama</th>
                        <th class="px-4 py-3 text-left font-medium">Status</th>
                        <th class="px-4 py-3 text-left font-medium">Tanggal mulai</th>
                        <th class="px-4 py-3 text-left font-medium">Deadline</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($projects as $project)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $project->name }}</td>
                            <td class="px-4 py-3">{{ $project->status }}</td>
                            <td class="px-4 py-3">{{ $project->start_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $project->due_date?->format('d M Y') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada master project.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
