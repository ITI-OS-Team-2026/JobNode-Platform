<script setup>
import EmployerLayout from '@/Layouts/EmployerLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    jobs: Array,
});
</script>

<template>
    <Head title="My Listings" />

    <EmployerLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-display font-semibold text-xl text-gray-800 leading-tight">My Job Listings</h2>
                <Link :href="route('employer.jobs.create')">
                    <PrimaryButton type="button">Post New Role</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div v-if="jobs.length === 0" class="bg-white overflow-hidden shadow-l1 sm:rounded-2xl border border-neutral-light p-12 text-center">
                    <h3 class="font-display text-2xl mb-2 text-charcoal">No listings yet</h3>
                    <p class="font-body text-neutral-stone mb-6">Post your first role to start receiving applications.</p>
                </div>

                <div v-for="job in jobs" :key="job.id" class="bg-white overflow-hidden shadow-l1 sm:rounded-2xl border border-neutral-light p-6 transition hover:border-jobnode-emerald">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-1">
                                <h3 class="font-display text-2xl text-charcoal">{{ job.title }}</h3>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold capitalize" 
                                      :class="{
                                          'bg-yellow-100 text-yellow-800': job.status === 'pending',
                                          'bg-jobnode-emerald/20 text-jobnode-emeraldHover': job.status === 'approved',
                                          'bg-red-100 text-red-800': job.status === 'rejected',
                                          'bg-neutral-light text-neutral-stone': job.status === 'closed'
                                      }">
                                    {{ job.status }}
                                </span>
                            </div>
                            <p class="font-body text-neutral-stone text-sm">{{ job.location }} • {{ job.work_type }}</p>
                        </div>

                        <div class="flex items-center gap-8 px-6 border-l border-neutral-light">
                            <div class="text-center">
                                <p class="font-display text-2xl">{{ job.views_count }}</p>
                                <p class="font-body text-xs text-neutral-stone uppercase tracking-wide">Views</p>
                            </div>
                            <div class="text-center">
                                <p class="font-display text-2xl text-jobnode-emerald">{{ job.applications_count }}</p>
                                <p class="font-body text-xs text-neutral-stone uppercase tracking-wide">Applicants</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 md:pl-6">
                            <Link :href="route('employer.jobs.edit', job.id)" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg text-sm font-semibold hover:bg-emerald-600 hover:text-white transition-all shadow-sm border border-emerald-100">
                                Edit
                            </Link>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </EmployerLayout>
</template>
