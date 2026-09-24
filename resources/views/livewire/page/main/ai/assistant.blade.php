<div class="min-h-[calc(100vh-7rem)]">
    <div class="mx-auto flex h-full max-w-6xl flex-col gap-6">

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

        <x-wirekit::conversation
            max-height="620px"
            label="Percakapan HRWork AI"
            class="flex-1 rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950"
        >
            <x-wirekit::stack gap="md" class="min-h-[560px] p-4 sm:p-6">

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
                                data HRWork akan ditambahkan bertahap.<br>
                                Chat hanya ditampilkan selama halaman ini terbuka dan tidak disimpan sebagai riwayat.
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
                    @foreach ($messages as $message)
                        @if ($message['role'] === 'user')
                            <x-wirekit::message
                                :author="['name' => 'Kamu']"
                                side="right"
                            >
                                {!! nl2br(e($message['content'])) !!}
                            </x-wirekit::message>
                        @else
                            <x-wirekit::assistant-message model="gemini-3.8-flash">
                                {!! nl2br(e($message['content'])) !!}
                            </x-wirekit::assistant-message>
                        @endif
                    @endforeach

                    @if ($isLoading)
                        <x-wirekit::chat-marker variant="status">
                            AI sedang berpikir...
                        </x-wirekit::chat-marker>
                    @endif
                @endif

            </x-wirekit::stack>

            @if ($errorMessage)
                <x-slot:footer>
                    <div class="border-t border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-950 dark:bg-red-950/30 dark:text-red-300">
                        {{ $errorMessage }}
                    </div>
                </x-slot:footer>
            @endif
        </x-wirekit::conversation>

        <div class="border-t border-slate-200 bg-slate-50/80 rounded-3xl dark:border-slate-800 dark:bg-slate-900/50">
            <form wire:submit="send" class="space-y-3 p-4 sm:p-5">
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
