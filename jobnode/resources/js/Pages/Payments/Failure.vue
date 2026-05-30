<script setup>
import EmployerLayout from '@/Layouts/EmployerLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { XCircleIcon } from '@heroicons/vue/24/solid';

defineProps({
    application_id: {
        type: Number,
        required: false,
    },
    error: {
        type: String,
        default: 'We could not complete your payment.',
    }
});
</script>

<template>
    <Head title="Payment Not Completed" />

    <EmployerLayout>
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-10 text-center">
                        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 mb-6">
                            <XCircleIcon class="h-12 w-12 text-red-600" aria-hidden="true" />
                        </div>
                        
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            Payment Not Completed
                        </h2>
                        
                        <p class="text-lg text-gray-600 mb-8 max-w-lg mx-auto">
                            {{ error }}
                        </p>

                        <div class="flex flex-col sm:flex-row justify-center gap-4">
                            <Link
                                v-if="application_id"
                                :href="route('employer.applications.show', application_id)"
                                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-150 ease-in-out"
                            >
                                Try Again
                            </Link>
                            
                            <Link
                                :href="route('employer.applications.index')"
                                class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-150 ease-in-out"
                            >
                                Back to Applications
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </EmployerLayout>
</template>
