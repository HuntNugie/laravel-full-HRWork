<div class="flex min-h-[calc(100vh-7rem)] gap-4">

    <x-wirekit::card class="hidden w-72 shrink-0 lg:flex lg:flex-col">
        <x-wirekit::card.header>
            <div class="flex items-center justify-between gap-3">
                <x-wirekit::stack gap="1">
                    <span class="text-sm font-medium text-[#30AFFF]">HRWork AI</span>
                    <h2 class="text-lg font-semibold text-slate-900">Percakapan</h2>
                </x-wirekit::stack>

                <x-wirekit::button
                    type="button"
                    size="sm"
                    class="bg-[#30AFFF] text-white hover:bg-sky-500"
                    wire:click="newConversation"
                >
                    Baru
                </x-wirekit::button>
            </div>
        </x-wirekit::card.header>

        <x-wirekit::card.body class="flex-1 overflow-y-auto">
            <div class="space-y-2">
                @forelse ($conversations as $conversation)
                    <button
                        type="button"
                        wire:key="conversation-{{ $conversation->id }}"
                        wire:click="openConversation('{{ $conversation->id }}')"
                        class="w-full rounded-xl border px-3 py-3 text-left transition
                            {{ $conversationId === $conversation->id
                                ? 'border-sky-200 bg-sky-50'
                                : 'border-slate-200 bg-white hover:border-sky-200 hover:bg-sky-50/60' }}"
                    >
                        <p class="truncate text-sm font-medium text-slate-800">
                            {{ $conversation->title ?: 'Percakapan baru' }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            {{ $conversation->updated_at?->diffForHumans() }}
                        </p>
                    </button>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 px-4 py-8 text-center">
                        <p class="text-sm font-medium text-slate-700">Belum ada percakapan</p>
                        <p class="mt-1 text-xs leading-5 text-slate-400">
                            Mulai percakapan pertama dengan HRWork AI.
                        </p>
                    </div>
                @endforelse
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

    <x-wirekit::card class="min-w-0 flex-1">
        <x-wirekit::card.header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <span class="text-sm font-medium text-[#30AFFF]">AI Assistant</span>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900">
                        HRWork AI
                    </h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Tanyakan sesuatu tentang HRWork.
                    </p>
                </div>

                <x-wirekit::button
                    type="button"
                    size="sm"
                    intent="neutral"
                    surface="outline"
                    wire:click="newConversation"
                >
                    Percakapan baru
                </x-wirekit::button>
            </div>
        </x-wirekit::card.header>

        <x-wirekit::card.body class="flex min-h-[calc(100vh-17rem)] flex-col">

            <div class="flex-1 space-y-5 overflow-y-auto px-1 py-2">

                @if (count($messages) === 0)
                    <div class="flex min-h-[420px] items-center justify-center">
                        <div class="max-w-2xl text-center">
                            <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-sky-50 text-2xl">
                                ✦
                            </div>

                            <h2 class="mt-5 text-2xl font-bold text-slate-900">
                                Halo, {{ auth()->user()->name }}
                            </h2>

                            <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-slate-500">
                                Saya dapat membantu Anda memahami data dan proses HRWork.
                                Nantinya saya juga dapat membantu analisis recruitment dan CV.
                            </p>

                            <div class="mt-7 grid gap-3 text-left sm:grid-cols-2">
                                @foreach ([
                                    'Ringkas data seorang karyawan',
                                    'Bagaimana attendance karyawan bulan ini?',
                                    'Siapa yang kontraknya akan segera berakhir?',
                                    'Apa yang masih kurang dari proses PHK?',
                                ] as $suggestion)
                                    <button
                                        type="button"
                                        wire:click="$set('prompt', @js($suggestion))"
                                        class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition hover:border-sky-200 hover:bg-sky-50"
                                    >
                                        {{ $suggestion }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    @foreach ($messages as $message)
                        <div
                            wire:key="message-{{ $loop->index }}"
                            class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}"
                        >
                            <div
                                class="max-w-3xl rounded-2xl px-4 py-3 text-sm leading-6 shadow-sm
                                    {{ $message['role'] === 'user'
                                        ? 'bg-[#30AFFF] text-white'
                                        : 'border border-slate-200 bg-slate-50 text-slate-700' }}"
                            >
                                {!! nl2br(e($message['content'])) !!}
                            </div>
                        </div>
                    @endforeach
                @endif

                @if ($isLoading)
                    <div class="flex justify-start">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                            HRWork AI sedang berpikir...
                        </div>
                    </div>
                @endif

            </div>

            <div class="mt-5 border-t border-slate-100 pt-4">
                <form wire:submit="send" class="flex items-end gap-3">
                    <div class="min-w-0 flex-1">
                        <textarea
                            wire:model="prompt"
                            rows="2"
                            placeholder="Tulis pertanyaan atau perintah..."
                            class="min-h-12 w-full resize-none rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-[#30AFFF] focus:ring-2 focus:ring-sky-100"
                            @disabled($isLoading)
                        ></textarea>

                        @error('prompt')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-wirekit::button
                        type="submit"
                        class="bg-[#30AFFF] text-white hover:bg-sky-500"
                        wire:loading.attr="disabled"
                    >
                        Kirim
                    </x-wirekit::button>
                </form>

                <p class="mt-2 text-center text-xs text-slate-400">
                    AI dapat membuat kesalahan. Verifikasi informasi penting di HRWork.
                </p>
            </div>

        </x-wirekit::card.body>
    </x-wirekit::card>

</div>
