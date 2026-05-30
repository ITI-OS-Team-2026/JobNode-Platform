<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    profile: Object
});

const form = useForm({
    title: props.profile?.title || '',
    skills: Array.isArray(props.profile?.skills) ? props.profile.skills.join(', ') : (props.profile?.skills || ''),
    linkedin_url: props.profile?.linkedin_url || '',
    phone: props.profile?.phone || '',
    resume: null,
});

const submit = () => {
    form.post(route('candidate.profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('resume'); // reset file input visually
        }
    });
};
</script>

<template>
    <Head title="My Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display font-semibold text-xl text-gray-800 leading-tight">Candidate Profile</h2>
        </template>

        <div class="py-12 max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-l1 sm:rounded-2xl border border-neutral-light p-8">
                
                <div class="mb-8 border-b border-neutral-light pb-6">
                    <h3 class="font-display text-2xl text-charcoal mb-1">Your Identity</h3>
                    <p class="font-body text-neutral-stone text-sm">This information is shared with employers when you apply.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6 font-body">
                    
                    <div>
                        <label class="block text-sm font-medium text-charcoal mb-1">Professional Title</label>
                        <input v-model="form.title" type="text" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" placeholder="e.g. Senior Full-Stack Engineer" />
                        <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-charcoal mb-1">Core Skills (Comma separated)</label>
                        <input v-model="form.skills" type="text" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" placeholder="Vue.js, Laravel, Workflow Automation..." />
                        <div v-if="form.errors.skills" class="text-red-500 text-xs mt-1">{{ form.errors.skills }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-charcoal mb-1">LinkedIn URL</label>
                        <input v-model="form.linkedin_url" type="url" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" placeholder="https://linkedin.com/in/yourprofile" />
                        <div v-if="form.errors.linkedin_url" class="text-red-500 text-xs mt-1">{{ form.errors.linkedin_url }}</div>
                    </div>

                    <div class="pt-6 border-t border-neutral-light">
                        <h4 class="font-display text-lg text-charcoal mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-jobnode-emerald" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Gated Information
                        </h4>
                        <p class="text-sm text-neutral-stone mb-4">Employers must unlock your profile to access these details.</p>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-charcoal mb-1">Direct Phone Number</label>
                                <input v-model="form.phone" type="tel" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" placeholder="+1 (555) 000-0000" />
                                <div v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-charcoal mb-1">Resume / CV (PDF only)</label>
                                <input type="file" @input="form.resume = $event.target.files[0]" accept=".pdf,.doc,.docx" class="w-full text-sm text-neutral-stone file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-jobnode-emerald/10 file:text-jobnode-emeraldHover hover:file:bg-jobnode-emerald/20" />
                                <div v-if="form.errors.resume" class="text-red-500 text-xs mt-1">{{ form.errors.resume }}</div>
                                <div v-if="$page.props.profile?.resume_original_filename" class="text-xs text-green-600 mt-2">
                                    ✓ Current Resume: {{ $page.props.profile.resume_original_filename }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-neutral-light">
                        <span v-if="form.recentlySuccessful" class="text-sm text-green-600 font-medium">✓ Saved successfully.</span>
                        <PrimaryButton type="submit" :disabled="form.processing">Save Profile</PrimaryButton>
                    </div>

                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
