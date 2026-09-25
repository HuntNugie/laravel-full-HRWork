<div class="min-h-[calc(100vh-7rem)] bg-sky-50/30">
    <div
        class="mx-auto flex h-full max-w-6xl flex-col gap-5"
        x-data="{
            submitting: false,
            pendingMessage: '',
            async submitPrompt() {
                const value = this.$refs.prompt?.value?.trim() ?? '';

                if (!value || this.submitting) {
                    return;
                }

                this.pendingMessage = value;

                // Clear only the visible composer. The submitted value is passed
                // directly to Livewire so it remains intact on the server.
                this.$refs.prompt.value = '';

                this.submitting = true;

                try {
                    await this.$wire.send(value);
                } finally {
                    this.submitting = false;
                    this.pendingMessage = '';
                }
            },
        }"
    >

        <div class="flex items-center gap-3 px-1">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#30AFFF]/10 text-[#30AFFF] ring-1 ring-[#30AFFF]/20">
                <x-wirekit::icon name="sparkles" class="h-6 w-6" />
            </div>

            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">
                    HRWork AI
                </h1>
                <p class="text-sm text-slate-500">
                    Asisten AI untuk membantu memahami data dan proses HRWork.
                </p>
            </div>
        </div>

        <div
            class="overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-[0_10px_40px_rgba(48,175,255,0.08)]"
            style="
                --color-wk-bg: #ffffff;
                --color-wk-bg-elevated: #ffffff;
                --color-wk-bg-muted: #f4faff;
                --color-wk-bg-subtle: #eef8ff;
                --color-wk-bg-input: #ffffff;
                --color-wk-text: #0f172a;
                --color-wk-text-muted: #64748b;
                --color-wk-text-subtle: #94a3b8;
                --color-wk-border: #dbeaf4;
                --color-wk-border-subtle: #e6f0f6;
                --color-wk-accent: #30AFFF;
                --color-wk-accent-fg: #ffffff;
                --color-wk-ring: #30AFFF;
            "
        >
            <x-wirekit::conversation
                max-height="560px"
                label="Percakapan HRWork AI"
            >
                <x-wirekit::stack gap="md" class="min-h-[500px] p-4 sm:p-6">

                    <div
                        x-show="!submitting"
                        x-cloak
                        x-transition.opacity.duration.150ms
                    >
                        @if ($messages === [])
                            <div class="flex min-h-[460px] items-center justify-center">
                                <div class="w-full max-w-2xl text-center">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-[#30AFFF]/10 text-[#30AFFF] ring-1 ring-[#30AFFF]/10">
                                        <x-wirekit::icon name="sparkles" class="h-8 w-8" />
                                    </div>

                                    <h2 class="mt-6 text-2xl font-semibold tracking-tight text-slate-900">
                                        Apa yang ingin kamu tanyakan?
                                    </h2>

                                    <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-500">
                                        Tanyakan seputar HR, kebijakan, atau data HRWork.
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
                                                class="rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 text-left text-sm text-slate-700 transition hover:border-[#30AFFF]/40 hover:bg-[#30AFFF]/10 hover:text-[#168fd8]"
                                            >
                                                {{ $suggestion }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    @foreach ($messages as $index => $message)
                        @if ($message['role'] === 'user')
                            <x-wirekit::message
                                wire:key="message-user-{{ $index }}"
                                :author="['name' => 'Kamu']"
                                side="right"
                            >
                                {{ $message['content'] }}
                            </x-wirekit::message>
                        @else
                            <div wire:key="message-assistant-{{ $index }}">
                                <x-wirekit::assistant-message
                                    :name="'HRWork AI'"
                                    model="Gemini 3.8 Flash"
                                    announce="all"
                                >
                                    {!! \Illuminate\Support\Str::markdown($message['content'], ['html_input' => 'strip']) !!}
                                </x-wirekit::assistant-message>
                            </div>
                        @endif
                    @endforeach

                    <template x-if="submitting && pendingMessage">
                        <div x-cloak>
                            <x-wirekit::message
                                :author="['name' => 'Kamu']"
                                side="right"
                            >
                                <span x-text="pendingMessage"></span>
                            </x-wirekit::message>
                        </div>
                    </template>

                    <template x-if="submitting">
                        <div x-cloak>
                            <x-wirekit::message-typing
                                author="HRWork AI"
                                announce
                            />
                        </div>
                    </template>

                </x-wirekit::stack>
            </x-wirekit::conversation>

            @if ($errorMessage)
                <div class="border-t border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errorMessage }}
                </div>
            @endif
        </div>

        <div class="rounded-3xl border border-sky-100 bg-sky-50/70 p-3 shadow-sm sm:p-4">
            <form x-on:submit.prevent="submitPrompt">
                <div class="rounded-2xl border border-sky-100 bg-white p-2 shadow-sm transition focus-within:border-[#30AFFF]/60 focus-within:ring-2 focus-within:ring-[#30AFFF]/10">
                    <textarea
                        x-ref="prompt"
                        wire:model="prompt"
                        rows="3"
                        maxlength="5000"
                        placeholder="Tanyakan sesuatu tentang HR, kebijakan, atau data HRWork..."
                        class="w-full resize-none border-0 bg-transparent px-3 py-2 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:ring-0"
                        x-bind:disabled="submitting"
                        wire:loading.attr="disabled"
                    ></textarea>

                    <div class="flex items-center justify-between gap-3 px-2 pb-1">
                        <p class="text-xs text-slate-400">
                            AI dapat membuat kesalahan. Verifikasi informasi penting di HRWork.
                        </p>

                        <x-wirekit::button
                            type="submit"
                            x-bind:disabled="submitting"
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
