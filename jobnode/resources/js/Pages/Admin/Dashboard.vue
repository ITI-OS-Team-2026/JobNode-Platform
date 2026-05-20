<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display font-semibold text-xl text-gray-800 leading-tight">Platform Command Center</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-l1 sm:rounded-2xl border border-neutral-light p-8">
                    <h3 class="font-display text-2xl mb-2 text-charcoal">System Overview</h3>
                    <p class="font-body text-neutral-stone">Platform metrics and moderation queues will appear here.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>