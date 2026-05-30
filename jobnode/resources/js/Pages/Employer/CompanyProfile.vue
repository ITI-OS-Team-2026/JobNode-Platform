<script setup>
import EmployerLayout from '@/Layouts/EmployerLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    company: Object
});

const form = useForm({
    company_name: props.company?.company_name || '',
    website: props.company?.website || '',
    logo: null,
});

const submit = () => {
    form.post(route('employer.company.profile.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset('logo'),
    });
};
</script>

<template>
    <Head title="Company Settings" />

    <EmployerLayout>
        <template #header>
            <h2 class="font-display font-semibold text-xl text-gray-800 leading-tight">Company Settings</h2>
        </template>

        <div class="py-12 max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-l1 sm:rounded-2xl border border-neutral-light p-8">
                
                <div class="mb-8 border-b border-neutral-light pb-6">
                    <h3 class="font-display text-2xl text-charcoal mb-1">Workspace Identity</h3>
                    <p class="font-body text-neutral-stone text-sm">Set up your brand presence for the public job board.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6 font-body">
                    
                    <div>
                        <label class="block text-sm font-medium text-charcoal mb-1">Company Name</label>
                        <input v-model="form.company_name" type="text" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" placeholder="Acme Corp" />
                        <div v-if="form.errors.company_name" class="text-red-500 text-xs mt-1">{{ form.errors.company_name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-charcoal mb-1">Website URL</label>
                        <input v-model="form.website" type="url" class="w-full rounded-lg border-neutral-light focus:border-jobnode-emerald focus:ring-jobnode-emerald text-[16px]" placeholder="https://acme.com" />
                        <div v-if="form.errors.website" class="text-red-500 text-xs mt-1">{{ form.errors.website }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-charcoal mb-1">Company Logo (Square)</label>
                        <input type="file" @input="form.logo = $event.target.files[0]" accept="image/*" class="w-full text-sm text-neutral-stone file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-jobnode-emerald/10 file:text-jobnode-emeraldHover hover:file:bg-jobnode-emerald/20" />
                        <div v-if="form.errors.logo" class="text-red-500 text-xs mt-1">{{ form.errors.logo }}</div>
                        
                        <!-- Existing Logo Display -->
                        <div v-if="props.company?.logo_path" class="mt-4 flex items-center gap-3 bg-neutral-light/20 p-3 rounded-lg border border-neutral-light w-fit">
                            <img :src="'/storage/' + props.company.logo_path" class="w-12 h-12 rounded-lg object-cover border border-neutral-light" alt="Company Logo" />
                            <div>
                                <p class="text-xs font-semibold text-charcoal">Current Active Logo</p>
                                <p class="text-[10px] text-neutral-stone">Displayed on your job listings</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-neutral-light">
                        <span v-if="form.recentlySuccessful" class="text-sm text-green-600 font-medium">✓ Saved successfully.</span>
                        <PrimaryButton type="submit" :disabled="form.processing">Update Brand</PrimaryButton>
                    </div>

                </form>

            </div>
        </div>
    </EmployerLayout>
</template>
