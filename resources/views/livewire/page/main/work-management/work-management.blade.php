<x-wirekit::stack gap="md">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <x-wirekit::stack gap="sm">
            <span class="text-sm font-medium text-[#30AFFF]">Work Management</span>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Master Projects</h1>
            <p class="text-sm text-slate-500">Pantau master project dan pembagian pekerjaan per divisi.</p>
        </x-wirekit::stack>

        <div class="w-full sm:w-72">
            <x-wirekit::input wire:model.live="search" placeholder="Cari master project" class="text-black" />
        </div>
    </div>

    <x-wirekit::card>
        <x-wirekit::card.body>
            <div class="overflow-x-auto">
                <x-wirekit::table>
                    <x-wirekit::table.head>
                        <x-wirekit::table.row>
                            <x-wirekit::table.th>Project</x-wirekit::table.th>
                            <x-wirekit::table.th>Status</x-wirekit::table.th>
                            <x-wirekit::table.th>Due Date</x-wirekit::table.th>
                            <x-wirekit::table.th>Division Projects</x-wirekit::table.th>
                        </x-wirekit::table.row>
                    </x-wirekit::table.head>
                    <x-wirekit::table.body>
                        @forelse ($projects as $project)
                            <x-wirekit::table.row>
                                <x-wirekit::table.td>
                                    <p class="font-semibold text-slate-800">{{ $project->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $project->description ?: 'Tidak ada deskripsi' }}</p>
                                </x-wirekit::table.td>
                                <x-wirekit::table.td>{{ str_replace('_', ' ', $project->status) }}</x-wirekit::table.td>
                                <x-wirekit::table.td>{{ $project->due_date?->format('d M Y') ?? '-' }}</x-wirekit::table.td>
                                <x-wirekit::table.td>{{ $project->divisionProjects->count() }}</x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @empty
                            <x-wirekit::table.row>
                                <x-wirekit::table.td colspan="4">
                                    <p class="py-8 text-center text-sm text-slate-500">Belum ada master project.</p>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @endforelse
                    </x-wirekit::table.body>
                </x-wirekit::table>
            </div>

            <div class="mt-4">{{ $projects->links() }}</div>
        </x-wirekit::card.body>
    </x-wirekit::card>
</x-wirekit::stack>
