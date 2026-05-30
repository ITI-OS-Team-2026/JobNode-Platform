<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

defineProps({
    jobs: Array,
});

const updateStatus = (jobId, newStatus) => {
    if (newStatus === 'rejected' && !confirm('Are you sure you want to reject this job?')) {
        return;
    }

    router.post(route('admin.jobs.status.update', jobId), {
        _method: 'patch',
        status: newStatus
    }, {
        preserveScroll: true,
    });
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric'
    });
};
</script>

<template>
    <Head title="Moderation Queue" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display font-semibold text-xl text-gray-800 leading-tight">Moderation Queue</h2>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="mb-6">
                <h1 class="font-display text-3xl text-charcoal mb-1">Pending Approvals</h1>
                <p class="font-body text-neutral-stone">Review and moderate incoming job listings before they go live.</p>
            </div>

            <div v-if="jobs.length === 0" class="bg-white overflow-hidden shadow-l1 sm:rounded-2xl border border-neutral-light p-12 text-center">
                <svg class="w-12 h-12 text-jobnode-emerald mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="font-display text-2xl mb-2 text-charcoal">Queue is clear</h3>
                <p class="font-body text-neutral-stone mb-6">There are no pending jobs requiring moderation at this time.</p>
            </div>

            <div v-for="job in jobs" :key="job.id" class="bg-white overflow-hidden shadow-l1 sm:rounded-2xl border border-neutral-light p-6">
                <div class="flex flex-col lg:flex-row gap-6">
                    
                    <div class="flex-1 space-y-4">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h3 class="font-display text-2xl text-charcoal">{{ job.title }}</h3>
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold uppercase tracking-wider">
                                    Needs Review
                                </span>
                            </div>
                            <p class="font-body text-neutral-stone text-sm">
                                Posted by <span class="font-medium text-charcoal">{{ job.employer.company?.company_name || 'Unknown Company' }}</span> on {{ formatDate(job.created_at) }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-4 border-y border-neutral-light">
                            <div>
                                <p class="text-xs text-neutral-stone uppercase tracking-wider mb-1">Location</p>
                                <p class="text-sm text-charcoal font-medium">{{ job.location }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-stone uppercase tracking-wider mb-1">Type</p>
                                <p class="text-sm text-charcoal font-medium capitalize">{{ job.work_type }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-stone uppercase tracking-wider mb-1">Category</p>
                                <p class="text-sm text-charcoal font-medium capitalize">{{ job.category }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-stone uppercase tracking-wider mb-1">Salary</p>
                                <p class="text-sm text-charcoal font-medium">
                                    {{ job.min_salary ? `$${job.min_salary / 1000}k - $${job.max_salary / 1000}k` : 'Unlisted' }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-neutral-stone uppercase tracking-wider mb-1">Brief Description</p>
                            <p class="text-sm text-charcoal line-clamp-2">{{ job.description }}</p>
                        </div>
                    </div>

                    <div class="flex flex-row lg:flex-col items-center justify-center gap-3 lg:border-l border-neutral-light lg:pl-6">
                        <button 
                            @click="updateStatus(job.id, 'approved')"
                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-jobnode-emerald rounded-xl font-body text-sm text-black font-medium transition hover:bg-jobnode-emeraldHover"
                        >
                            Approve Listing
                        </button>
                        
                        <button 
                            @click="updateStatus(job.id, 'rejected')"
                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-white border border-red-200 rounded-xl font-body text-sm text-red-600 font-medium transition hover:bg-red-50 hover:border-red-300"
                        >
                            Reject
                        </button>
                        

                    </div>

                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
