<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();
const profile = ref(page.props.profile);

const form = useForm({
    title: profile.value.title || '',
    phone: profile.value.phone || '',
    linkedin_url: profile.value.linkedin_url || '',
    skills: profile.value.skills || [],
    resume: null,
});

const skillInput = ref('');
const isSubmitting = ref(false);

const completenessStatus = computed(() => {
    const percentage = profile.value.completeness_percentage;
    if (percentage === 100) return 'Complete';
    if (percentage >= 75) return 'Almost Complete';
    if (percentage >= 50) return 'Partially Complete';
    return 'Incomplete';
});

const completenessStatusClass = computed(() => {
    const percentage = profile.value.completeness_percentage;
    if (percentage === 100) return 'text-green-600 bg-green-50';
    if (percentage >= 75) return 'text-yellow-600 bg-yellow-50';
    if (percentage >= 50) return 'text-orange-600 bg-orange-50';
    return 'text-red-600 bg-red-50';
});

const addSkill = () => {
    if (skillInput.value.trim() && form.skills.length < 20) {
        form.skills.push(skillInput.value.trim());
        skillInput.value = '';
    }
};

const removeSkill = (index) => {
    form.skills.splice(index, 1);
};

const handleKeyPress = (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        addSkill();
    }
};

const handleResumeSelect = (e) => {
    form.resume = e.target.files[0];
};

const submitForm = () => {
    isSubmitting.value = true;
    form.put(route('candidate.profile.update'), {
        onFinish: () => {
            isSubmitting.value = false;
            // Reload profile data after successful update
            if (!form.errors) {
                location.reload();
            }
        }
    });
};

const downloadResume = () => {
    window.location.href = route('candidate.profile.resume.download');
};
</script>

<template>
    <Head title="Candidate Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display font-semibold text-xl text-gray-800 leading-tight">My Profile</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Completeness Status Card -->
                <div class="bg-white overflow-hidden shadow-l1 sm:rounded-2xl border border-neutral-light p-8 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-display text-lg font-semibold text-charcoal mb-2">Profile Completeness</h3>
                            <p class="font-body text-neutral-stone mb-4">
                                Your profile is
                                <span :class="`font-semibold ${completenessStatusClass}`" class="px-2 py-1 rounded">
                                    {{ completenessStatus }}
                                </span>
                            </p>
                            <p class="font-body text-sm text-neutral-stone mb-3">
                                Your profile must have a phone number and resume to be considered complete.
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-4xl font-bold" :class="profile.completeness_percentage === 100 ? 'text-green-600' : 'text-orange-600'">
                                {{ profile.completeness_percentage }}%
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                        <div 
                            class="h-2 rounded-full transition-all"
                            :class="profile.completeness_percentage === 100 ? 'bg-green-600' : 'bg-orange-500'"
                            :style="`width: ${profile.completeness_percentage}%`"
                        ></div>
                    </div>
                </div>

                <!-- Profile Form Card -->
                <div class="bg-white overflow-hidden shadow-l1 sm:rounded-2xl border border-neutral-light p-8">
                    <h3 class="font-display text-2xl mb-6 text-charcoal">Edit Profile</h3>

                    <!-- Success Message -->
                    <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="font-body text-green-700">{{ $page.props.flash.success }}</p>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-6">
                        <!-- Professional Title -->
                        <div>
                            <label for="title" class="block font-body font-medium text-charcoal mb-2">
                                Professional Title
                            </label>
                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                placeholder="e.g., Senior Vue Developer"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p v-if="form.errors.title" class="font-body text-red-600 text-sm mt-1">
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone" class="block font-body font-medium text-charcoal mb-2">
                                Phone Number <span class="text-red-600">*</span>
                            </label>
                            <input
                                id="phone"
                                v-model="form.phone"
                                type="tel"
                                placeholder="e.g., +1 (555) 123-4567"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p v-if="form.errors.phone" class="font-body text-red-600 text-sm mt-1">
                                {{ form.errors.phone }}
                            </p>
                            <p class="font-body text-neutral-stone text-sm mt-1">
                                Required for profile completeness
                            </p>
                        </div>

                        <!-- LinkedIn URL -->
                        <div>
                            <label for="linkedin_url" class="block font-body font-medium text-charcoal mb-2">
                                LinkedIn Profile URL
                            </label>
                            <input
                                id="linkedin_url"
                                v-model="form.linkedin_url"
                                type="url"
                                placeholder="https://linkedin.com/in/yourprofile"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p v-if="form.errors.linkedin_url" class="font-body text-red-600 text-sm mt-1">
                                {{ form.errors.linkedin_url }}
                            </p>
                        </div>

                        <!-- Skills -->
                        <div>
                            <label for="skills" class="block font-body font-medium text-charcoal mb-2">
                                Skills <span class="text-gray-500 text-sm">(Max 20)</span>
                            </label>
                            <div class="flex gap-2 mb-3">
                                <input
                                    id="skills"
                                    v-model="skillInput"
                                    type="text"
                                    placeholder="Add a skill (e.g., Vue.js)"
                                    @keypress="handleKeyPress"
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                <button
                                    type="button"
                                    @click="addSkill"
                                    :disabled="form.skills.length >= 20"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400"
                                >
                                    Add
                                </button>
                            </div>
                            
                            <!-- Skills Tags -->
                            <div v-if="form.skills.length > 0" class="flex flex-wrap gap-2 mb-3">
                                <span
                                    v-for="(skill, index) in form.skills"
                                    :key="index"
                                    class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm"
                                >
                                    {{ skill }}
                                    <button
                                        type="button"
                                        @click="removeSkill(index)"
                                        class="text-blue-700 hover:text-blue-900 font-bold"
                                    >
                                        ×
                                    </button>
                                </span>
                            </div>
                            
                            <p v-if="form.errors.skills" class="font-body text-red-600 text-sm">
                                {{ form.errors.skills }}
                            </p>
                        </div>

                        <!-- Resume Upload -->
                        <div>
                            <label for="resume" class="block font-body font-medium text-charcoal mb-2">
                                Resume <span class="text-red-600">*</span>
                            </label>
                            
                            <!-- Current Resume Info -->
                            <div v-if="profile.resume_path" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="font-body font-semibold text-blue-900 mb-2">Current Resume</p>
                                <p class="font-body text-blue-800 text-sm mb-2">
                                    <strong>File:</strong> {{ profile.resume_original_filename }}
                                </p>
                                <p v-if="profile.resume_uploaded_at" class="font-body text-blue-800 text-sm mb-3">
                                    <strong>Uploaded:</strong> {{ new Date(profile.resume_uploaded_at).toLocaleDateString() }}
                                </p>
                                <button
                                    type="button"
                                    @click="downloadResume"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm"
                                >
                                    Download Resume
                                </button>
                            </div>

                            <!-- Upload Area -->
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors cursor-pointer"
                                 @click="$refs.resumeInput.click()"
                                 @dragover.prevent="$event.dataTransfer.dropEffect = 'copy'"
                                 @drop.prevent="form.resume = $event.dataTransfer.files[0]">
                                <input
                                    ref="resumeInput"
                                    type="file"
                                    accept=".pdf,.doc,.docx"
                                    @change="handleResumeSelect"
                                    class="hidden"
                                />
                                <p class="font-body font-semibold text-charcoal mb-1">
                                    {{ form.resume ? form.resume.name : 'Click to upload resume or drag and drop' }}
                                </p>
                                <p class="font-body text-neutral-stone text-sm">
                                    PDF or Word documents up to 5MB
                                </p>
                            </div>
                            <p v-if="form.errors.resume" class="font-body text-red-600 text-sm mt-2">
                                {{ form.errors.resume }}
                            </p>
                            <p class="font-body text-neutral-stone text-sm mt-2">
                                Required for profile completeness
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-4">
                            <button
                                type="submit"
                                :disabled="isSubmitting || form.processing"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400 font-body font-semibold transition-colors"
                            >
                                {{ isSubmitting || form.processing ? 'Saving...' : 'Save Profile' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
