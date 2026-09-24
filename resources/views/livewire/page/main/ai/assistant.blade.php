<div class="min-h-[calc(100vh-7rem)]">
    <div class="mx-auto flex h-full max-w-6xl flex-col gap-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#30AFFF]/10 text-[#30AFFF] ring-1 ring-[#30AFFF]/20">
                        <x-wirekit::icon name="sparkles" class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">
                            HRWork AI
                        </h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Asisten AI untuk membantu memahami data dan proses HRWork.
                        </p>
                    </div>
                </div>
            </div>

            <x-wirekit::button
                wire:click="newConversation"
                wire:loading.attr="disabled"
                variant="outline"
                size="sm"
                icon="plus"
            >
                Percakapan baru
            </x-wirekit::button>
        </div>

        <div class="flex min-h-[620px] flex-1 flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                @if ($messages === [])
                    <div class="flex min-h-[520px] items-center justify-center">
                        <div class="max-w-2xl text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-[#30AFFF]/10 text-[#30AFFF]">
                                <x-wirekit::icon name="sparkles" class="h-8 w-8" />
                            </div>

                            <h2 class="mt-6 text-2xl font-semibold text-slate-900 dark:text-white">
                                Apa yang ingin kamu tanyakan?
                            </h2>

                            <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-500 dark:text-slate-400">
                                Untuk saat ini AI digunakan sebagai chat foundation. Tool untuk membaca
                                data HRWork akan ditambahkan bertahap setelah smoke test berhasil.
                            </p>

                            <div class="mt-8 grid gap-3 text-left sm:grid-cols-2">
                                @foreach ([
                                    'Jelaskan fungsi HRWork AI.',
                                    'Apa yang bisa dibantu AI dalam HRIS?',
                                    'Bagaimana nanti AI mengakses data karyawan?',
                                    'Apa batasan AI dalam proses HR?'
                                ] as $suggestion)
                                    <button
                                        type="button"
                                        wire:click="$set('prompt', @js($suggestion))"
                                        class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-left text-sm text-slate-700 transition hover:border-[#30AFFF]/40 hover:bg-[#30AFFF]/5 hover:text-[#30AFFF] dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"
                                    >
                                        {{ $suggestion }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach ($messages as $message)
                            <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[90%] sm:max-w-[75%]">
                                    <div class="{{ $message['role'] === 'user'
                                        ? 'rounded-2xl rounded-br-md bg-[#30AFFF] px-4 py-3 text-sm leading-6 text-white shadow-sm'
                                        : 'rounded-2xl rounded-bl-md border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200' }}">
                                        {!! nl2br(e($message['content'])) !!}
                                    </div>

                                    <div class="mt-1.5 px-1 text-[11px] text-slate-400">
                                        {{ $message['role'] === 'user' ? 'Kamu' : 'HRWork AI' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if ($isLoading)
                            <div class="flex justify-start">
                                <div class="rounded-2xl rounded-bl-md border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
                                    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                                        <span class="h-2 w-2 animate-pulse rounded-full bg-[#30AFFF]"></span>
                                        <span class="h-2 w-2 animate-pulse rounded-full bg-[#30AFFF] [animation-delay:150ms]"></span>
                                        <span class="h-2 w-2 animate-pulse rounded-full bg-[#30AFFF] [animation-delay:300ms]"></span>
                                        <span class="ml-1">AI sedang berpikir...</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            @if ($errorMessage)
                <div class="border-t border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-950 dark:bg-red-950/30 dark:text-red-300">
                    {{ $errorMessage }}
                </div>
            @endif

            <div class="border-t border-slate-200 bg-slate-50/80 p-4 dark:border-slate-800 dark:bg-slate-900/50 sm:p-5">
                <form wire:submit="send" class="space-y-3">
                    <div class="rounded-2xl border border-slate-200 bg-white p-2 shadow-sm focus-within:border-[#30AFFF]/60 focus-within:ring-2 focus-within:ring-[#30AFFF]/10 dark:border-slate-800 dark:bg-slate-950">
                        <textarea
                            wire:model="prompt"
                            rows="3"
                            maxlength="5000"
                            placeholder="Tanyakan sesuatu tentang HR, kebijakan, atau data HRWork..."
                            class="w-full resize-none border-0 bg-transparent px-3 py-2 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:ring-0 dark:text-white"
                            wire:loading.attr="disabled"
                        ></textarea>

                        <div class="flex items-center justify-between gap-3 px-2 pb-1">
                            <p class="text-xs text-slate-400">
                                AI dapat membuat kesalahan. Verifikasi informasi penting di HRWork.
                            </p>

                            <x-wirekit::button
                                type="submit"
                                wire:loading.attr="disabled"
                                size="sm"
                                icon="paper-airplane"
                            >
                                Kirim
                            </x-wirekit::button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
