<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DarkCard from '@/Components/DarkCard.vue';

const props = defineProps({
    job: Object,
    hasApplied: Boolean,
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric'
    });
};

const formatDateTime = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
    });
};
</script>

<template>
    <Head :title="job.title + ' at ' + (job.employer.company?.company_name || 'Confidential')" />

    <div class="min-h-screen bg-neutral-100 font-body pb-16">
        
        <div class="bg-charcoal text-white pt-12 pb-10 px-6 lg:px-8 border-b border-jobnode-emerald/20">
            <div class="max-w-5xl mx-auto flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <Link :href="route('jobs.index')" class="text-xs font-semibold text-jobnode-emerald uppercase tracking-wider hover:underline mb-4 inline-block">
                        ← Back to all open roles
                    </Link>
                    <p class="text-sm font-medium text-neutral-light mb-1">
                        {{ job.employer.company?.company_name || 'Confidential Company' }}
                    </p>
                    <h1 class="font-display text-4xl text-white">{{ job.title }}</h1>
                    <p class="text-neutral-light text-sm mt-2">
                        Posted {{ formatDate(job.created_at) }} • Deadline: {{ formatDate(job.application_deadline) }}
                    </p>
                </div>

                <div class="text-left md:text-right">
                    <p v-if="job.min_salary && job.max_salary" class="font-display text-3xl text-jobnode-emerald font-semibold">
                        ${{ (job.min_salary / 1000).toFixed(0) }}k - ${{ (job.max_salary / 1000).toFixed(0) }}k
                    </p>
                    <p v-else class="font-display text-xl text-neutral-light">Salary Unlisted</p>
                    <p class="text-xs text-neutral-light mt-1 capitalize">{{ job.location }} • {{ job.work_type }}</p>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-6 lg:px-8 mt-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <div class="lg:col-span-8 space-y-6">
                    <div class="bg-white p-8 rounded-2xl border border-neutral-light shadow-l1 space-y-8">
                        
                        <div>
                            <h3 class="font-display text-xl text-charcoal border-b border-neutral-light pb-2 mb-3">Role Overview</h3>
                            <p class="text-neutral-stone leading-relaxed whitespace-pre-line text-[16px]">{{ job.description }}</p>
                        </div>

                        <div>
                            <h3 class="font-display text-xl text-charcoal border-b border-neutral-light pb-2 mb-3">Core Responsibilities</h3>
                            <p class="text-neutral-stone leading-relaxed whitespace-pre-line text-[16px]">{{ job.responsibilities }}</p>
                        </div>

                        <div>
                            <h3 class="font-display text-xl text-charcoal border-b border-neutral-light pb-2 mb-3">Requirements & Qualifications</h3>
                            <p class="text-neutral-stone leading-relaxed whitespace-pre-line text-[16px]">{{ job.requirements }}</p>
                        </div>

                        <div v-if="job.technologies && job.technologies.length > 0">
                            <h3 class="font-display text-sm font-semibold uppercase tracking-wider text-neutral-stone mb-3">Target Stack</h3>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="(tech, i) in job.technologies" :key="i" class="px-3 py-1 bg-neutral-100 border border-neutral-light rounded-lg text-sm text-charcoal font-medium">
                                    {{ tech }}
                                </span>
                            </div>
                        </div>

                        <div v-if="job.benefits">
                            <h3 class="font-display text-xl text-charcoal border-b border-neutral-light pb-2 mb-3">Comp & Benefits</h3>
                            <p class="text-neutral-stone leading-relaxed whitespace-pre-line text-[16px]">{{ job.benefits }}</p>
                        </div>

                    </div>
                </div>

                <div class="lg:col-span-4 space-y-6">
                    
                    <div class="bg-white p-6 rounded-2xl border border-neutral-light shadow-l1 text-center">
                        <template v-if="hasApplied">
                            <div class="p-4 bg-jobnode-emerald/10 border border-jobnode-emerald/30 rounded-xl text-jobnode-emeraldHover text-sm font-medium mb-2">
                                ✓ Application Submitted
                            </div>
                            <p class="text-xs text-neutral-stone">You have already initiated an active workflow track for this position.</p>
                        </template>
                        
                        <template v-else>
                            <template v-if="$page.props.auth.user && $page.props.auth.user.role === 'candidate'">
                                <Link 
                                    :href="route('candidate.jobs.apply', job.id)" 
                                    method="post" 
                                    as="button" 
                                    type="button" 
                                    preserve-scroll
                                    class="w-full inline-flex items-center justify-center px-8 py-3 bg-jobnode-emerald rounded-4xl font-body text-[16px] text-black font-normal transition-all hover:bg-jobnode-emeraldHover hover:shadow-l2 active:scale-98"
                                >
                                    Apply For This Role
                                </Link>
                                <p class="text-xs text-neutral-stone mt-3">Submitting counts toward your 12-role platform limit.</p>
                            </template>

                            <template v-else-if="!$page.props.auth.user">
                                <Link :href="route('register')" class="w-full block">
                                    <PrimaryButton type="button" class="w-full">
                                        Sign Up to Apply
                                    </PrimaryButton>
                                </Link>
                                <p class="text-xs text-neutral-stone mt-3">
                                    Already have an account? 
                                    <Link :href="route('login')" class="text-jobnode-sky hover:underline">Log In</Link>
                                </p>
                            </template>

                            <template v-else>
                                <div class="p-4 bg-neutral-100 border border-neutral-light rounded-xl text-neutral-stone text-sm">
                                    Viewing listing as an {{ $page.props.auth.user.role }}.
                                </div>
                            </template>
                        </template>
                    </div>

                    <DarkCard class="p-6">
                        <h4 class="font-display text-lg text-white mb-1">Process Timeline</h4>
                        <p class="text-xs text-jobnode-emerald mb-4">Live structural tracking directly from the hiring manager.</p>
                        
                        <div v-if="job.comments && job.comments.length > 0" class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
                            <div v-for="comment in job.comments" :key="comment.id" class="p-3 bg-charcoal-dark border border-neutral-stone/30 rounded-xl">
                                <p class="text-sm text-white/90 leading-normal mb-2">{{ comment.content }}</p>
                                <p class="text-[10px] font-display text-neutral-stone uppercase tracking-wider">{{ formatDateTime(comment.created_at) }}</p>
                            </div>
                        </div>
                        
                        <div v-else class="text-xs text-neutral-stone italic py-4 border-t border-jobnode-emerald/20">
                            No custom process updates have been published for this workspace track yet.
                        </div>
                    </DarkCard>

                </div>
            </div>
        </div>
    </div>
</template>
