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
                <h2 class="text-lg font-semibold text-slate-900">Review Progress Manager</h2>
                <p class="text-sm text-slate-500">Setiap Division Project diajukan Manager secara terpisah. Review progress dan catatan sebelum Master Project masuk final approval.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="space-y-4">
                @forelse ($masterProject->divisionProjects as $project)
                    @php
                        $managerReport = $project->reports
                            ->where('report_level', 'manager')
                            ->sortByDesc('id')
                            ->first();

                        $latestReview = $project->reviews
                            ->where('reviewer_level', 'general_manager')
                            ->sortByDesc('id')
                            ->first();

                        $automaticProgress = $project->automaticProgress();
                        $reportStatusClass = match ($managerReport?->status) {
                            'approved' => 'bg-emerald-50 text-emerald-600',
                            'rejected' => 'bg-rose-50 text-rose-600',
                            'submitted' => 'bg-violet-50 text-violet-600',
                            default => 'bg-slate-100 text-slate-500',
                        };
                    @endphp

                    <div class="rounded-xl border border-slate-100 p-4">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <x-wirekit::stack gap="1">
                                <a
                                    href="{{ route('work-management.division-projects.show', $project) }}"
                                    wire:navigate
                                    class="text-sm font-semibold text-[#168ED1] hover:underline"
                                >
                                    {{ $project->name }}
                                </a>
                                <span class="text-xs text-slate-400">
                                    {{ $project->division?->name ?? '-' }} · Manager: {{ $project->manager?->user?->name ?? '-' }}
                                </span>
                            </x-wirekit::stack>

                            <span class="inline-flex w-fit items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $reportStatusClass }}">
                                {{ $managerReport ? ($managerReport->status === 'submitted' ? 'Menunggu Approval GM' : ucfirst($managerReport->status)) : 'Belum Submit' }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
                            <div class="rounded-xl bg-slate-50 p-4">
                                <div class="flex items-center justify-between text-xs text-slate-500">
                                    <span>Progress Manager</span>
                                    <span class="font-bold text-slate-800">{{ $managerReport?->progress ?? $project->manual_progress }}%</span>
                                </div>
                                <div class="mt-2 h-2 rounded-full bg-slate-200">
                                    <div
                                        class="h-2 rounded-full bg-[#30AFFF]"
                                        style="width: {{ $managerReport?->progress ?? $project->manual_progress }}%"
                                    ></div>
                                </div>
                                <p class="mt-2 text-[11px] text-slate-400">Automatic Task: {{ $automaticProgress }}%</p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4 lg:col-span-2">
                                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Catatan / Laporan Manager</div>
                                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">
                                    {{ $managerReport?->content ?? 'Belum ada submission Manager.' }}
                                </p>
                                @if ($managerReport)
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] text-slate-400">
                                        <span>{{ $managerReport->reporter?->user?->name ?? '-' }}</span>
                                        <span>·</span>
                                        <span>{{ $managerReport->created_at?->format('d M Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($latestReview?->decision === 'rejected' && $latestReview->feedback)
                            <div class="mt-4 rounded-xl border border-amber-100 bg-amber-50 p-4">
                                <div class="text-xs font-semibold uppercase tracking-wide text-amber-700">Feedback GM Terakhir</div>
                                <p class="mt-1 text-sm leading-6 text-amber-900">{{ $latestReview->feedback }}</p>
                            </div>
                        @endif

                        @if ($managerReport?->status === 'submitted')
                            @can('reviewManagerReport', $project)
                                <div class="mt-4 border-t border-slate-100 pt-4">
                                    <div class="grid grid-cols-1 gap-3 lg:grid-cols-[1fr_auto] lg:items-end">
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-slate-700">Feedback GM</label>
                                            <textarea
                                                wire:model="managerReviewFeedback.{{ $project->id }}"
                                                rows="3"
                                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20"
                                                placeholder="Isi feedback jika progress/laporan perlu direvisi."
                                            ></textarea>
                                            @error("managerReviewFeedback.{$project->id}")
                                                <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="flex flex-col gap-2 sm:flex-row lg:justify-end">
                                            <x-wirekit::button
                                                type="button"
                                                class="bg-[#30AFFF] text-white hover:bg-[#1599E8]"
                                                wire:click="reviewManagerReport({{ $project->id }}, 'approved')"
                                            >
                                                Approve Progress
                                            </x-wirekit::button>

                                            <x-wirekit::button
                                                type="button"
                                                variant="outline"
                                                wire:click="reviewManagerReport({{ $project->id }}, 'rejected')"
                                            >
                                                Return untuk Revisi
                                            </x-wirekit::button>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        @endif
                    </div>
                @empty
                    <div class="py-10 text-center text-sm text-slate-500">Belum ada Division Project.</div>
                @endforelse
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>
    @if ($masterProject->status === 'ready_for_review')
        <x-wirekit::card>
            <x-wirekit::card.header>
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Final Approval Master Project</h2>
                    <p class="text-sm text-slate-500">Seluruh laporan Manager yang wajib sudah disetujui. Berikan keputusan final.</p>
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
    @else
        <x-wirekit::card>
            <x-wirekit::card.body>
                <div class="flex items-center gap-3 text-sm text-slate-600">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-violet-50 font-semibold text-violet-600">i</span>
                    <span>Final approval belum tersedia. Approve seluruh Division Project yang diperlukan terlebih dahulu.</span>
                </div>
            </x-wirekit::card.body>
        </x-wirekit::card>
    @endif
</x-wirekit::stack>