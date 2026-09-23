<x-wirekit::stack gap="md">
    <div>
        <div class="mb-2 flex items-center gap-2">
            <a href="{{ route('work-management.master-projects.show', $masterProject) }}" wire:navigate class="text-sm text-[#30AFFF] hover:underline">Master Project</a>
            <span class="text-sm text-slate-400">/ Final Approval</span>
        </div>

        <x-wirekit::stack gap="1">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Final Approval Master Project</h1>
            <p class="text-sm text-slate-500">{{ $masterProject->name }}</p>
        </x-wirekit::stack>
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Division Projects</h2>
                <p class="text-sm text-slate-500">Review laporan Manager dari seluruh Division Project sebelum keputusan final.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="wk-scrollbar overflow-x-auto">
                <x-wirekit::table hoverable>
                    <x-wirekit::table.head>
                        <x-wirekit::table.row>
                            <x-wirekit::table.th>Division Project</x-wirekit::table.th>
                            <x-wirekit::table.th>Divisi</x-wirekit::table.th>
                            <x-wirekit::table.th>Manager</x-wirekit::table.th>
                            <x-wirekit::table.th>Status</x-wirekit::table.th>
                            <x-wirekit::table.th>Progress Manual</x-wirekit::table.th>
                            <x-wirekit::table.th>Catatan Progress</x-wirekit::table.th>
                            <x-wirekit::table.th>Laporan Manager</x-wirekit::table.th>
                        </x-wirekit::table.row>
                    </x-wirekit::table.head>

                    <x-wirekit::table.body>
                        @forelse ($masterProject->divisionProjects as $project)
                            @php
                                $statusClass = match ($project->status) {
                                    'completed' => 'bg-emerald-50 text-emerald-600',
                                    'ready_for_review' => 'bg-violet-50 text-violet-600',
                                    'submitted_to_gm' => 'bg-blue-50 text-blue-600',
                                    'manager_approved' => 'bg-cyan-50 text-cyan-600',
                                    'revision_required' => 'bg-amber-50 text-amber-600',
                                    'in_progress' => 'bg-sky-50 text-sky-600',
                                    'rejected' => 'bg-rose-50 text-rose-600',
                                    default => 'bg-slate-100 text-slate-600',
                                };
                            @endphp

                            <x-wirekit::table.row>
                                <x-wirekit::table.td>
                                    <span class="text-sm font-semibold text-slate-800">{{ $project->name }}</span>
                                </x-wirekit::table.td>
                                <x-wirekit::table.td>
                                    <span class="text-sm text-slate-700">{{ $project->division?->name ?? '-' }}</span>
                                </x-wirekit::table.td>
                                <x-wirekit::table.td>
                                    <span class="text-sm text-slate-700">{{ $project->manager?->user?->name ?? '-' }}</span>
                                </x-wirekit::table.td>
                                <x-wirekit::table.td>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">
                                        {{ str_replace('_', ' ', $project->status) }}
                                    </span>
                                </x-wirekit::table.td>
                                @php
                                    $latestProgress = $project->progressUpdates->sortByDesc('id')->first();
                                    $managerReport = $project->reports
                                        ->where('report_level', 'manager')
                                        ->sortByDesc('id')
                                        ->first();
                                    $automaticProgress = $project->automaticProgress();
                                @endphp

                                <x-wirekit::table.td>
                                    <div class="min-w-36 space-y-1.5">
                                        <div class="flex items-center justify-between text-xs text-slate-500">
                                            <span>Manual</span>
                                            <span class="font-semibold text-slate-700">{{ $project->manual_progress }}%</span>
                                        </div>
                                        <div class="h-2 rounded-full bg-slate-100">
                                            <div class="h-2 rounded-full bg-[#30AFFF]" style="width: {{ $project->manual_progress }}%"></div>
                                        </div>
                                        <span class="text-[11px] text-slate-400">Auto: {{ $automaticProgress }}%</span>
                                    </div>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <div class="max-w-xs">
                                        <p class="line-clamp-3 text-sm text-slate-700">
                                            {{ $latestProgress?->note ?: 'Tidak ada catatan progress.' }}
                                        </p>
                                        @if ($latestProgress)
                                            <span class="mt-1 block text-[11px] text-slate-400">
                                                {{ $latestProgress->reporter?->user?->name ?? '-' }} · {{ $latestProgress->created_at?->format('d M Y H:i') }}
                                            </span>
                                        @endif
                                    </div>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <div class="max-w-sm">
                                        <p class="line-clamp-4 text-sm text-slate-700">
                                            {{ $managerReport?->content ?? 'Belum ada laporan Manager.' }}
                                        </p>
                                        @if ($managerReport)
                                            <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px]">
                                                <span class="text-slate-400">{{ $managerReport->reporter?->user?->name ?? '-' }}</span>
                                                <span class="text-slate-300">·</span>
                                                <span class="rounded-full px-2 py-0.5 font-medium
                                                    {{ match ($managerReport->status) {
                                                        'approved' => 'bg-emerald-50 text-emerald-600',
                                                        'rejected' => 'bg-rose-50 text-rose-600',
                                                        default => 'bg-violet-50 text-violet-600',
                                                    } }}">
                                                    {{ $managerReport->status === 'submitted' ? 'Menunggu Approval GM' : ucfirst($managerReport->status) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @empty
                            <x-wirekit::table.row>
                                <x-wirekit::table.td colspan="7">
                                    <div class="py-10 text-center text-sm text-slate-500">Belum ada Division Project.</div>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @endforelse
                    </x-wirekit::table.body>
                </x-wirekit::table>
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Keputusan Final</h2>
                <p class="text-sm text-slate-500">Berikan keputusan approval dan feedback untuk project.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <form wire:submit="approve" class="space-y-5">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Keputusan</label>
                    <select wire:model="decision" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20">
                        <option value="">Pilih keputusan</option>
                        <option value="approved">Approve</option>
                        <option value="rejected">Return untuk Revisi</option>
                    </select>
                    @error('decision') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Feedback</label>
                    <textarea wire:model="feedback" rows="5" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20" placeholder="Tuliskan feedback final approval"></textarea>
                    @error('feedback') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <x-wirekit::button
                        type="button"
                        variant="outline"
                        href="{{ route('work-management.master-projects.show', $masterProject) }}"
                        wire:navigate
                    >
                        Batal
                    </x-wirekit::button>

                    <x-wirekit::button type="submit" class="bg-[#30AFFF] text-white hover:bg-[#1599E8]">
                        Simpan Final Approval
                    </x-wirekit::button>
                </div>
            </form>
        </x-wirekit::card.body>
    </x-wirekit::card>
</x-wirekit::stack>