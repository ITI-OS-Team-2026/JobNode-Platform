<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const dashboardRoutes = {
    candidate: 'candidate.dashboard',
    employer: 'employer.dashboard',
    admin: 'admin.dashboard',
};

const dashboardRouteName = (role) => dashboardRoutes[role] ?? 'home';
</script>

<template>
    <Head title="JobNode - Developer Job Platform" />

    <div class="min-h-screen bg-slate-950 text-slate-100 selection:bg-emerald-500 selection:text-slate-950 font-body relative overflow-hidden">
        
        <!-- Background decorative glows -->
        <div class="absolute top-[-10%] left-[-20%] w-[600px] h-[600px] rounded-full bg-emerald-500/10 blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-20%] w-[600px] h-[600px] rounded-full bg-sky-500/10 blur-[120px] pointer-events-none"></div>

        <!-- Sleek Navbar -->
        <header class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-6 flex items-center justify-between border-b border-slate-900/50 backdrop-blur-md bg-slate-950/30 sticky top-0">
            <div class="flex items-center gap-3">
                <!-- Logo -->
                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <svg class="w-6 h-6 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <span class="font-display font-bold text-2xl tracking-tight text-white">
                    Job<span class="text-emerald-400">Node</span>
                </span>
            </div>

            <nav class="flex items-center gap-6">
                <Link :href="route('jobs.index')" class="text-sm font-medium text-slate-400 hover:text-white transition">
                    Browse Jobs
                </Link>

                <div v-if="canLogin" class="flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route(dashboardRouteName($page.props.auth.user.role))"
                        class="px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-500 text-slate-950 hover:bg-emerald-400 transition-all duration-200 shadow-md shadow-emerald-500/10"
                    >
                        Go to Dashboard
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-sm font-medium text-slate-400 hover:text-white transition"
                        >
                            Log in
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-4 py-2 rounded-xl text-sm font-semibold bg-slate-900 border border-slate-800 text-white hover:bg-slate-850 hover:border-slate-700 transition"
                        >
                            Sign Up
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <!-- Hero Section -->
        <main class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-20 lg:py-32 flex flex-col items-center text-center">
            
            <!-- Announcement Badge -->
            <!-- <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-900/80 border border-slate-800/80 text-xs font-semibold text-emerald-400 mb-8 backdrop-blur-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Phase 7.3 Release: Secure File & Profile Handler
            </div> -->

            <!-- Headings -->
            <h1 class="font-display font-extrabold text-5xl lg:text-7xl text-white tracking-tight max-w-4xl leading-tight">
                Deploy Your Next <br />
                <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-sky-400 bg-clip-text text-transparent">
                    Career Node
                </span>
            </h1>
            <p class="font-body text-slate-400 text-lg lg:text-xl max-w-2xl mt-6 leading-relaxed">
                Connect directly with verified tech employers. Secure applications, robust developer profiles, and real-time dashboard analytics.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-wrap justify-center gap-4 mt-10">
                <Link
                    :href="route('jobs.index')"
                    class="px-8 py-4 rounded-xl text-base font-semibold bg-gradient-to-r from-emerald-500 to-teal-400 text-slate-950 hover:from-emerald-450 hover:to-teal-450 transition-all duration-200 shadow-xl shadow-emerald-500/10 hover:shadow-emerald-500/20 transform hover:-translate-y-0.5"
                >
                    Browse Open Roles
                </Link>
                <Link
                    v-if="!$page.props.auth.user"
                    :href="route('register')"
                    class="px-8 py-4 rounded-xl text-base font-semibold bg-slate-900 border border-slate-800 text-white hover:bg-slate-850 hover:border-slate-700 transition transform hover:-translate-y-0.5"
                >
                    Post an Opening
                </Link>
            </div>

            <!-- Stats/Highlights Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl w-full mt-24 border-t border-slate-900/50 pt-16">
                
                <!-- Stat 1 -->
                <div class="bg-slate-950/40 p-6 rounded-2xl border border-slate-900/60 backdrop-blur-sm text-left group hover:border-slate-800 transition">
                    <div class="h-10 w-10 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-4 group-hover:scale-110 transition duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-lg text-white mb-2">Secure Resume Vault</h3>
                    <p class="font-body text-slate-400 text-sm">Resumes are isolated on private disks with tokenized server-side download authorizations.</p>
                </div>

                <!-- Stat 2 -->
                <div class="bg-slate-950/40 p-6 rounded-2xl border border-slate-900/60 backdrop-blur-sm text-left group hover:border-slate-800 transition">
                    <div class="h-10 w-10 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center mb-4 group-hover:scale-110 transition duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-lg text-white mb-2">Employer Unlocks</h3>
                    <p class="font-body text-slate-400 text-sm">Gated access ensures companies lock and unlock developers' resumes through integrated channels.</p>
                </div>

                <!-- Stat 3 -->
                <div class="bg-slate-950/40 p-6 rounded-2xl border border-slate-900/60 backdrop-blur-sm text-left group hover:border-slate-800 transition">
                    <div class="h-10 w-10 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center mb-4 group-hover:scale-110 transition duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-lg text-white mb-2">Admin Moderation</h3>
                    <p class="font-body text-slate-400 text-sm">All listings undergo approval loops to maintain clean, verified engineering opportunities.</p>
                </div>

            </div>

        </main>

        <!-- Footer -->
        <footer class="relative z-10 border-t border-slate-900 py-12 text-center text-sm text-slate-500">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <p>&copy; 2026 JobNode. Built on Laravel + Vue + Inertia.</p>
                <div class="flex items-center gap-6">
                    <Link :href="route('jobs.index')" class="hover:text-slate-350 transition">Open Board</Link>
                    <a href="https://laravel.com" class="hover:text-slate-350 transition" target="_blank" rel="noopener">Laravel Framework</a>
                </div>
            </div>
        </footer>

    </div>
</template>
