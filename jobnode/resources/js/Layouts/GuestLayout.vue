<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: '',
    },
    subtitle: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 font-body relative overflow-hidden">
        <div class="absolute top-[-10%] left-[-20%] w-[600px] h-[600px] rounded-full bg-emerald-500/10 blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-20%] w-[600px] h-[600px] rounded-full bg-sky-500/10 blur-[120px] pointer-events-none"></div>

        <div class="relative z-10 min-h-screen flex flex-col lg:flex-row">
            <div class="hidden lg:flex lg:w-[45%] xl:w-1/2 flex-col justify-between p-12 xl:p-16 border-r border-slate-900/60">
                <Link href="/" class="flex items-center gap-3 group w-fit">
                    <div class="h-11 w-11 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span class="font-display font-bold text-2xl tracking-tight text-white">
                        Job<span class="text-emerald-400">Node</span>
                    </span>
                </Link>

                <div class="max-w-md">
                    <h2 class="font-display text-4xl xl:text-5xl font-bold text-white leading-tight mb-6">
                        Your next career move starts here.
                    </h2>
                    <p class="text-slate-400 text-lg leading-relaxed mb-10">
                        Secure applications, verified employers, and real-time dashboards — built for developers and hiring teams.
                    </p>

                    <ul class="space-y-4">
                        <li class="flex items-start gap-3 text-slate-300">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-400">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </span>
                            <span>Apply to up to 12 roles with one profile</span>
                        </li>
                        <li class="flex items-start gap-3 text-slate-300">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-400">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </span>
                            <span>Track applications and profile completeness</span>
                        </li>
                        <li class="flex items-start gap-3 text-slate-300">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-400">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </span>
                            <span>Employers get analytics and applicant triage</span>
                        </li>
                    </ul>
                </div>

                <p class="text-sm text-slate-600">&copy; {{ new Date().getFullYear() }} JobNode Platform</p>
            </div>

            <div class="flex flex-1 flex-col">
                <header class="lg:hidden flex items-center justify-between px-6 py-5 border-b border-slate-900/50">
                    <Link href="/" class="flex items-center gap-2.5">
                        <div class="h-9 w-9 rounded-lg bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center">
                            <svg class="w-5 h-5 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <span class="font-display font-bold text-lg text-white">
                            Job<span class="text-emerald-400">Node</span>
                        </span>
                    </Link>
                    <Link :href="route('jobs.index')" class="text-sm text-slate-400 hover:text-white transition">
                        Browse Jobs
                    </Link>
                </header>

                <div class="flex flex-1 items-center justify-center px-6 py-10 sm:px-8">
                    <div class="w-full max-w-md">
                        <div v-if="title || subtitle" class="mb-8 text-center lg:text-left">
                            <h1 v-if="title" class="font-display text-3xl font-bold text-white mb-2">{{ title }}</h1>
                            <p v-if="subtitle" class="text-slate-400">{{ subtitle }}</p>
                        </div>

                        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 sm:p-8 backdrop-blur-sm shadow-2xl shadow-black/20">
                            <slot />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
