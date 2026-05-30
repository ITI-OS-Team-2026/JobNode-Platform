<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    applications: Array
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric'
    });
};

const statusClasses = (status) => {
    const classes = {
        applied: 'bg-jobnode-emerald/10 text-jobnode-emeraldHover border-jobnode-emerald/30',
        reviewed: 'bg-blue-50 text-blue-700 border-blue-200',
        accepted: 'bg-green-50 text-green-700 border-green-200',
        rejected: 'bg-red-50 text-red-700 border-red-200',
        cancelled: 'bg-neutral-100 text-neutral-stone border-neutral-light',
    };

    return classes[status] || 'bg-neutral-100 text-charcoal border-neutral-light';
};

const canCancel = (status) => status === 'applied';

const cancelApplication = (application) => {
    if (!confirm(`Cancel your application for "${application.job.title}"?`)) {
        return;
    }

    router.post(route('candidate.applications.cancel', application.id), {}, {
        preserveScroll: true,
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

                    <div v-if="$page.props.flash && $page.props.flash.success" class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                        {{ $page.props.flash.success }}
                    </div>

                    <div v-if="$page.props.flash && $page.props.flash.error" class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                        {{ $page.props.flash.error }}
                    </div>
                    
                    <div v-if="applications.length === 0" class="text-center py-8">
                        <p class="font-body text-neutral-stone mb-4">You haven't applied to any jobs yet.</p>
                        <Link :href="route('jobs.index')" class="inline-flex items-center px-4 py-2 bg-jobnode-emerald border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-jobnode-emeraldHover focus:bg-jobnode-emeraldHover active:bg-jobnode-emeraldHover focus:outline-none focus:ring-2 focus:ring-jobnode-emerald focus:ring-offset-2 transition ease-in-out duration-150">
                            Browse Jobs
                        </Link>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="app in applications" :key="app.id" class="p-6 border border-neutral-light rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:border-jobnode-emerald transition-colors">
                            <div>
                                <h4 class="font-display text-xl text-charcoal">{{ app.job.title }}</h4>
                                <p class="text-sm text-neutral-stone mt-1">
                                    {{ app.job.employer.company?.company_name || 'Confidential Company' }} • {{ app.job.location }}
                                </p>
                            </div>
                            <div class="flex flex-col items-start sm:items-end gap-2">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold capitalize border"
                                    :class="statusClasses(app.status)"
                                >
                                    {{ app.status }}
                                </span>
                                <p class="text-xs text-neutral-stone">Applied on {{ formatDate(app.created_at) }}</p>
                                <button
                                    v-if="canCancel(app.status)"
                                    type="button"
                                    @click="cancelApplication(app)"
                                    class="text-xs font-semibold text-red-600 hover:text-red-700 hover:underline"
                                >
                                    Cancel Application
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
