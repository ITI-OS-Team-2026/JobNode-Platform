<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    applications: Array
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric'
    });
};
</script>

<template>
    <Head title="Candidate Applications" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display font-semibold text-xl text-gray-800 leading-tight">Applications</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-l1 sm:rounded-2xl border border-neutral-light p-8">
                    <h3 class="font-display text-2xl mb-4 text-charcoal">My Applications</h3>
                    
                    <div v-if="applications.length === 0" class="text-center py-8">
                        <p class="font-body text-neutral-stone mb-4">You haven't applied to any jobs yet.</p>
                        <Link :href="route('jobs.index')" class="inline-flex items-center px-4 py-2 bg-jobnode-emerald border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-jobnode-emeraldHover focus:bg-jobnode-emeraldHover active:bg-jobnode-emeraldHover focus:outline-none focus:ring-2 focus:ring-jobnode-emerald focus:ring-offset-2 transition ease-in-out duration-150">
                            Browse Jobs
                        </Link>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="app in applications" :key="app.id" class="p-6 border border-neutral-light rounded-xl flex items-center justify-between hover:border-jobnode-emerald transition-colors">
                            <div>
                                <h4 class="font-display text-xl text-charcoal">{{ app.job.title }}</h4>
                                <p class="text-sm text-neutral-stone mt-1">
                                    {{ app.job.employer.company?.company_name || 'Confidential Company' }} • {{ app.job.location }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-neutral-100 text-charcoal capitalize border border-neutral-light">
                                    Status: {{ app.status }}
                                </span>
                                <p class="text-xs text-neutral-stone mt-2">Applied on {{ formatDate(app.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
