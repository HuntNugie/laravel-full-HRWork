<x-wirekit::stack gap="md">
    <div>
        <div class="mb-2 flex items-center gap-2">
            <a href="{{ route('work-management.division-projects.show', $divisionProject) }}" wire:navigate class="text-sm text-[#30AFFF] hover:underline">Division Project</a>
            <span class="text-sm text-slate-400">/ Create Task</span>
        </div>

        <x-wirekit::stack gap="1">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Create Task</h1>
            <p class="text-sm text-slate-500">{{ $divisionProject->name }}</p>
        </x-wirekit::stack>
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Informasi Task</h2>
                <p class="text-sm text-slate-500">
                    {{ $isSupervisor
                        ? 'Pilih anggota team kamu yang akan mengerjakan task ini.'
                        : 'Tentukan team dan karyawan yang bertanggung jawab atas task ini.' }}
                </p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <form wire:submit="save" class="space-y-5">

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Team</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">{{ $team->name }}</p>
                    <p class="mt-1 text-xs text-slate-500">
                        Supervisor: {{ $team->supervisor?->user?->name ?? '—' }}
                    </p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Anggota Team</label>
                    <select wire:model="assignee_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20">
                        <option value="">
                            Pilih anggota team
                        </option>
                        @foreach ($assignees as $assignee)
                            <option value="{{ $assignee->id }}">
                                {{ $assignee->user?->name ?? $assignee->id }}
                            </option>
                        @endforeach
                    </select>
                    @error('assignee_id') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Judul Task</label>
                    <x-wirekit::input type="text" wire:model="title" name="title" placeholder="Masukkan judul task" />
                    @error('title') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Deskripsi</label>
                    <textarea wire:model="description" rows="5" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20" placeholder="Jelaskan pekerjaan yang harus dilakukan"></textarea>
                    @error('description') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div class="max-w-md">
                    <label class="mb-2 block text-sm font-medium text-slate-700">Deadline</label>
                    <input type="date" wire:model="due_date" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20" />
                    @error('due_date') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <x-wirekit::button
                        type="button"
                        variant="outline"
                        href="{{ route('work-management.division-projects.teams.show', ['divisionProject' => $divisionProject, 'team' => $team]) }}"
                        wire:navigate
                    >
                        Batal
                    </x-wirekit::button>

                    <x-wirekit::button type="submit" class="bg-[#30AFFF] text-white hover:bg-[#1599E8]">
                        Simpan Task
                    </x-wirekit::button>
                </div>
            </form>
        </x-wirekit::card.body>
    </x-wirekit::card>
</x-wirekit::stack>
