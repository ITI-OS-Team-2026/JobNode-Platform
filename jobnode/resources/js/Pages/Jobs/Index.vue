<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    jobs: Object, // Note: It's an object now because it's paginated data
    filters: Object,
});

const dashboardRoutes = {
    candidate: 'candidate.dashboard',
    employer: 'employer.dashboard',
    admin: 'admin.dashboard',
};

const dashboardRouteName = (role) => dashboardRoutes[role] ?? 'home';

// Initialize reactive state with the values from the URL (if any exist)
const search = ref(props.filters.search || '');
const category = ref(props.filters.category || '');
const workType = ref(props.filters.work_type || '');
const location = ref(props.filters.location || '');

let debounceTimeout = null;

// Watch all filters and hit the backend when they change
watch([search, category, workType, location], () => {
    clearTimeout(debounceTimeout);
    
    debounceTimeout = setTimeout(() => {
        router.get(
            route('jobs.index'),
            {
                search: search.value,
                category: category.value,
                work_type: workType.value,
                location: location.value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true, // Replaces the current history state so the back button works cleanly
            }
        );
    }, 300); // 300ms delay to prevent spamming the database while typing
});

const clearFilters = () => {
    search.value = '';
    category.value = '';
    workType.value = '';
    location.value = '';
};
</script>

<template>
    <Head title="Browse Open Roles" />

    <div class="min-h-screen bg-neutral-100 font-body">
        
        <div class="bg-charcoal text-white pt-16 pb-12 px-6 lg:px-8">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div>
                    <h1 class="font-display text-5xl mb-2">Open Roles.</h1>
                    <p class="text-neutral-light text-lg">Filter by stack, location, or work type.</p>
                </div>
                
                <div class="hidden md:flex gap-4">
                    <Link v-if="!$page.props.auth.user" :href="route('login')" class="text-white hover:text-jobnode-emerald transition">Log In</Link>
                    <Link v-if="$page.props.auth.user" :href="route(dashboardRouteName($page.props.auth.user.role))" class="text-jobnode-emerald hover:underline">Go to Dashboard</Link>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <div class="lg:col-span-3">
                    <div class="sticky top-8 bg-white p-6 rounded-2xl border border-neutral-light shadow-l1 space-y-6">
                        
                        <div>
                            <label class="block text-sm font-medium text-charcoal mb-2">Search Keywords</label>
                            <input v-model="search" type="text" placeholder="Vue, Next.js, N8N..." class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-charcoal mb-2">Category</label>
                            <select v-model="category" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]">
                                <option value="">All Categories</option>
                                <option value="engineering">Software Engineering</option>
                                <option value="design">Product Design</option>
                                <option value="management">Product Management</option>
                                <option value="marketing">Marketing</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-charcoal mb-2">Work Type</label>
                            <select v-model="workType" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]">
                                <option value="">Any</option>
                                <option value="remote">Remote</option>
                                <option value="hybrid">Hybrid</option>
                                <option value="onsite">On-site</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-charcoal mb-2">Location</label>
                            <input v-model="location" type="text" placeholder="e.g. New York or GMT-5" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" />
                        </div>

                        <button @click="clearFilters" class="w-full text-center text-sm text-neutral-stone hover:text-jobnode-emerald transition underline pt-4">
                            Clear all filters
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-9 space-y-4">
                    
                    <div v-if="jobs.data.length === 0" class="bg-white p-12 text-center rounded-2xl border border-neutral-light">
                        <h3 class="font-display text-2xl text-charcoal mb-2">No roles found</h3>
                        <p class="text-neutral-stone">Try adjusting your filters to broaden your search.</p>
                    </div>

                    <Link 
                        v-for="job in jobs.data" 
                        :key="job.id"
                        :href="route('jobs.show', job.id)"
                        class="block bg-white p-6 rounded-2xl border border-neutral-light shadow-sm transition-all duration-200 hover:border-jobnode-emerald hover:shadow-l2 group"
                    >
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-neutral-stone mb-1">
                                    {{ job.employer.company?.company_name || 'Confidential Company' }}
                                </p>
                                <h3 class="font-display text-2xl text-charcoal group-hover:text-jobnode-emerald transition-colors">{{ job.title }}</h3>
                                
                                <div class="flex flex-wrap items-center gap-3 mt-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-neutral-light/50 text-charcoal capitalize">
                                        {{ job.work_type }}
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-neutral-light/50 text-charcoal capitalize">
                                        {{ job.category }}
                                    </span>
                                    <span class="text-sm text-neutral-stone flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        {{ job.location }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <p v-if="job.min_salary && job.max_salary" class="font-display text-jobnode-sky font-semibold">
                                    ${{ (job.min_salary / 1000).toFixed(0) }}k - ${{ (job.max_salary / 1000).toFixed(0) }}k
                                </p>
                                <p v-else class="text-sm text-neutral-stone">Salary unlisted</p>
                            </div>
                        </div>
                    </Link>

                    <div v-if="jobs.links.length > 3" class="flex items-center justify-center gap-2 mt-8">
                        <template v-for="(link, index) in jobs.links" :key="index">
                            <Link 
                                v-if="link.url" 
                                :href="link.url"
                                class="px-4 py-2 rounded-lg text-sm transition-colors"
                                :class="link.active ? 'bg-charcoal text-white' : 'bg-white border border-neutral-light hover:border-jobnode-emerald'"
                                v-html="link.label"
                            ></Link>
                            <span v-else class="px-4 py-2 text-neutral-stone" v-html="link.label"></span>
                        </template>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
