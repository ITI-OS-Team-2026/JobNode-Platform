<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    ChartBarSquareIcon,
    BriefcaseIcon,
    DocumentTextIcon,
    UserCircleIcon,
    ArrowRightStartOnRectangleIcon,
    Cog6ToothIcon,
    Bars3Icon,
    XMarkIcon,
    MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline';

const page = usePage();
const user = computed(() => page.props.auth.user);
const mobileOpen = ref(false);
const profileOpen = ref(false);

const initials = computed(() => {
    const name = user.value.name || '';
    const parts = name.split(' ');
    if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase();
    return name.substring(0, 2).toUpperCase();
});

const navItems = computed(() => {
    if (!user.value) return [];
    
    if (user.value.role === 'candidate') {
        return [
            { label: 'Browse Jobs', route: 'jobs.index', pattern: 'jobs.*', icon: MagnifyingGlassIcon },
            { label: 'Dashboard', route: 'candidate.dashboard', pattern: 'candidate.dashboard', icon: ChartBarSquareIcon },
            { label: 'My Profile', route: 'candidate.profile', pattern: 'candidate.profile', icon: UserCircleIcon },
            { label: 'Applications', route: 'candidate.applications', pattern: 'candidate.applications', icon: DocumentTextIcon },
        ];
    }
    
    if (user.value.role === 'employer') {
        return [
            { label: 'Dashboard', route: 'employer.dashboard', pattern: 'employer.dashboard', icon: ChartBarSquareIcon },
            { label: 'My Listings', route: 'employer.jobs.index', pattern: 'employer.jobs.*', icon: BriefcaseIcon },
            { label: 'Applications', route: 'employer.applications.index', pattern: 'employer.applications.*', icon: DocumentTextIcon },
            { label: 'Company Settings', route: 'employer.company.profile', pattern: 'employer.company.*', icon: Cog6ToothIcon },
        ];
    }
    
    if (user.value.role === 'admin') {
        return [
            { label: 'Dashboard', route: 'admin.dashboard', pattern: 'admin.dashboard', icon: ChartBarSquareIcon },
            { label: 'Moderation Queue', route: 'admin.jobs.pending', pattern: 'admin.jobs.*', icon: BriefcaseIcon },
        ];
    }
    
    return [];
});

const isActive = (pattern) => route().current(pattern);

const closeProfile = () => { profileOpen.value = false; };
const closeOnEscape = (e) => { if (e.key === 'Escape') closeProfile(); };
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Sticky Navbar -->
        <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-gray-200/60 shadow-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">

                    <!-- Left: Logo + Nav -->
                    <div class="flex items-center gap-8">
                        <!-- Brand Mark -->
                        <Link :href="user ? route(user.role + '.dashboard') : route('home')" class="flex items-center gap-2.5 group">
                            <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                                <svg class="w-4.5 h-4.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <span class="hidden lg:block text-lg font-bold text-gray-900 tracking-tight">
                                Job<span class="text-emerald-600">Node</span>
                            </span>
                        </Link>

                        <!-- Desktop Nav Pills -->
                        <div class="hidden sm:flex items-center gap-1">
                            <Link
                                v-for="item in navItems"
                                :key="item.route"
                                :href="route(item.route)"
                                class="flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                                :class="isActive(item.pattern)
                                    ? 'bg-emerald-50 text-emerald-700 shadow-sm ring-1 ring-emerald-200/60'
                                    : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100/70'"
                            >
                                <component :is="item.icon" class="w-4 h-4 flex-shrink-0" />
                                <span>{{ item.label }}</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Right: Profile + Mobile Toggle -->
                    <div class="flex items-center gap-3">
                        <!-- Profile Dropdown (Desktop) -->
                        <div class="hidden sm:block relative">
                            <button
                                @click="profileOpen = !profileOpen"
                                @keydown="closeOnEscape"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl hover:bg-gray-100/70 transition-all duration-200 group"
                            >
                                <div class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm ring-2 ring-white">
                                    {{ initials }}
                                </div>
                                <div class="hidden lg:block text-left">
                                    <p class="text-sm font-semibold text-gray-800 leading-tight">{{ user.name }}</p>
                                    <p class="text-xs text-gray-400 leading-tight capitalize">{{ user.role }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-transform duration-200" :class="{ 'rotate-180': profileOpen }" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <!-- Dropdown Overlay -->
                            <div v-if="profileOpen" class="fixed inset-0 z-40" @click="closeProfile"></div>

                            <!-- Dropdown Panel -->
                            <Transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="opacity-0 scale-95 -translate-y-1"
                                enter-to-class="opacity-100 scale-100 translate-y-0"
                                leave-active-class="transition ease-in duration-150"
                                leave-from-class="opacity-100 scale-100 translate-y-0"
                                leave-to-class="opacity-0 scale-95 -translate-y-1"
                            >
                                <div v-if="profileOpen" class="absolute right-0 z-50 mt-2.5 w-56 origin-top-right">
                                    <div class="bg-white rounded-xl shadow-xl ring-1 ring-gray-200/60 border border-gray-100 overflow-hidden">
                                        <!-- User Info Header -->
                                        <div class="px-4 py-3.5 bg-gray-50/80 border-b border-gray-100">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ user.name }}</p>
                                            <p class="text-xs text-gray-500 truncate mt-0.5">{{ user.email }}</p>
                                        </div>
                                        <!-- Menu Items -->
                                        <div class="py-1.5">
                                            <Link
                                                :href="route('profile.edit')"
                                                @click="closeProfile"
                                                class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors"
                                            >
                                                <Cog6ToothIcon class="w-4 h-4 text-gray-400" />
                                                Account Settings
                                            </Link>
                                            <Link
                                                :href="route('logout')"
                                                method="post"
                                                as="button"
                                                @click="closeProfile"
                                                class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors"
                                            >
                                                <ArrowRightStartOnRectangleIcon class="w-4 h-4 text-gray-400" />
                                                Sign Out
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <!-- Mobile Hamburger -->
                        <button
                            @click="mobileOpen = !mobileOpen"
                            class="sm:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                        >
                            <Bars3Icon v-if="!mobileOpen" class="w-5 h-5" />
                            <XMarkIcon v-else class="w-5 h-5" />
                        </button>
                    </div>

                </div>
            </div>

            <!-- Mobile Navigation Panel -->
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div v-if="mobileOpen" class="sm:hidden border-t border-gray-100 bg-white">
                    <div class="px-3 py-3 space-y-1">
                        <Link
                            v-for="item in navItems"
                            :key="item.route"
                            :href="route(item.route)"
                            @click="mobileOpen = false"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200"
                            :class="isActive(item.pattern)
                                ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200/60'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        >
                            <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
                            <span>{{ item.label }}</span>
                        </Link>
                    </div>
                    <!-- Mobile User Section -->
                    <div class="border-t border-gray-100 px-3 py-3">
                        <div class="flex items-center gap-3 px-4 py-2 mb-2">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                {{ initials }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ user.name }}</p>
                                <p class="text-xs text-gray-500">{{ user.email }}</p>
                            </div>
                        </div>
                        <Link
                            :href="route('profile.edit')"
                            @click="mobileOpen = false"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors"
                        >
                            <Cog6ToothIcon class="w-5 h-5" />
                            Account Settings
                        </Link>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            @click="mobileOpen = false"
                            class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-red-50 hover:text-red-600 transition-colors"
                        >
                            <ArrowRightStartOnRectangleIcon class="w-5 h-5" />
                            Sign Out
                        </Link>
                    </div>
                </div>
            </Transition>
        </nav>

        <!-- Page Heading -->
        <header v-if="$slots.header" class="bg-white border-b border-gray-100">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
