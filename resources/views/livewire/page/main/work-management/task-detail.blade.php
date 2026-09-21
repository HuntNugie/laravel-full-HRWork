<div class="space-y-6">
    <div><a href="{{ route('work-management.tasks') }}" wire:navigate class="text-sm text-blue-600">← Tasks</a><h1 class="mt-2 text-2xl font-semibold">{{ $task->title }}</h1><p class="text-sm text-gray-500">{{ $task->divisionProject?->name ?? '-' }} · {{ $task->team?->name ?? '-' }} · {{ $task->assignee?->user?->name ?? '-' }}</p></div>
    @if (session('success')) <div class="rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ session('success') }}</div> @endif
    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border bg-white p-4"><div class="text-xs uppercase text-gray-500">Status</div><div class="mt-1 font-semibold">{{ str_replace('_', ' ', $task->status) }}</div></div>
        <div class="rounded-xl border bg-white p-4"><div class="text-xs uppercase text-gray-500">Progress</div><div class="mt-1 font-semibold">{{ $task->progress }}%</div></div>
        <div class="rounded-xl border bg-white p-4"><div class="text-xs uppercase text-gray-500">Deadline</div><div class="mt-1 font-semibold">{{ $task->due_date?->format('d M Y') ?? '-' }}</div></div>
        <div class="rounded-xl border bg-white p-4"><div class="text-xs uppercase text-gray-500">Supervisor</div><div class="mt-1 font-semibold">{{ $task->team?->supervisor?->user?->name ?? '-' }}</div></div>
    </div>
    <div class="rounded-xl border bg-white p-5"><h2 class="font-semibold">Deskripsi</h2><p class="mt-2 whitespace-pre-line text-sm text-gray-700">{{ $task->description ?: '-' }}</p></div>

    @can('updateOwn', $task)
        @if (in_array($task->status, ['to_do','in_progress','blocked']))
            <form wire:submit="saveWork" class="space-y-4 rounded-xl border bg-white p-5">
                <h2 class="font-semibold">Update Pekerjaan</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <div><label class="mb-1 block text-sm font-medium">Progress (%)</label><input type="number" min="0" max="100" wire:model="progress" class="w-full rounded-lg border-gray-300" />@error('progress')<span class="text-sm text-red-600">{{ $message }}</span>@enderror</div>
                    <div><label class="mb-1 block text-sm font-medium">Status kerja</label><select wire:model="workStatus" class="w-full rounded-lg border-gray-300"><option value="in_progress">In Progress</option><option value="blocked">Blocked</option></select></div>
                </div>
                <div><label class="mb-1 block text-sm font-medium">Hasil / Catatan</label><textarea wire:model="result" rows="4" class="w-full rounded-lg border-gray-300"></textarea></div>
                @if ($workStatus === 'blocked')<div><label class="mb-1 block text-sm font-medium">Alasan Blocked</label><textarea wire:model="blockedReason" rows="3" class="w-full rounded-lg border-gray-300"></textarea></div>@endif
                <div class="flex flex-wrap gap-2"><button class="rounded-lg border px-4 py-2 text-sm">Simpan Update</button>@if ($task->progress === 100)<button type="button" wire:click="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white">Submit untuk Review</button>@endif</div>
            </form>
        @endif
    @endcan

    @can('review', $task)
        @if ($task->status === 'in_review')
            <form wire:submit="review" class="space-y-3 rounded-xl border bg-white p-5"><h2 class="font-semibold">Review Supervisor</h2><select wire:model="reviewDecision" class="w-full rounded-lg border-gray-300"><option value="">Pilih keputusan</option><option value="approved">Approve</option><option value="rejected">Return untuk Revisi</option></select><textarea wire:model="reviewFeedback" rows="3" class="w-full rounded-lg border-gray-300" placeholder="Feedback"></textarea><button class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white">Simpan Review</button></form>
        @endif
    @endcan

    <div class="rounded-xl border bg-white"><div class="border-b px-4 py-3 font-semibold">Riwayat Review</div><div class="divide-y">@forelse ($task->reviews->sortByDesc('created_at') as $review)<div class="space-y-1 p-4 text-sm"><div class="font-medium">{{ ucfirst($review->decision) }} · {{ $review->reviewer?->user?->name ?? '-' }}</div><div class="text-gray-500">{{ $review->created_at?->format('d M Y H:i') }}</div>@if ($review->feedback)<div>{{ $review->feedback }}</div>@endif</div>@empty<div class="p-6 text-center text-gray-500">Belum ada review.</div>@endforelse</div></div>
</div>
