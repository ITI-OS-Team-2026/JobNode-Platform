<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    BriefcaseIcon, 
    EyeIcon, 
    UsersIcon, 
    KeyIcon,
    ChevronRightIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    metrics: Object,
    recentApplications: Array
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric'
    });
};
</script>

<template>
    <Head title="Employer Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display font-semibold text-xl text-gray-800 leading-tight">Analytics Workspace</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- Welcome Section -->
                <div class="bg-gradient-to-r from-charcoal to-slate-800 text-white overflow-hidden shadow-dark-l1 sm:rounded-2xl border border-jobnode-emerald/30 p-8 relative isolate">
                    <div class="absolute inset-0 bg-[url('/img/grid-pattern.svg')] opacity-10"></div>
                    <div class="relative z-10">
                        <h3 class="font-display text-3xl font-bold mb-2">Welcome back, {{ $page.props.auth.user.name }}</h3>
                        <p class="font-body text-jobnode-emerald/90 text-lg">Here's what's happening with your job listings today.</p>
                    </div>
                </div>

                <!-- Metrics Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Active Roles -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-gray-500 font-medium text-sm">Active Roles</h4>
                            <div class="bg-blue-50 text-blue-600 p-2 rounded-lg">
                                <BriefcaseIcon class="w-5 h-5" />
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ metrics?.active_jobs || 0 }}</div>
                    </div>

                    <!-- Total Views -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-gray-500 font-medium text-sm">Total Views</h4>
                            <div class="bg-purple-50 text-purple-600 p-2 rounded-lg">
                                <EyeIcon class="w-5 h-5" />
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ metrics?.total_views || 0 }}</div>
                    </div>

                    <!-- Applicants -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-gray-500 font-medium text-sm">Applicants</h4>
                            <div class="bg-jobnode-emerald/10 text-jobnode-emerald p-2 rounded-lg">
                                <UsersIcon class="w-5 h-5" />
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ metrics?.total_applicants || 0 }}</div>
                    </div>

                    <!-- Unlocked Profiles -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-gray-500 font-medium text-sm">Unlocked Profiles</h4>
                            <div class="bg-amber-50 text-amber-600 p-2 rounded-lg">
                                <KeyIcon class="w-5 h-5" />
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ metrics?.candidate_unlocks || 0 }}</div>
                    </div>
                </div>

                <!-- Recent Applications Feed -->
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Applications</h3>
                        <Link :href="route('employer.applications.index')" class="text-sm font-medium text-jobnode-emerald hover:text-emerald-700 transition-colors">
                            View all
                        </Link>
                    </div>
                    
                    <div class="divide-y divide-gray-100">
                        <template v-if="recentApplications && recentApplications.length > 0">
                            <div v-for="application in recentApplications" :key="application.id" class="p-6 hover:bg-gray-50 transition-colors duration-200 group">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-start space-x-4">
                                        <div class="h-10 w-10 rounded-full bg-jobnode-emerald/10 flex items-center justify-center text-jobnode-emerald font-bold shrink-0">
                                            {{ application.candidate.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900">{{ application.candidate.name }}</h4>
                                            <div class="flex items-center mt-1 space-x-2 text-sm text-gray-500">
                                                <span class="font-medium text-gray-700">{{ application.job.title }}</span>
                                                <span>&bull;</span>
                                                <span>Applied {{ formatDate(application.created_at) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shrink-0">
                                        <Link :href="route('employer.applications.show', application.id)" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-jobnode-emerald bg-jobnode-emerald/10 hover:bg-jobnode-emerald/20 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-jobnode-emerald">
                                            Review
                                            <ChevronRightIcon class="ml-2 w-4 h-4" />
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Empty State -->
                        <div v-else class="p-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                <UsersIcon class="w-8 h-8 text-gray-400" />
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">No applications yet</h3>
                            <p class="text-gray-500">When candidates apply to your jobs, they'll appear here.</p>
                            <div class="mt-6">
                                <Link :href="route('employer.jobs.create')" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-charcoal hover:bg-gray-800 transition-colors shadow-sm">
                                    Post a new job
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </AuthenticatedLayout>
</template>
