<div
    class="min-h-[calc(100vh-7rem)] bg-sky-50/30"
    x-data="{
        analyzing: false,
        progress: 0,
        timer: null,
        stageIndex: -1,
        steps: [
            {
                label: 'Membaca CV',
                detail: 'Mengekstrak isi dokumen dan menyiapkan data untuk dianalisis.',
            },
            {
                label: 'Memahami profil kandidat',
                detail: 'Mengidentifikasi pengalaman, pendidikan, dan kompetensi yang tertulis.',
            },
            {
                label: 'Menganalisis kompetensi',
                detail: 'Memetakan skill dan pengalaman yang memiliki bukti pada CV.',
            },
            {
                label: 'Mencocokkan dengan posisi',
                detail: 'Membandingkan CV dengan requirement dan jobdesk posisi yang dipilih.',
            },
            {
                label: 'Mengevaluasi company alignment',
                detail: 'Membandingkan CV dengan kriteria perusahaan yang diberikan HR.',
            },
            {
                label: 'Menyusun hasil analisis',
                detail: 'Merangkum evidence, gap, verifikasi, dan pertanyaan interview.',
            },
        ],
        states: [],
        init() {
            this.resetSteps();
        },
        resetSteps() {
            this.states = this.steps.map(() => 'pending');
            this.stageIndex = -1;
            this.progress = 0;
        },
        advance() {
            if (this.stageIndex < this.steps.length - 1) {
                if (this.stageIndex >= 0) {
                    this.states[this.stageIndex] = 'done';
                }

                this.stageIndex++;
                this.states[this.stageIndex] = 'active';

                const targets = [8, 22, 40, 60, 77, 90];
                this.progress = targets[this.stageIndex];
            }
        },
        startProgress() {
            this.resetSteps();
            this.analyzing = true;
            this.advance();

            this.timer = setInterval(() => {
                if (this.progress < 88) {
                    this.advance();
                }
            }, 1600);
        },
        finishProgress() {
            clearInterval(this.timer);
            this.states = this.steps.map(() => 'done');
            this.stageIndex = this.steps.length - 1;
            this.progress = 100;
        },
        stopProgress() {
            clearInterval(this.timer);
            this.timer = null;
            this.analyzing = false;
        },
        async startAnalysis() {
            if (this.analyzing) {
                return;
            }

            this.startProgress();

            try {
                await this.$wire.analyze();
                this.finishProgress();

                setTimeout(() => {
                    this.analyzing = false;
                    this.timer = null;
                }, 450);
            } catch (error) {
                clearInterval(this.timer);
                this.timer = null;
                this.analyzing = false;
            }
        },
    }"
>
    <div class="mx-auto flex h-full max-w-6xl flex-col gap-5">

        <div class="flex items-center gap-3 px-1">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#30AFFF]/10 text-[#30AFFF] ring-1 ring-[#30AFFF]/20">
                <x-wirekit::icon name="sparkles" class="h-6 w-6" />
            </div>

            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">
                    CV Analyzer
                </h1>
                <p class="text-sm text-slate-500">
                    Analisis CV kandidat berdasarkan posisi dan kebutuhan perusahaan.
                </p>
            </div>
        </div>

        @if ($errorMessage)
            <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errorMessage }}
            </div>
        @endif

        {{-- =====================================================
        ANALYSIS PROGRESS
        ====================================================== --}}
        <div
            x-show="analyzing"
            x-cloak
            class="rounded-3xl border border-sky-100 bg-white p-6 shadow-[0_10px_40px_rgba(48,175,255,0.08)]"
        >
            <div class="mx-auto max-w-3xl">

                <div class="text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#30AFFF]/10 text-[#30AFFF] ring-1 ring-[#30AFFF]/20">
                        <x-wirekit::icon name="sparkles" class="h-7 w-7" />
                    </div>

                    <h2 class="mt-4 text-xl font-semibold tracking-tight text-slate-900">
                        AI sedang menganalisis CV
                    </h2>

                    <p class="mt-2 text-sm text-slate-500">
                        Setiap tahap akan diproses sampai laporan analisis selesai.
                    </p>
                </div>

                <div class="mt-8">
                    <x-wirekit::progress
                        value-expression="progress"
                        max="100"
                        label="Progress analisis"
                        show-value
                        animation="shimmer"
                        size="md"
                    />
                </div>

                <div class="mt-8 space-y-3">
                    <template x-for="(step, index) in steps" :key="step.label">
                        <div class="flex items-start gap-3 rounded-2xl border border-slate-100 bg-slate-50/70 p-3">
                            <div
                                class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-semibold"
                                :class="{
                                    'bg-emerald-50 text-emerald-600': states[index] === 'done',
                                    'bg-[#30AFFF]/10 text-[#30AFFF]': states[index] === 'active',
                                    'bg-slate-100 text-slate-400': states[index] === 'pending'
                                }"
                            >
                                <span x-show="states[index] === 'done'">✓</span>
                                <span x-show="states[index] === 'active'" class="h-2 w-2 rounded-full bg-current"></span>
                                <span x-show="states[index] === 'pending'" x-text="index + 1"></span>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p
                                    class="text-sm font-semibold"
                                    :class="states[index] === 'pending' ? 'text-slate-400' : 'text-slate-800'"
                                    x-text="step.label"
                                ></p>

                                <p
                                    class="mt-0.5 text-xs leading-5 text-slate-500"
                                    x-show="states[index] !== 'pending'"
                                    x-text="step.detail"
                                ></p>
                            </div>

                            <span
                                x-show="states[index] === 'active'"
                                class="mt-1 text-xs font-medium text-[#30AFFF]"
                            >
                                Memproses
                            </span>
                        </div>
                    </template>
                </div>

            </div>
        </div>

        {{-- =====================================================
        ANALYSIS FORM
        ====================================================== --}}
        <div x-show="!analyzing" x-cloak>
            @if (!$analysisResult)
                <div class="grid gap-5 lg:grid-cols-3">

                    <x-wirekit::card class="lg:col-span-2">
                        <x-wirekit::card.header>
                            <x-wirekit::stack gap="1">
                                <h2 class="text-lg font-semibold text-slate-900">
                                    Upload CV
                                </h2>
                                <p class="text-sm text-slate-500">
                                    Upload CV kandidat untuk dianalisis.
                                </p>
                            </x-wirekit::stack>
                        </x-wirekit::card.header>

                        <x-wirekit::card.body>
                            <form x-on:submit.prevent="startAnalysis" class="space-y-6">

                                <div>
                                    <label class="text-sm font-medium text-slate-700">
                                        File CV
                                    </label>

                                    <div class="mt-2 rounded-2xl border-2 border-dashed border-sky-100 bg-sky-50/50 p-6 text-center transition hover:border-[#30AFFF]/40">
                                        <input
                                            type="file"
                                            wire:model="cvFile"
                                            accept=".pdf,.docx,.txt"
                                            class="mx-auto block w-full max-w-md cursor-pointer text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-[#30AFFF]/10 file:px-4 file:py-2 file:text-sm file:font-medium file:text-[#168fd8] hover:file:bg-[#30AFFF]/20"
                                        />

                                        <p class="mt-3 text-xs text-slate-400">
                                            PDF, DOCX, atau TXT • maksimal 10 MB
                                        </p>

                                        @error('cvFile')
                                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                        @enderror

                                        @if ($cvFile)
                                            <div class="mt-4 rounded-xl bg-white px-3 py-2 text-left text-xs text-slate-600 ring-1 ring-sky-100">
                                                File terpilih:
                                                <span class="font-medium text-slate-800">
                                                    {{ $cvFile->getClientOriginalName() }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <label for="cv-position" class="text-sm font-medium text-slate-700">
                                        Posisi yang dilamar
                                    </label>

                                    <select
                                        id="cv-position"
                                        wire:model="positionId"
                                        class="mt-2 block w-full rounded-xl border border-sky-100 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none focus:border-[#30AFFF]/60 focus:ring-2 focus:ring-[#30AFFF]/10"
                                    >
                                        <option value="">Tidak ditentukan</option>
                                        @foreach ($positions as $position)
                                            <option value="{{ $position->id }}">
                                                {{ $position->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <p class="mt-2 text-xs text-slate-400">
                                        Saat dipilih, AI akan menggunakan deskripsi dan jobdesk posisi dari HRWork.
                                    </p>
                                </div>

                                <div>
                                    <label for="company-criteria" class="text-sm font-medium text-slate-700">
                                        Kriteria perusahaan / tambahan
                                        <span class="font-normal text-slate-400">(opsional)</span>
                                    </label>

                                    <textarea
                                        id="company-criteria"
                                        wire:model="companyCriteria"
                                        rows="5"
                                        maxlength="5000"
                                        placeholder="Contoh: terbiasa Agile/Scrum, pernah bekerja remote, pengalaman di SaaS, wajib memahami REST API..."
                                        class="mt-2 w-full resize-none rounded-xl border border-sky-100 bg-white px-3 py-3 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-[#30AFFF]/60 focus:ring-2 focus:ring-[#30AFFF]/10"
                                    ></textarea>

                                    <p class="mt-2 text-xs text-slate-400">
                                        Hanya masukkan requirement pekerjaan atau kebutuhan perusahaan yang relevan.
                                    </p>
                                </div>

                                <div class="flex items-center justify-end">
                                    <x-wirekit::button
                                        type="submit"
                                        class="bg-[#30AFFF] text-white hover:bg-sky-500"
                                        wire:loading.attr="disabled"
                                    >
                                        <x-wirekit::icon name="sparkles" />
                                        Analisis CV
                                    </x-wirekit::button>
                                </div>

                            </form>
                        </x-wirekit::card.body>
                    </x-wirekit::card>

                    <x-wirekit::card>
                        <x-wirekit::card.header>
                            <x-wirekit::stack gap="1">
                                <h2 class="text-lg font-semibold text-slate-900">
                                    Yang akan dianalisis
                                </h2>
                                <p class="text-sm text-slate-500">
                                    Hasil dibuat berdasarkan evidence dari CV.
                                </p>
                            </x-wirekit::stack>
                        </x-wirekit::card.header>

                        <x-wirekit::card.body>
                            <div class="space-y-3">
                                @foreach ([
                                    'Profil kandidat',
                                    'Skill & pengalaman',
                                    'Kesesuaian requirement posisi',
                                    'Kesesuaian jobdesk',
                                    'Company alignment',
                                    'Gap & hal yang perlu diverifikasi',
                                    'Pertanyaan interview',
                                ] as $item)
                                    <div class="flex items-start gap-2">
                                        <span class="mt-1 text-[#30AFFF]">✓</span>
                                        <span class="text-sm leading-5 text-slate-600">
                                            {{ $item }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 rounded-2xl bg-sky-50/80 p-4">
                                <p class="text-xs leading-5 text-slate-500">
                                    AI tidak mengambil keputusan hiring. Informasi yang tidak tercantum di CV akan
                                    ditandai sebagai tidak ditemukan dan tetap perlu diverifikasi oleh HR.
                                </p>
                            </div>
                        </x-wirekit::card.body>
                    </x-wirekit::card>

                </div>
            @else
                @php
                    $statusLabel = [
                        'strong' => 'Kuat',
                        'moderate' => 'Cukup',
                        'limited' => 'Terbatas',
                        'not_assessed' => 'Belum dinilai',
                    ];

                    $requirementLabel = [
                        'met' => 'Terpenuhi',
                        'partial' => 'Sebagian',
                        'not_found' => 'Tidak ditemukan',
                        'conflicting' => 'Perlu verifikasi',
                        'not_assessed' => 'Belum dinilai',
                    ];

                    $statusClass = [
                        'strong' => 'bg-emerald-50 text-emerald-700',
                        'moderate' => 'bg-sky-50 text-sky-700',
                        'limited' => 'bg-amber-50 text-amber-700',
                        'not_assessed' => 'bg-slate-100 text-slate-600',
                    ];

                    $requirementClass = [
                        'met' => 'bg-emerald-50 text-emerald-700',
                        'partial' => 'bg-amber-50 text-amber-700',
                        'not_found' => 'bg-slate-100 text-slate-600',
                        'conflicting' => 'bg-red-50 text-red-700',
                        'not_assessed' => 'bg-slate-100 text-slate-600',
                    ];

                    $positionAlignment = $analysisResult['position_alignment'] ?? [];
                    $companyAlignment = $analysisResult['company_alignment'] ?? [];
                    $candidate = $analysisResult['candidate'] ?? [];
                @endphp

                <div class="flex flex-col gap-5">

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <x-wirekit::stack gap="1">
                            <span class="text-sm font-medium text-[#30AFFF]">AI Screening</span>
                            <h2 class="text-2xl font-semibold tracking-tight text-slate-900">
                                Hasil Analisis CV
                            </h2>
                            <p class="text-sm text-slate-500">
                                {{ $candidate['name'] ?? 'Kandidat' }}
                                •
                                {{ $candidate['current_or_targeted_title'] ?? 'Profil kandidat' }}
                            </p>
                        </x-wirekit::stack>

                        <x-wirekit::button
                            type="button"
                            wire:click="resetAnalysis"
                            class="border border-sky-100 bg-white text-slate-700 hover:bg-sky-50"
                        >
                            Analisis CV lain
                        </x-wirekit::button>
                    </div>

                    <div class="grid gap-5 lg:grid-cols-3">

                        <x-wirekit::card class="lg:col-span-2">
                            <x-wirekit::card.header>
                                <h3 class="text-lg font-semibold text-slate-900">
                                    Candidate Profile
                                </h3>
                            </x-wirekit::card.header>

                            <x-wirekit::card.body>
                                <div class="space-y-5">
                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Profile Summary
                                        </p>
                                        <p class="mt-2 text-sm leading-6 text-slate-600">
                                            {{ $candidate['profile_summary'] ?? '-' }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Experience
                                        </p>
                                        <p class="mt-2 text-sm leading-6 text-slate-600">
                                            {{ $candidate['experience_summary'] ?? '-' }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Skills
                                        </p>

                                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                            @forelse (($candidate['skills'] ?? []) as $skill)
                                                <div class="rounded-2xl border border-sky-100 bg-sky-50/50 p-3">
                                                    <p class="text-sm font-semibold text-slate-800">
                                                        {{ $skill['name'] ?? '-' }}
                                                    </p>
                                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                                        {{ $skill['evidence'] ?? 'Tidak ada evidence.' }}
                                                    </p>
                                                </div>
                                            @empty
                                                <p class="text-sm text-slate-500">Tidak ada skill yang dapat diekstrak.</p>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Education
                                        </p>

                                        <div class="mt-3 space-y-3">
                                            @forelse (($candidate['education'] ?? []) as $education)
                                                <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-3">
                                                    <p class="text-sm font-semibold text-slate-800">
                                                        {{ $education['qualification'] ?? '-' }}
                                                    </p>
                                                    <p class="mt-1 text-xs text-slate-500">
                                                        {{ $education['institution'] ?? '-' }}
                                                    </p>
                                                    <p class="mt-2 text-xs leading-5 text-slate-500">
                                                        {{ $education['relevance'] ?? '-' }}
                                                    </p>
                                                </div>
                                            @empty
                                                <p class="text-sm text-slate-500">Tidak ada pendidikan yang terdeteksi.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </x-wirekit::card.body>
                        </x-wirekit::card>

                        <x-wirekit::card>
                            <x-wirekit::card.header>
                                <h3 class="text-lg font-semibold text-slate-900">
                                    Alignment
                                </h3>
                            </x-wirekit::card.header>

                            <x-wirekit::card.body>
                                <div class="space-y-5">
                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Position
                                        </p>
                                        <div class="mt-2">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass[$positionAlignment['status'] ?? 'not_assessed'] ?? $statusClass['not_assessed'] }}">
                                                {{ $statusLabel[$positionAlignment['status'] ?? 'not_assessed'] ?? 'Belum dinilai' }}
                                            </span>
                                        </div>
                                        <p class="mt-2 text-sm leading-5 text-slate-600">
                                            {{ $positionAlignment['summary'] ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="border-t border-slate-100 pt-5">
                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Company
                                        </p>
                                        <div class="mt-2">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass[$companyAlignment['status'] ?? 'not_assessed'] ?? $statusClass['not_assessed'] }}">
                                                {{ $statusLabel[$companyAlignment['status'] ?? 'not_assessed'] ?? 'Belum dinilai' }}
                                            </span>
                                        </div>
                                        <p class="mt-2 text-sm leading-5 text-slate-600">
                                            {{ $companyAlignment['summary'] ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </x-wirekit::card.body>
                        </x-wirekit::card>

                    </div>

                    <div class="grid gap-5 lg:grid-cols-2">

                        <x-wirekit::card>
                            <x-wirekit::card.header>
                                <h3 class="text-lg font-semibold text-slate-900">
                                    Requirement Analysis
                                </h3>
                            </x-wirekit::card.header>

                            <x-wirekit::card.body>
                                <div class="space-y-3">
                                    @forelse (($positionAlignment['requirements'] ?? []) as $item)
                                        <div class="rounded-2xl border border-slate-100 bg-white p-4 ring-1 ring-slate-100">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <p class="text-sm font-semibold text-slate-800">
                                                    {{ $item['requirement'] ?? '-' }}
                                                </p>
                                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $requirementClass[$item['status'] ?? 'not_assessed'] ?? $requirementClass['not_assessed'] }}">
                                                    {{ $requirementLabel[$item['status'] ?? 'not_assessed'] ?? 'Belum dinilai' }}
                                                </span>
                                            </div>

                                            <p class="mt-2 text-xs leading-5 text-slate-600">
                                                <span class="font-medium text-slate-700">Evidence:</span>
                                                {{ $item['evidence'] ?? '-' }}
                                            </p>

                                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                                {{ $item['notes'] ?? '-' }}
                                            </p>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">
                                            Tidak ada requirement posisi yang dianalisis.
                                        </div>
                                    @endforelse
                                </div>
                            </x-wirekit::card.body>
                        </x-wirekit::card>

                        <x-wirekit::card>
                            <x-wirekit::card.header>
                                <h3 class="text-lg font-semibold text-slate-900">
                                    Jobdesk Alignment
                                </h3>
                            </x-wirekit::card.header>

                            <x-wirekit::card.body>
                                <div class="space-y-3">
                                    @forelse (($positionAlignment['jobdesk_alignment'] ?? []) as $item)
                                        <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <p class="text-sm font-semibold text-slate-800">
                                                    {{ $item['requirement'] ?? '-' }}
                                                </p>
                                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $requirementClass[$item['status'] ?? 'not_assessed'] ?? $requirementClass['not_assessed'] }}">
                                                    {{ $requirementLabel[$item['status'] ?? 'not_assessed'] ?? 'Belum dinilai' }}
                                                </span>
                                            </div>

                                            <p class="mt-2 text-xs leading-5 text-slate-600">
                                                <span class="font-medium text-slate-700">Evidence:</span>
                                                {{ $item['evidence'] ?? '-' }}
                                            </p>

                                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                                {{ $item['notes'] ?? '-' }}
                                            </p>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">
                                            Tidak ada jobdesk posisi yang tersedia.
                                        </div>
                                    @endforelse
                                </div>
                            </x-wirekit::card.body>
                        </x-wirekit::card>

                    </div>

                    <div class="grid gap-5 lg:grid-cols-2">

                        <x-wirekit::card>
                            <x-wirekit::card.header>
                                <h3 class="text-lg font-semibold text-slate-900">
                                    Company Criteria
                                </h3>
                            </x-wirekit::card.header>

                            <x-wirekit::card.body>
                                <div class="space-y-3">
                                    @forelse (($companyAlignment['criteria'] ?? []) as $item)
                                        <div class="rounded-2xl border border-slate-100 bg-white p-4 ring-1 ring-slate-100">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <p class="text-sm font-semibold text-slate-800">
                                                    {{ $item['requirement'] ?? '-' }}
                                                </p>
                                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $requirementClass[$item['status'] ?? 'not_assessed'] ?? $requirementClass['not_assessed'] }}">
                                                    {{ $requirementLabel[$item['status'] ?? 'not_assessed'] ?? 'Belum dinilai' }}
                                                </span>
                                            </div>
                                            <p class="mt-2 text-xs leading-5 text-slate-600">
                                                <span class="font-medium text-slate-700">Evidence:</span>
                                                {{ $item['evidence'] ?? '-' }}
                                            </p>
                                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                                {{ $item['notes'] ?? '-' }}
                                            </p>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">
                                            Belum ada kriteria perusahaan yang dianalisis.
                                        </div>
                                    @endforelse
                                </div>
                            </x-wirekit::card.body>
                        </x-wirekit::card>

                        <x-wirekit::card>
                            <x-wirekit::card.header>
                                <h3 class="text-lg font-semibold text-slate-900">
                                    Strengths
                                </h3>
                            </x-wirekit::card.header>

                            <x-wirekit::card.body>
                                <div class="space-y-3">
                                    @forelse (($analysisResult['strengths'] ?? []) as $strength)
                                        <div class="flex items-start gap-2 rounded-2xl bg-emerald-50/70 px-3 py-3">
                                            <span class="mt-0.5 text-emerald-600">✓</span>
                                            <p class="text-sm leading-5 text-slate-700">
                                                {{ $strength }}
                                            </p>
                                        </div>
                                    @empty
                                        <p class="text-sm text-slate-500">Tidak ada strength yang terdeteksi.</p>
                                    @endforelse
                                </div>
                            </x-wirekit::card.body>
                        </x-wirekit::card>

                    </div>

                    <x-wirekit::card>
                        <x-wirekit::card.header>
                            <h3 class="text-lg font-semibold text-slate-900">
                                Gap & Hal yang Perlu Diverifikasi
                            </h3>
                        </x-wirekit::card.header>

                        <x-wirekit::card.body>
                            <div class="grid gap-3 lg:grid-cols-2">
                                @forelse (($analysisResult['gaps'] ?? []) as $gap)
                                    <div class="rounded-2xl border border-amber-100 bg-amber-50/60 p-4">
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $gap['item'] ?? '-' }}
                                        </p>
                                        <p class="mt-2 text-xs leading-5 text-slate-600">
                                            <span class="font-medium text-slate-700">Evidence:</span>
                                            {{ $gap['evidence'] ?? '-' }}
                                        </p>
                                        <p class="mt-2 text-xs leading-5 text-slate-500">
                                            {{ $gap['impact'] ?? '-' }}
                                        </p>
                                    </div>
                                @empty
                                    <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">
                                        Tidak ada gap yang terdeteksi dari informasi CV.
                                    </div>
                                @endforelse
                            </div>

                            <div class="mt-5 border-t border-slate-100 pt-5">
                                <p class="text-sm font-semibold text-slate-800">
                                    Verification Checklist
                                </p>

                                <div class="mt-3 grid gap-2 lg:grid-cols-2">
                                    @forelse (($analysisResult['verification_items'] ?? []) as $item)
                                        <div class="flex items-start gap-2 rounded-xl bg-slate-50 px-3 py-2.5">
                                            <span class="mt-0.5 text-[#30AFFF]">•</span>
                                            <p class="text-sm leading-5 text-slate-600">
                                                {{ $item }}
                                            </p>
                                        </div>
                                    @empty
                                        <p class="text-sm text-slate-500">
                                            Tidak ada item tambahan yang perlu diverifikasi.
                                        </p>
                                    @endforelse
                                </div>
                            </div>
                        </x-wirekit::card.body>
                    </x-wirekit::card>

                    <x-wirekit::card>
                        <x-wirekit::card.header>
                            <h3 class="text-lg font-semibold text-slate-900">
                                Interview Questions
                            </h3>
                            <p class="mt-1 text-sm text-slate-500">
                                Pertanyaan dibuat dari gap, ambiguity, dan pengalaman yang perlu diverifikasi.
                            </p>
                        </x-wirekit::card.header>

                        <x-wirekit::card.body>
                            <div class="grid gap-3 lg:grid-cols-2">
                                @forelse (($analysisResult['interview_questions'] ?? []) as $index => $question)
                                    <div class="rounded-2xl border border-sky-100 bg-sky-50/50 p-4">
                                        <div class="flex items-start gap-3">
                                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#30AFFF]/10 text-xs font-semibold text-[#168fd8]">
                                                {{ $index + 1 }}
                                            </span>
                                            <p class="text-sm leading-6 text-slate-700">
                                                {{ $question }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-500">
                                        Tidak ada pertanyaan interview tambahan yang dihasilkan.
                                    </p>
                                @endforelse
                            </div>
                        </x-wirekit::card.body>
                    </x-wirekit::card>

                    <x-wirekit::card>
                        <x-wirekit::card.header>
                            <h3 class="text-lg font-semibold text-slate-900">
                                AI Feedback
                            </h3>
                        </x-wirekit::card.header>

                        <x-wirekit::card.body>
                            <p class="whitespace-pre-line text-sm leading-7 text-slate-600">
                                {{ $analysisResult['feedback'] ?? '-' }}
                            </p>
                        </x-wirekit::card.body>
                    </x-wirekit::card>

                </div>
            @endif
        </div>

    </div>
</div>
