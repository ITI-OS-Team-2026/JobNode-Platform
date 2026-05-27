<script setup>
import EmployerLayout from '@/Layouts/EmployerLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    title: '',
    category: '',
    work_type: 'remote',
    location: '',
    min_salary: '',
    max_salary: '',
    application_deadline: '',
    technologies: '',
    description: '',
    responsibilities: '',
    requirements: '',
    benefits: '',
});

const submit = () => {
    form.post(route('employer.jobs.store'));
};
</script>

<template>
    <Head title="Post a New Role" />

    <EmployerLayout>
        <template #header>
            <h2 class="font-display font-semibold text-xl text-gray-800 leading-tight">Post a New Role</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-l1 sm:rounded-2xl border border-neutral-light p-8">
                    
                    <form @submit.prevent="submit" class="space-y-8 font-body">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-charcoal mb-1">Job Title</label>
                                <input v-model="form.title" type="text" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" placeholder="e.g. Senior Frontend Engineer" />
                                <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-charcoal mb-1">Category</label>
                                <select v-model="form.category" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]">
                                    <option disabled value="">Select a category</option>
                                    <option value="engineering">Software Engineering</option>
                                    <option value="design">Product Design</option>
                                    <option value="management">Product Management</option>
                                    <option value="marketing">Marketing</option>
                                </select>
                                <div v-if="form.errors.category" class="text-red-500 text-sm mt-1">{{ form.errors.category }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-charcoal mb-1">Work Type</label>
                                <select v-model="form.work_type" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]">
                                    <option value="remote">Remote</option>
                                    <option value="hybrid">Hybrid</option>
                                    <option value="onsite">On-site</option>
                                </select>
                                <div v-if="form.errors.work_type" class="text-red-500 text-sm mt-1">{{ form.errors.work_type }}</div>
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-charcoal mb-1">Location (or Timezone requirement)</label>
                                <input v-model="form.location" type="text" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" placeholder="e.g. New York, NY or GMT-5" />
                                <div v-if="form.errors.location" class="text-red-500 text-sm mt-1">{{ form.errors.location }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-charcoal mb-1">Minimum Salary (USD)</label>
                                <input v-model="form.min_salary" type="number" step="1000" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" placeholder="80000" />
                                <div v-if="form.errors.min_salary" class="text-red-500 text-sm mt-1">{{ form.errors.min_salary }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-charcoal mb-1">Maximum Salary (USD)</label>
                                <input v-model="form.max_salary" type="number" step="1000" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" placeholder="120000" />
                                <div v-if="form.errors.max_salary" class="text-red-500 text-sm mt-1">{{ form.errors.max_salary }}</div>
                            </div>
                            
                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-sm font-medium text-charcoal mb-1">Application Deadline</label>
                                <input v-model="form.application_deadline" type="date" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" />
                                <div v-if="form.errors.application_deadline" class="text-red-500 text-sm mt-1">{{ form.errors.application_deadline }}</div>
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-charcoal mb-1">Technologies (Comma separated)</label>
                                <input v-model="form.technologies" type="text" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" placeholder="Vue.js, Laravel, TailwindCSS, MySQL" />
                                <div v-if="form.errors.technologies" class="text-red-500 text-sm mt-1">{{ form.errors.technologies }}</div>
                            </div>
                        </div>

                        <div class="space-y-6 pt-6 border-t border-neutral-light">
                            <div>
                                <label class="block text-sm font-medium text-charcoal mb-1">Job Description</label>
                                <textarea v-model="form.description" rows="4" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" placeholder="Briefly describe the role and the team..."></textarea>
                                <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-charcoal mb-1">Responsibilities</label>
                                <textarea v-model="form.responsibilities" rows="4" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" placeholder="What will this person do day-to-day?"></textarea>
                                <div v-if="form.errors.responsibilities" class="text-red-500 text-sm mt-1">{{ form.errors.responsibilities }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-charcoal mb-1">Requirements</label>
                                <textarea v-model="form.requirements" rows="4" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" placeholder="What skills and experience are necessary?"></textarea>
                                <div v-if="form.errors.requirements" class="text-red-500 text-sm mt-1">{{ form.errors.requirements }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-charcoal mb-1">Benefits (Optional)</label>
                                <textarea v-model="form.benefits" rows="3" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" placeholder="Health insurance, equity, unlimited PTO..."></textarea>
                                <div v-if="form.errors.benefits" class="text-red-500 text-sm mt-1">{{ form.errors.benefits }}</div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-6 border-t border-neutral-light">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Submit for Approval
                            </PrimaryButton>
                        </div>
                        
                    </form>

                </div>
            </div>
        </div>
    </EmployerLayout>
</template>
