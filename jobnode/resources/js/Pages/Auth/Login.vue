<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const inputClass = 'mt-1.5 block w-full bg-slate-950/60 border-slate-700 text-white placeholder:text-slate-500 focus:border-emerald-500 focus:ring-emerald-500';

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout title="Welcome back" subtitle="Sign in to continue to your dashboard.">
        <Head title="Log in" />

        <div v-if="status" class="mb-5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Email address" class="!text-slate-300" />

                <TextInput
                    id="email"
                    type="email"
                    :class="inputClass"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@example.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <InputLabel for="password" value="Password" class="!text-slate-300" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs font-medium text-emerald-400 hover:text-emerald-300 transition"
                    >
                        Forgot password?
                    </Link>
                </div>

                <TextInput
                    id="password"
                    type="password"
                    :class="inputClass"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <label class="flex items-center gap-2.5 cursor-pointer">
                <Checkbox name="remember" v-model:checked="form.remember" />
                <span class="text-sm text-slate-400">Remember me on this device</span>
            </label>

            <PrimaryButton
                class="w-full !rounded-xl !py-3.5"
                :class="{ 'opacity-50': form.processing }"
                :disabled="form.processing"
            >
                Sign in
            </PrimaryButton>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-800 text-center">
            <p class="text-sm text-slate-400">
                Don't have an account?
                <Link :href="route('register')" class="font-semibold text-emerald-400 hover:text-emerald-300 transition ml-1">
                    Create one
                </Link>
            </p>
        </div>
    </GuestLayout>
</template>
