<x-wirekit::stack gap="md">
    <div>
        <div class="mb-2 flex items-center gap-2">
            <a href="{{ route('work-management.division-projects.teams.show', ['divisionProject' => $divisionProject, 'team' => $team]) }}" wire:navigate class="text-sm text-[#30AFFF] hover:underline">Team</a>
            <span class="text-sm text-slate-400">/ Buat Task</span>
        </div>

        <x-wirekit::stack gap="1">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Buat Task</h1>
            <p class="text-sm text-slate-500">{{ $divisionProject->name }} · {{ $team->name }}</p>
        </x-wirekit::stack>
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Penugasan Task</h2>
                <p class="text-sm text-slate-500">Supervisor membuat task untuk salah satu Employee di Team ini.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <form wire:submit="save" class="space-y-5">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Team</label>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-medium text-slate-700">
                            {{ $team->name }}
                        </div>
                    </div>

                    <div>
                        <label for="assignee_id" class="mb-2 block text-sm font-medium text-slate-700">Task Worker</label>
                        <select id="assignee_id" wire:model="assignee_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20">
                            <option value="">Pilih Employee</option>
                            @foreach ($assignees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->user?->name ?? '-' }} · {{ $employee->employee_code }}</option>
                            @endforeach
                        </select>
                        @error('assignee_id') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label for="title" class="mb-2 block text-sm font-medium text-slate-700">Judul Task</label>
                    <x-wirekit::input id="title" type="text" wire:model="title" name="title" placeholder="Contoh: Implementasi endpoint login" />
                    @error('title') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi</label>
                    <textarea id="description" wire:model="description" rows="5" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20" placeholder="Jelaskan hasil yang diharapkan dari task"></textarea>
                    @error('description') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div class="max-w-md">
                    <label class="mb-2 block text-sm font-medium text-slate-700">Deadline</label>
                    <input type="date" wire:model="due_date" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20" />
                    @error('due_date') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div class="rounded-xl border border-sky-100 bg-sky-50 p-4 text-sm text-sky-800">
                    Task Worker tidak perlu mengubah progress atau mengirim laporan. Setelah pekerjaan selesai, cukup centang checkbox pada detail task.
                </div>

                <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <x-wirekit::button type="button" variant="outline" href="{{ route('work-management.division-projects.teams.show', ['divisionProject' => $divisionProject, 'team' => $team]) }}" wire:navigate>
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
