<x-layouts::app title="Login">

    <div class="min-h-screen bg-white">

        {{-- Logo --}}
        <div class="absolute left-6 top-6 z-20 sm:left-8 sm:top-8">
            <a href="{{ url('/') }}" class="flex items-center gap-2" wire:navigate>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#30AFFF]">
                    <x-wirekit::image src="/assets/logo-inovindo.webp"
                        alt="A product shot" ratio="1/1" fit="contain" rounded />
                </div>

                <span class="text-lg font-bold tracking-tight text-slate-900">
                    {{ config('app.name') }}
                </span>
            </a>
        </div>


        {{-- Main --}}
        <div class="grid min-h-screen lg:grid-cols-2">

            <section class="flex min-h-screen items-center justify-center px-6 py-24 sm:px-10 lg:px-16">

                {{ $slot }}

            </section>


            {{-- =========================
            RIGHT - VISUAL
            ========================== --}}
            <section class="relative hidden overflow-hidden lg:block">

                {{-- Background --}}
                <div class="absolute inset-0 bg-[#30AFFF]"></div>

                {{-- Soft shape --}}
                <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-[#92EEFF]/40 blur-3xl"></div>

                <div class="absolute -bottom-32 -left-20 h-80 w-80 rounded-full bg-[#D8FFC5]/40 blur-3xl"></div>


                {{-- Illustration --}}
                <div class="relative flex h-full items-center justify-center p-16">

                    <div class="w-full max-w-xl">

                        <x-wirekit::stack gap="8">

                            <div>

                                <span
                                    class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-medium text-white">
                                    HR & Work Management
                                </span>

                                <h2 class="mt-5 max-w-lg text-4xl font-bold leading-tight text-white">
                                    Satu tempat untuk mengelola
                                    <span class="text-[#D8FFC5]">
                                        tim dan pekerjaan
                                    </span>
                                    Anda.
                                </h2>

                                <p class="mt-4 max-w-md text-base leading-7 text-white/80">
                                    Pantau aktivitas tim, kelola pekerjaan,
                                    dan dapatkan insight dari data dalam satu
                                    platform yang terintegrasi.
                                </p>

                            </div>


                            {{-- Illustration card --}}
                            <div class="relative">

                                <div class="rounded-3xl bg-white/10 p-4 backdrop-blur-md">

                                    <div class="rounded-2xl bg-white p-6 shadow-2xl">

                                        <x-wirekit::stack gap="5">

                                            {{-- Fake dashboard header --}}
                                            <div class="flex items-center justify-between">

                                                <div>
                                                    <div class="h-3 w-24 rounded bg-slate-200"></div>
                                                    <div class="mt-2 h-2 w-16 rounded bg-slate-100"></div>
                                                </div>

                                                <div class="h-9 w-9 rounded-xl bg-[#92EEFF]"></div>

                                            </div>


                                            {{-- Stat cards --}}
                                            <div class="grid grid-cols-3 gap-3">

                                                <div class="rounded-xl bg-[#F4FBFF] p-4">
                                                    <div class="h-2 w-10 rounded bg-[#92EEFF]"></div>
                                                    <div class="mt-3 h-5 w-14 rounded bg-slate-800"></div>
                                                </div>

                                                <div class="rounded-xl bg-[#F7FFF3] p-4">
                                                    <div class="h-2 w-10 rounded bg-[#D8FFC5]"></div>
                                                    <div class="mt-3 h-5 w-14 rounded bg-slate-800"></div>
                                                </div>

                                                <div class="rounded-xl bg-[#FFF9ED] p-4">
                                                    <div class="h-2 w-10 rounded bg-[#FFA239]"></div>
                                                    <div class="mt-3 h-5 w-14 rounded bg-slate-800"></div>
                                                </div>

                                            </div>


                                            {{-- Fake chart --}}
                                            <div class="rounded-xl border border-slate-100 p-5">

                                                <div class="h-2 w-20 rounded bg-slate-200"></div>

                                                <svg viewBox="0 0 500 160" class="mt-5 w-full" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M10 130C65 120 70 90 115 100C155 110 170 65 220 75C270 85 275 45 320 55C370 65 390 25 450 35"
                                                        stroke="#30AFFF" stroke-width="5" stroke-linecap="round" />

                                                    <path
                                                        d="M10 130C65 120 70 90 115 100C155 110 170 65 220 75C270 85 275 45 320 55C370 65 390 25 450 35V150H10V130Z"
                                                        fill="#92EEFF" opacity="0.25" />
                                                </svg>

                                            </div>


                                            {{-- Fake task rows --}}
                                            <x-wirekit::stack gap="3">

                                                <div class="flex items-center gap-3">
                                                    <div class="h-8 w-8 rounded-lg bg-[#C4F7CA]"></div>

                                                    <div class="flex-1">
                                                        <div class="h-2.5 w-32 rounded bg-slate-200"></div>
                                                        <div class="mt-1.5 h-2 w-20 rounded bg-slate-100"></div>
                                                    </div>

                                                    <div class="h-2 w-12 rounded bg-[#D8FFC5]"></div>
                                                </div>

                                                <div class="flex items-center gap-3">
                                                    <div class="h-8 w-8 rounded-lg bg-[#92EEFF]"></div>

                                                    <div class="flex-1">
                                                        <div class="h-2.5 w-28 rounded bg-slate-200"></div>
                                                        <div class="mt-1.5 h-2 w-16 rounded bg-slate-100"></div>
                                                    </div>

                                                    <div class="h-2 w-12 rounded bg-[#92EEFF]"></div>
                                                </div>

                                                <div class="flex items-center gap-3">
                                                    <div class="h-8 w-8 rounded-lg bg-[#C4F7CA]"></div>

                                                    <div class="flex-1">
                                                        <div class="h-2.5 w-36 rounded bg-slate-200"></div>
                                                        <div class="mt-1.5 h-2 w-24 rounded bg-slate-100"></div>
                                                    </div>

                                                    <div class="h-2 w-12 rounded bg-[#D8FFC5]"></div>
                                                </div>

                                            </x-wirekit::stack>

                                        </x-wirekit::stack>

                                    </div>

                                </div>

                            </div>

                        </x-wirekit::stack>

                    </div>

                </div>

            </section>

        </div>

    </div>

</x-layouts::app>