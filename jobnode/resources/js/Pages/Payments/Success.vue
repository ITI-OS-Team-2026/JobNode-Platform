<script setup>
import EmployerLayout from '@/Layouts/EmployerLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircleIcon } from '@heroicons/vue/24/solid';

defineProps({
    application_id: {
        type: Number,
        required: true,
    },
});
</script>

<template>
    <Head title="Payment Successful" />

    <EmployerLayout>
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-10 text-center">
                        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                            <CheckCircleIcon class="h-12 w-12 text-green-600" aria-hidden="true" />
                        </div>
                        
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            Payment Successful!
                        </h2>
                        
                        <p class="text-lg text-gray-600 mb-8 max-w-lg mx-auto">
                            Thank you! Your payment has been processed successfully and the candidate's contact information has been unlocked.
                        </p>

                        <Link
                            v-if="application_id"
                            :href="route('employer.applications.show', application_id)"
                            class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-150 ease-in-out"
                        >
                            View Candidate Details
                        </Link>
                        <Link
                            v-else
                            :href="route('employer.applications.index')"
                            class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-150 ease-in-out"
                        >
                            Back to Applications
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </EmployerLayout>
</template>
