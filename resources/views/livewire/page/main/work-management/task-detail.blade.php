<x-wirekit::stack gap="md">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <x-wirekit::stack gap="sm">
            <div class="flex items-center gap-2">
                <a href="{{ route('work-management.tasks') }}" wire:navigate class="text-sm text-[#30AFFF] hover:underline">Tasks</a>
                <span class="text-sm text-slate-400">/ Detail</span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $task->title }}</h1>
            <p class="text-sm text-slate-500">
                {{ $task->divisionProject?->name ?? '-' }} · {{ $task->team?->name ?? '-' }} · {{ $task->assignee?->user?->name ?? '-' }}
            </p>
        </x-wirekit::stack>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @php
        $statusClass = match ($task->status) {
            'done' => 'bg-emerald-50 text-emerald-600',
            'in_review' => 'bg-violet-50 text-violet-600',
            'in_progress' => 'bg-sky-50 text-sky-600',
            'blocked' => 'bg-amber-50 text-amber-600',
            'cancelled' => 'bg-rose-50 text-rose-600',
            default => 'bg-slate-100 text-slate-600',
        };
    @endphp

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    <span class="text-sm font-medium text-slate-500">Status</span>
                    <span class="inline-flex w-fit items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">{{ str_replace('_', ' ', $task->status) }}</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Progress</span>
                    <span class="text-lg font-bold text-slate-900">{{ $task->progress }}%</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Deadline</span>
                    <span class="text-lg font-bold text-slate-900">{{ $task->due_date?->format('d M Y') ?? '—' }}</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-slate-500">Supervisor</span>
                    <span class="text-sm font-semibold text-slate-900">{{ $task->team?->supervisor?->user?->name ?? '—' }}</span>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Deskripsi</h2>
                <p class="text-sm text-slate-500">Detail pekerjaan dan konteks task.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>
        <x-wirekit::card.body>
            <p class="whitespace-pre-line text-sm leading-6 text-slate-700">{{ $task->description ?: '—' }}</p>
        </x-wirekit::card.body>
    </x-wirekit::card>

    @can('updateOwn', $task)
        @if (in_array($task->status, ['to_do', 'in_progress', 'blocked']))
            <x-wirekit::card>
                <x-wirekit::card.header>
                    <x-wirekit::stack gap="1">
                        <h2 class="text-lg font-semibold text-slate-900">Update Pekerjaan</h2>
                        <p class="text-sm text-slate-500">Perbarui progress dan status pekerjaan Anda.</p>
                    </x-wirekit::stack>
                </x-wirekit::card.header>

                <x-wirekit::card.body>
                    <form wire:submit="saveWork" class="space-y-5">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Progress (%)</label>
                                <x-wirekit::input type="number" min="0" max="100" wire:model="progress" name="progress" />
                                @error('progress') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Status kerja</label>
                                <select wire:model="workStatus" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20">
                                    <option value="in_progress">In Progress</option>
                                    <option value="blocked">Blocked</option>
                                </select>
                                @error('workStatus') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Hasil / Catatan</label>
                            <textarea wire:model="result" rows="5" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20" placeholder="Tuliskan hasil pekerjaan atau catatan"></textarea>
                            @error('result') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        @if ($workStatus === 'blocked')
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Alasan Blocked</label>
                                <textarea wire:model="blockedReason" rows="4" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20" placeholder="Jelaskan hambatan yang terjadi"></textarea>
                                @error('blockedReason') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div class="flex flex-wrap gap-2 border-t border-slate-100 pt-5">
                            <x-wirekit::button type="submit" variant="outline">Simpan Update</x-wirekit::button>

                            @if ($task->progress === 100)
                                <x-wirekit::button type="button" wire:click="submit" class="bg-[#30AFFF] text-white hover:bg-[#1599E8]">
                                    Submit untuk Review
                                </x-wirekit::button>
                            @endif
                        </div>
                    </form>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endif
    @endcan

    @can('review', $task)
        @if ($task->status === 'in_review')
            <x-wirekit::card>
                <x-wirekit::card.header>
                    <x-wirekit::stack gap="1">
                        <h2 class="text-lg font-semibold text-slate-900">Review Supervisor</h2>
                        <p class="text-sm text-slate-500">Tinjau hasil task dan tentukan apakah perlu revisi atau dapat diselesaikan.</p>
                    </x-wirekit::stack>
                </x-wirekit::card.header>

                <x-wirekit::card.body>
                    <form wire:submit="review" class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Keputusan</label>
                            <select wire:model="reviewDecision" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20">
                                <option value="">Pilih keputusan</option>
                                <option value="approved">Approve</option>
                                <option value="rejected">Return untuk Revisi</option>
                            </select>
                            @error('reviewDecision') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Feedback</label>
                            <textarea wire:model="reviewFeedback" rows="4" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20" placeholder="Tuliskan feedback review"></textarea>
                            @error('reviewFeedback') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <x-wirekit::button type="submit" class="bg-[#30AFFF] text-white hover:bg-[#1599E8]">
                            Simpan Review
                        </x-wirekit::button>
                    </form>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endif
    @endcan

    <x-wirekit::card>
        <x-wirekit::card.header>
            <div class="flex items-center justify-between gap-3">
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Riwayat Review</h2>
                    <p class="text-sm text-slate-500">Riwayat keputusan dan feedback reviewer.</p>
                </x-wirekit::stack>
                <span class="text-sm text-slate-500">{{ $task->reviews->count() }} review</span>
            </div>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="divide-y divide-slate-100">
                @forelse ($task->reviews->sortByDesc('created_at') as $review)
                    @php
                        $reviewClass = $review->decision === 'approved'
                            ? 'bg-emerald-50 text-emerald-600'
                            : 'bg-rose-50 text-rose-600';
                    @endphp

                    <div class="space-y-2 py-4 first:pt-0 last:pb-0">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $reviewClass }}">
                                    {{ ucfirst($review->decision) }}
                                </span>
                                <span class="text-sm font-medium text-slate-800">{{ $review->reviewer?->user?->name ?? '—' }}</span>
                            </div>
                            <span class="text-xs text-slate-400">{{ $review->created_at?->format('d M Y H:i') }}</span>
                        </div>

                        @if ($review->feedback)
                            <p class="text-sm leading-6 text-slate-600">{{ $review->feedback }}</p>
                        @endif
                    </div>
                @empty
                    <div class="py-10 text-center text-sm text-slate-500">Belum ada review.</div>
                @endforelse
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>
</x-wirekit::stack>