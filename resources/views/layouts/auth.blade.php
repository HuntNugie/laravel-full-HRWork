<x-layouts::app :title="$title">

    <div class="min-h-screen bg-white">

        {{-- =========================================================
        BRAND
        ========================================================== --}}
        <div class="absolute left-6 top-6 z-30 sm:left-8 sm:top-8">

            <a
                href="{{ url('/') }}"
                wire:navigate
                class="flex items-center gap-2"
            >

                <div
                    class="flex h-9 w-9 items-center justify-center
                           rounded-xl bg-[#30AFFF]"
                >
                    <x-wirekit::image
                        src="/assets/logo-inovindo.webp"
                        alt="Inovindo"
                        ratio="1/1"
                        fit="contain"
                        rounded
                    />
                </div>

                <span class="text-lg font-bold tracking-tight text-slate-900">
                    {{ config('app.name') }}
                </span>

            </a>

        </div>


        {{-- =========================================================
        MAIN
        ========================================================== --}}
        <div class="grid min-h-screen lg:grid-cols-2">


            {{-- =====================================================
            LEFT - LOGIN CONTENT
            ====================================================== --}}
            <section
                class="flex min-h-screen items-center justify-center
                       px-6 py-24 sm:px-10 lg:px-16"
            >

                {{ $slot }}

            </section>


            {{-- =====================================================
            RIGHT - HERO VISUAL
            ====================================================== --}}
            <section
                class="relative hidden min-h-screen overflow-hidden
                       bg-[#30AFFF] lg:block"
            >

                {{-- =================================================
                BACKGROUND
                ================================================== --}}
                <div class="absolute inset-0 bg-[#30AFFF]"></div>


                {{-- Top glow --}}
                <div
                    class="absolute -right-20 -top-20 h-72 w-72
                           animate-login-pulse rounded-full
                           bg-[#92EEFF]/40 blur-3xl"
                ></div>


                {{-- Bottom glow --}}
                <div
                    class="absolute -bottom-32 -left-20 h-80 w-80
                           animate-login-pulse rounded-full
                           bg-[#D8FFC5]/35 blur-3xl"
                    style="animation-delay: -2.5s;"
                ></div>


                {{-- Decorative dots --}}
                <div
                    class="absolute right-[18%] top-[18%]
                           h-2.5 w-2.5 rounded-full bg-white/30"
                ></div>

                <div
                    class="absolute bottom-[20%] left-[14%]
                           h-2 w-2 rounded-full bg-[#D8FFC5]/70"
                ></div>

                <div
                    class="absolute bottom-[17%] right-[12%]
                           h-3 w-3 rounded-full bg-[#92EEFF]/60"
                ></div>


                {{-- =================================================
                CONTENT
                ================================================== --}}
                <div
                    class="relative z-10 flex h-full flex-col
                           justify-between px-10 py-14 xl:px-14"
                >


                    {{-- =================================================
                    HERO TEXT
                    ================================================== --}}
                    <div class="max-w-lg">

                        <span
                            class="inline-flex items-center gap-2
                                   rounded-full border border-white/10
                                   bg-white/10 px-3 py-1.5
                                   text-xs font-medium text-white
                                   backdrop-blur-sm"
                        >

                            <span
                                class="h-1.5 w-1.5 rounded-full
                                       bg-[#D8FFC5]"
                            ></span>

                            HR & Work Management

                        </span>


                        <h2
                            class="mt-4 max-w-lg text-3xl font-bold
                                   leading-tight tracking-tight text-white
                                   xl:text-4xl"
                        >
                            Satu tempat untuk mengelola
                            <span class="text-[#D8FFC5]">
                                tim dan pekerjaan
                            </span>
                            Anda.
                        </h2>


                        <p
                            class="mt-4 max-w-md text-sm leading-6
                                   text-white/75 xl:text-base xl:leading-7"
                        >
                            Kelola karyawan, pekerjaan, project,
                            dan performa tim dalam satu platform
                            yang terintegrasi.
                        </p>

                    </div>


                    {{-- =================================================
                    ILLUSTRATION AREA
                    ================================================== --}}
                    <div
                        class="relative flex flex-1 items-center
                               justify-center py-6"
                    >

                        {{-- Decorative orbit --}}
                        <div
                            class="absolute h-[21rem] w-[21rem]
                                   rounded-full border border-white/10"
                        ></div>

                        <div
                            class="absolute h-[17rem] w-[17rem]
                                   rounded-full border border-dashed
                                   border-white/10"
                        ></div>


                        {{-- =================================================
                        MAIN FLOATING CARD
                        ================================================== --}}
                        <div
                            class="relative z-10 w-full max-w-[440px]
                                   animate-login-float"
                        >

                            {{-- Glass frame --}}
                            <div
                                class="rounded-[1.75rem] border border-white/15
                                       bg-white/10 p-2.5 shadow-2xl
                                       backdrop-blur-xl"
                            >

                                {{-- White board --}}
                                <div
                                    class="overflow-hidden rounded-[1.4rem]
                                           bg-white shadow-2xl"
                                >

                                    {{-- =================================================
                                    BOARD HEADER
                                    ================================================== --}}
                                    <div
                                        class="flex items-center
                                               justify-between border-b
                                               border-slate-100 px-5 py-3.5"
                                    >

                                        <div class="flex items-center gap-2.5">

                                            <div
                                                class="flex h-8 w-8
                                                       items-center justify-center
                                                       rounded-lg bg-[#EAF9FF]"
                                            >
                                                <div
                                                    class="h-3.5 w-3.5
                                                           rounded-md bg-[#30AFFF]"
                                                ></div>
                                            </div>

                                            <div>

                                                <p
                                                    class="text-xs font-semibold
                                                           text-slate-900"
                                                >
                                                    Work Overview
                                                </p>

                                                <p
                                                    class="mt-0.5 text-[10px]
                                                           text-slate-400"
                                                >
                                                    Team performance
                                                </p>

                                            </div>

                                        </div>


                                        <div class="flex gap-1.5">

                                            <div
                                                class="h-1.5 w-1.5
                                                       rounded-full bg-slate-200"
                                            ></div>

                                            <div
                                                class="h-1.5 w-1.5
                                                       rounded-full bg-slate-300"
                                            ></div>

                                            <div
                                                class="h-1.5 w-1.5
                                                       rounded-full bg-[#30AFFF]"
                                            ></div>

                                        </div>

                                    </div>


                                    <div class="p-5">


                                        {{-- =================================================
                                        STATS
                                        ================================================== --}}
                                        <div class="grid grid-cols-3 gap-2.5">


                                            {{-- Employees --}}
                                            <div
                                                class="rounded-xl
                                                       bg-[#F4FBFF] p-3"
                                            >

                                                <div
                                                    class="flex items-center
                                                           justify-between"
                                                >

                                                    <span
                                                        class="text-[9px]
                                                               font-medium
                                                               text-slate-400"
                                                    >
                                                        Employees
                                                    </span>

                                                    <span
                                                        class="flex h-6 w-6
                                                               items-center
                                                               justify-center
                                                               rounded-lg
                                                               bg-[#92EEFF]/50"
                                                    >
                                                        <span
                                                            class="h-2 w-2
                                                                   rounded-full
                                                                   bg-[#30AFFF]"
                                                        ></span>
                                                    </span>

                                                </div>

                                                <p
                                                    class="mt-2 text-lg font-bold
                                                           text-slate-900"
                                                >
                                                    128
                                                </p>

                                                <p
                                                    class="mt-0.5 text-[9px]
                                                           text-emerald-500"
                                                >
                                                    +8 this month
                                                </p>

                                            </div>


                                            {{-- Tasks --}}
                                            <div
                                                class="rounded-xl
                                                       bg-[#F7FFF3] p-3"
                                            >

                                                <div
                                                    class="flex items-center
                                                           justify-between"
                                                >

                                                    <span
                                                        class="text-[9px]
                                                               font-medium
                                                               text-slate-400"
                                                    >
                                                        Tasks
                                                    </span>

                                                    <span
                                                        class="flex h-6 w-6
                                                               items-center
                                                               justify-center
                                                               rounded-lg
                                                               bg-[#D8FFC5]"
                                                    >
                                                        <span
                                                            class="h-2 w-2
                                                                   rounded-full
                                                                   bg-emerald-400"
                                                        ></span>
                                                    </span>

                                                </div>

                                                <p
                                                    class="mt-2 text-lg font-bold
                                                           text-slate-900"
                                                >
                                                    84
                                                </p>

                                                <p
                                                    class="mt-0.5 text-[9px]
                                                           text-emerald-500"
                                                >
                                                    72% completed
                                                </p>

                                            </div>


                                            {{-- Performance --}}
                                            <div
                                                class="rounded-xl
                                                       bg-[#FFF9ED] p-3"
                                            >

                                                <div
                                                    class="flex items-center
                                                           justify-between"
                                                >

                                                    <span
                                                        class="text-[9px]
                                                               font-medium
                                                               text-slate-400"
                                                    >
                                                        Performance
                                                    </span>

                                                    <span
                                                        class="flex h-6 w-6
                                                               items-center
                                                               justify-center
                                                               rounded-lg
                                                               bg-[#FFA239]/20"
                                                    >
                                                        <span
                                                            class="h-2 w-2
                                                                   rounded-full
                                                                   bg-[#FFA239]"
                                                        ></span>
                                                    </span>

                                                </div>

                                                <p
                                                    class="mt-2 text-lg font-bold
                                                           text-slate-900"
                                                >
                                                    86%
                                                </p>

                                                <p
                                                    class="mt-0.5 text-[9px]
                                                           text-emerald-500"
                                                >
                                                    +4.6% this month
                                                </p>

                                            </div>

                                        </div>


                                        {{-- =================================================
                                        MIDDLE
                                        ================================================== --}}
                                        <div
                                            class="mt-3 grid gap-3
                                                   sm:grid-cols-[1.2fr_0.8fr]"
                                        >


                                            {{-- Project progress --}}
                                            <div
                                                class="rounded-xl border
                                                       border-slate-100 p-4"
                                            >

                                                <div
                                                    class="flex items-center
                                                           justify-between"
                                                >

                                                    <div>

                                                        <p
                                                            class="text-[10px]
                                                                   font-semibold
                                                                   text-slate-900"
                                                        >
                                                            Project Progress
                                                        </p>

                                                        <p
                                                            class="mt-0.5 text-[9px]
                                                                   text-slate-400"
                                                        >
                                                            Development Team
                                                        </p>

                                                    </div>

                                                    <span
                                                        class="text-[10px]
                                                               font-semibold
                                                               text-[#30AFFF]"
                                                    >
                                                        78%
                                                    </span>

                                                </div>


                                                {{-- Progress --}}
                                                <div
                                                    class="mt-4 h-1.5
                                                           overflow-hidden
                                                           rounded-full
                                                           bg-slate-100"
                                                >

                                                    <div
                                                        class="h-full w-[78%]
                                                               rounded-full
                                                               bg-[#30AFFF]"
                                                    ></div>

                                                </div>


                                                {{-- Mini chart --}}
                                                <div
                                                    class="mt-4 flex h-16
                                                           items-end gap-1.5"
                                                >

                                                    <div
                                                        class="h-[35%] flex-1
                                                               rounded-t-sm
                                                               bg-[#D8FFC5]"
                                                    ></div>

                                                    <div
                                                        class="h-[52%] flex-1
                                                               rounded-t-sm
                                                               bg-[#92EEFF]"
                                                    ></div>

                                                    <div
                                                        class="h-[44%] flex-1
                                                               rounded-t-sm
                                                               bg-[#D8FFC5]"
                                                    ></div>

                                                    <div
                                                        class="h-[68%] flex-1
                                                               rounded-t-sm
                                                               bg-[#92EEFF]"
                                                    ></div>

                                                    <div
                                                        class="h-[82%] flex-1
                                                               rounded-t-sm
                                                               bg-[#30AFFF]"
                                                    ></div>

                                                    <div
                                                        class="h-[72%] flex-1
                                                               rounded-t-sm
                                                               bg-[#92EEFF]"
                                                    ></div>

                                                </div>

                                            </div>


                                            {{-- Team --}}
                                            <div
                                                class="rounded-xl border
                                                       border-slate-100 p-4"
                                            >

                                                <div
                                                    class="flex items-center
                                                           justify-between"
                                                >

                                                    <div>

                                                        <p
                                                            class="text-[10px]
                                                                   font-semibold
                                                                   text-slate-900"
                                                        >
                                                            Team
                                                        </p>

                                                        <p
                                                            class="mt-0.5 text-[9px]
                                                                   text-slate-400"
                                                        >
                                                            Active members
                                                        </p>

                                                    </div>

                                                    <span
                                                        class="text-[10px]
                                                               font-semibold
                                                               text-slate-500"
                                                    >
                                                        12
                                                    </span>

                                                </div>


                                                {{-- Avatars --}}
                                                <div class="mt-4 flex items-center">

                                                    <div
                                                        class="flex h-7 w-7
                                                               items-center
                                                               justify-center
                                                               rounded-full
                                                               border-2 border-white
                                                               bg-[#30AFFF]
                                                               text-[8px]
                                                               font-bold text-white"
                                                    >
                                                        A
                                                    </div>

                                                    <div
                                                        class="-ml-2 flex h-7 w-7
                                                               items-center
                                                               justify-center
                                                               rounded-full
                                                               border-2 border-white
                                                               bg-[#D8FFC5]
                                                               text-[8px]
                                                               font-bold text-slate-700"
                                                    >
                                                        B
                                                    </div>

                                                    <div
                                                        class="-ml-2 flex h-7 w-7
                                                               items-center
                                                               justify-center
                                                               rounded-full
                                                               border-2 border-white
                                                               bg-[#FFA239]
                                                               text-[8px]
                                                               font-bold text-white"
                                                    >
                                                        C
                                                    </div>

                                                    <div
                                                        class="-ml-2 flex h-7 w-7
                                                               items-center
                                                               justify-center
                                                               rounded-full
                                                               border-2 border-white
                                                               bg-slate-800
                                                               text-[8px]
                                                               font-bold text-white"
                                                    >
                                                        +9
                                                    </div>

                                                </div>


                                                <div
                                                    class="mt-4 rounded-lg
                                                           bg-slate-50 p-2.5"
                                                >

                                                    <p
                                                        class="text-[9px]
                                                               text-slate-400"
                                                    >
                                                        On-time completion
                                                    </p>

                                                    <p
                                                        class="mt-0.5 text-base
                                                               font-bold text-slate-900"
                                                    >
                                                        91%
                                                    </p>

                                                </div>

                                            </div>

                                        </div>


                                        {{-- =================================================
                                        RECENT WORK
                                        ================================================== --}}
                                        <div class="mt-4">

                                            <div
                                                class="mb-2.5 flex items-center
                                                       justify-between"
                                            >

                                                <p
                                                    class="text-[10px]
                                                           font-semibold
                                                           text-slate-900"
                                                >
                                                    Recent Work
                                                </p>

                                                <span
                                                    class="text-[9px]
                                                           font-medium text-slate-400"
                                                >
                                                    Today
                                                </span>

                                            </div>


                                            <div class="space-y-2">


                                                {{-- Authentication --}}
                                                <div
                                                    class="flex items-center gap-2.5
                                                           rounded-lg border
                                                           border-slate-100 p-2.5"
                                                >

                                                    <div
                                                        class="flex h-7 w-7
                                                               shrink-0
                                                               items-center
                                                               justify-center
                                                               rounded-lg
                                                               bg-[#D8FFC5]"
                                                    >
                                                        <span
                                                            class="h-2 w-2
                                                                   rounded-full
                                                                   bg-emerald-500"
                                                        ></span>
                                                    </div>

                                                    <div class="min-w-0 flex-1">

                                                        <p
                                                            class="truncate text-[10px]
                                                                   font-medium
                                                                   text-slate-800"
                                                        >
                                                            Authentication Module
                                                        </p>

                                                        <p
                                                            class="mt-0.5 text-[9px]
                                                                   text-slate-400"
                                                        >
                                                            Completed by Backend Team
                                                        </p>

                                                    </div>

                                                    <span
                                                        class="text-[9px]
                                                               font-semibold
                                                               text-emerald-500"
                                                    >
                                                        Done
                                                    </span>

                                                </div>


                                                {{-- Attendance --}}
                                                <div
                                                    class="flex items-center gap-2.5
                                                           rounded-lg border
                                                           border-slate-100 p-2.5"
                                                >

                                                    <div
                                                        class="flex h-7 w-7
                                                               shrink-0
                                                               items-center
                                                               justify-center
                                                               rounded-lg
                                                               bg-[#92EEFF]/50"
                                                    >
                                                        <span
                                                            class="h-2 w-2
                                                                   rounded-full
                                                                   bg-[#30AFFF]"
                                                        ></span>
                                                    </div>

                                                    <div class="min-w-0 flex-1">

                                                        <p
                                                            class="truncate text-[10px]
                                                                   font-medium
                                                                   text-slate-800"
                                                        >
                                                            Attendance Module
                                                        </p>

                                                        <p
                                                            class="mt-0.5 text-[9px]
                                                                   text-slate-400"
                                                        >
                                                            Progress updated by Andi
                                                        </p>

                                                    </div>

                                                    <span
                                                        class="text-[9px]
                                                               font-semibold
                                                               text-[#30AFFF]"
                                                    >
                                                        72%
                                                    </span>

                                                </div>


                                                {{-- Payroll --}}
                                                <div
                                                    class="flex items-center gap-2.5
                                                           rounded-lg border
                                                           border-slate-100 p-2.5"
                                                >

                                                    <div
                                                        class="flex h-7 w-7
                                                               shrink-0
                                                               items-center
                                                               justify-center
                                                               rounded-lg
                                                               bg-[#FFF3DE]"
                                                    >
                                                        <span
                                                            class="h-2 w-2
                                                                   rounded-full
                                                                   bg-[#FFA239]"
                                                        ></span>
                                                    </div>

                                                    <div class="min-w-0 flex-1">

                                                        <p
                                                            class="truncate text-[10px]
                                                                   font-medium
                                                                   text-slate-800"
                                                        >
                                                            Payroll Module
                                                        </p>

                                                        <p
                                                            class="mt-0.5 text-[9px]
                                                                   text-slate-400"
                                                        >
                                                            Waiting for review
                                                        </p>

                                                    </div>

                                                    <span
                                                        class="text-[9px]
                                                               font-semibold
                                                               text-[#FFA239]"
                                                    >
                                                        Review
                                                    </span>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                        FLOATING PERFORMANCE CARD
                        ================================================== --}}
                        <div
                            class="absolute right-0 top-[18%]
                                   animate-login-float-reverse
                                   rounded-xl border border-white/20
                                   bg-white/90 p-2.5 shadow-xl
                                   backdrop-blur-md"
                        >

                            <div class="flex items-center gap-2.5">

                                <div
                                    class="flex h-8 w-8 items-center
                                           justify-center rounded-lg
                                           bg-[#D8FFC5]"
                                >
                                    <span
                                        class="text-xs font-bold
                                               text-emerald-600"
                                    >
                                        ↑
                                    </span>
                                </div>

                                <div>

                                    <p
                                        class="text-[9px] text-slate-400"
                                    >
                                        Team Performance
                                    </p>

                                    <p
                                        class="text-xs font-bold text-slate-900"
                                    >
                                        +12.4%
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                        FLOATING TASK CARD
                        ================================================== --}}
                        <div
                            class="absolute bottom-[18%] left-0
                                   animate-login-float
                                   rounded-xl border border-white/20
                                   bg-white/90 p-2.5 shadow-xl
                                   backdrop-blur-md"
                            style="animation-delay: -2s;"
                        >

                            <div class="flex items-center gap-2.5">

                                <div
                                    class="flex h-8 w-8 items-center
                                           justify-center rounded-lg
                                           bg-[#92EEFF]/50"
                                >
                                    <span
                                        class="h-2.5 w-2.5 rounded-full
                                               bg-[#30AFFF]"
                                    ></span>
                                </div>

                                <div>

                                    <p
                                        class="text-[9px] text-slate-400"
                                    >
                                        Tasks completed
                                    </p>

                                    <p
                                        class="text-xs font-bold text-slate-900"
                                    >
                                        24 / 28
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </section>

        </div>

    </div>

</x-layouts::app>