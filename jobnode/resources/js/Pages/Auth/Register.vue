<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { BriefcaseIcon, UserCircleIcon } from '@heroicons/vue/24/outline';

const form = useForm({
    name: '',
    email: '',
    role: 'candidate',
    password: '',
    password_confirmation: '',
});

const inputClass = 'mt-1.5 block w-full bg-slate-950/60 border-slate-700 text-white placeholder:text-slate-500 focus:border-emerald-500 focus:ring-emerald-500';

const roleOptions = [
    {
        value: 'candidate',
        label: 'Candidate',
        description: 'Search jobs and apply with your profile',
        icon: UserCircleIcon,
    },
    {
        value: 'employer',
        label: 'Employer',
        description: 'Post roles and review applicants',
        icon: BriefcaseIcon,
    },
];

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout title="Create your account" subtitle="Join JobNode as a candidate or employer.">
        <Head title="Register" />

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel value="I am joining as" class="!text-slate-300" />

                <div class="mt-2 grid grid-cols-2 gap-3">
                    <button
                        v-for="option in roleOptions"
                        :key="option.value"
                        type="button"
                        @click="form.role = option.value"
                        class="relative flex flex-col items-start rounded-xl border p-4 text-left transition-all duration-200"
                        :class="form.role === option.value
                            ? 'border-emerald-500/60 bg-emerald-500/10 ring-1 ring-emerald-500/40'
                            : 'border-slate-700 bg-slate-950/40 hover:border-slate-600 hover:bg-slate-950/60'"
                    >
                        <component
                            :is="option.icon"
                            class="h-5 w-5 mb-2"
                            :class="form.role === option.value ? 'text-emerald-400' : 'text-slate-500'"
                        />
                        <span class="text-sm font-semibold text-white">{{ option.label }}</span>
                        <span class="mt-1 text-xs leading-snug text-slate-500">{{ option.description }}</span>
                        <span
                            v-if="form.role === option.value"
                            class="absolute top-3 right-3 h-2 w-2 rounded-full bg-emerald-400"
                        ></span>
                    </button>
                </div>

                <InputError class="mt-2" :message="form.errors.role" />
            </div>

            <div>
                <InputLabel for="name" value="Full name" class="!text-slate-300" />

                <TextInput
                    id="name"
                    type="text"
                    :class="inputClass"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="John Doe"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email address" class="!text-slate-300" />

                <TextInput
                    id="email"
                    type="email"
                    :class="inputClass"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="you@example.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <InputLabel for="password" value="Password" class="!text-slate-300" />

                    <TextInput
                        id="password"
                        type="password"
                        :class="inputClass"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Confirm password" class="!text-slate-300" />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        :class="inputClass"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />

                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <PrimaryButton
                class="w-full !rounded-xl !py-3.5"
                :class="{ 'opacity-50': form.processing }"
                :disabled="form.processing"
            >
                Create account
            </PrimaryButton>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-800 text-center">
            <p class="text-sm text-slate-400">
                Already have an account?
                <Link :href="route('login')" class="font-semibold text-emerald-400 hover:text-emerald-300 transition ml-1">
                    Sign in
                </Link>
            </p>
        </div>
    </GuestLayout>
</template>
