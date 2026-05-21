<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DarkCard from '@/Components/DarkCard.vue';

const props = defineProps({
    job: Object,
});

// --- Main Job Form ---
const techString = Array.isArray(props.job.technologies) 
    ? props.job.technologies.join(', ') 
    : (props.job.technologies || '');

const form = useForm({
    title: props.job.title,
    category: props.job.category,
    work_type: props.job.work_type,
    location: props.job.location,
    min_salary: props.job.min_salary,
    max_salary: props.job.max_salary,
    application_deadline: props.job.application_deadline ? props.job.application_deadline.split('T')[0] : '',
    technologies: techString,
    description: props.job.description,
    responsibilities: props.job.responsibilities,
    requirements: props.job.requirements,
    benefits: props.job.benefits,
});

const submitJob = () => {
    form.put(route('employer.jobs.update', props.job.id));
};

// --- Comment Update Form ---
const commentForm = useForm({
    content: '',
});

const submitComment = () => {
    commentForm.post(route('employer.jobs.comments.store', props.job.id), {
        preserveScroll: true,
        onSuccess: () => commentForm.reset('content'),
    });
};

// Helper to format dates
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'
    });
};
</script>

<template>
    <Head :title="'Edit: ' + job.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display font-semibold text-xl text-gray-800 leading-tight">Edit Role: {{ job.title }}</h2>
        </template>

        <div class="py-12 flex flex-col gap-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-l1 sm:rounded-2xl border border-neutral-light p-8">
                <form @submit.prevent="submitJob" class="space-y-8 font-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-charcoal mb-1">Job Title</label>
                            <input v-model="form.title" type="text" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-charcoal mb-1">Category</label>
                            <select v-model="form.category" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]">
                                <option value="engineering">Software Engineering</option>
                                <option value="design">Product Design</option>
                                <option value="management">Product Management</option>
                                <option value="marketing">Marketing</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-charcoal mb-1">Work Type</label>
                            <select v-model="form.work_type" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]">
                                <option value="remote">Remote</option>
                                <option value="hybrid">Hybrid</option>
                                <option value="onsite">On-site</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-charcoal mb-1">Location</label>
                            <input v-model="form.location" type="text" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-charcoal mb-1">Minimum Salary (USD)</label>
                            <input v-model="form.min_salary" type="number" step="1000" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-charcoal mb-1">Maximum Salary (USD)</label>
                            <input v-model="form.max_salary" type="number" step="1000" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-charcoal mb-1">Application Deadline</label>
                            <input v-model="form.application_deadline" type="date" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" />
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-charcoal mb-1">Technologies (Comma separated)</label>
                            <input v-model="form.technologies" type="text" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" />
                        </div>
                    </div>

                    <div class="space-y-6 pt-6 border-t border-neutral-light">
                        <div>
                            <label class="block text-sm font-medium text-charcoal mb-1">Job Description</label>
                            <textarea v-model="form.description" rows="4" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-charcoal mb-1">Responsibilities</label>
                            <textarea v-model="form.responsibilities" rows="4" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-charcoal mb-1">Requirements</label>
                            <textarea v-model="form.requirements" rows="4" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-charcoal mb-1">Benefits (Optional)</label>
                            <textarea v-model="form.benefits" rows="3" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-6 border-t border-neutral-light">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Save Changes
                        </PrimaryButton>
                    </div>
                </form>
            </div>

            <DarkCard class="p-8">
                <h3 class="font-display text-2xl mb-2">Public Status Updates</h3>
                <p class="font-body text-jobnode-emerald mb-6">Post updates here to keep candidates informed about the hiring process.</p>
                
                <form @submit.prevent="submitComment" class="mb-8">
                    <textarea v-model="commentForm.content" rows="3" class="w-full bg-charcoal-dark text-white rounded-lg border-jobnode-emerald/30 focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px] placeholder:text-neutral-stone mb-3" placeholder="e.g. We are currently reviewing the first batch of applications..."></textarea>
                    <div v-if="commentForm.errors.content" class="text-red-500 text-sm mb-3">{{ commentForm.errors.content }}</div>
                    
                    <div class="text-right">
                        <button type="submit" :disabled="commentForm.processing" class="inline-flex items-center justify-center px-6 py-2 bg-jobnode-emerald rounded-full font-body text-[14px] text-black hover:bg-jobnode-emeraldHover transition disabled:opacity-50">
                            Post Update
                        </button>
                    </div>
                </form>

                <div v-if="job.comments && job.comments.length > 0" class="space-y-4 border-t border-jobnode-emerald/30 pt-6">
                    <div v-for="comment in job.comments" :key="comment.id" class="p-4 bg-charcoal-dark rounded-lg border border-neutral-stone/30">
                        <p class="font-body text-[15px] mb-2">{{ comment.content }}</p>
                        <p class="font-display text-xs text-neutral-stone">{{ formatDate(comment.created_at) }}</p>
                    </div>
                </div>
                <div v-else class="border-t border-jobnode-emerald/30 pt-6 text-neutral-stone text-sm">
                    No status updates posted yet.
                </div>
            </DarkCard>

        </div>
    </AuthenticatedLayout>
</template>