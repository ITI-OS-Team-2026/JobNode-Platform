<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    applications: Object,
});

const getStatusColor = (status) => {
    switch (status) {
        case "applied":
            return "bg-teal-100 text-teal-800";
        case "reviewed":
            return "bg-emerald-100 text-emerald-800";
        case "shortlisted":
            return "bg-green-100 text-green-800";
        case "rejected":
            return "bg-red-100 text-red-800";
        default:
            return "bg-gray-100 text-gray-800";
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};
</script>

<template>
    <Head title="Applications" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-display font-semibold text-xl text-gray-800 leading-tight"
            >
                Applications
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Empty State -->
                <div
                    v-if="applications.data.length === 0"
                    class="bg-white overflow-hidden shadow-l1 sm:rounded-2xl border border-neutral-light p-12 text-center"
                >
                    <h3 class="font-display text-2xl mb-2 text-charcoal">
                        No applications yet
                    </h3>
                    <p class="font-body text-neutral-stone">
                        Applications from candidates will appear here once you
                        post jobs.
                    </p>
                </div>

                <!-- Applications Table -->
                <div
                    v-else
                    class="bg-white overflow-hidden shadow-l1 sm:rounded-2xl border border-neutral-light"
                >
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead
                                class="bg-gray-50 border-b border-gray-100"
                            >
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left font-body font-semibold text-charcoal"
                                    >
                                        Candidate
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left font-body font-semibold text-charcoal"
                                    >
                                        Job Title
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left font-body font-semibold text-charcoal"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left font-body font-semibold text-charcoal"
                                    >
                                        Date Applied
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left font-body font-semibold text-charcoal"
                                    >
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="application in applications.data"
                                    :key="application.id"
                                    class="border-b border-gray-100 hover:bg-emerald-50/50 transition-colors cursor-pointer group"
                                    @click="$inertia.visit(route('employer.applications.show', application.id))"
                                >
                                    <td class="px-6 py-5">
                                        <p class="font-body font-semibold text-gray-900 group-hover:text-emerald-700 transition-colors">
                                            {{ application.candidate.name }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-5">
                                        <p class="font-body text-gray-600 font-medium">
                                            {{ application.job.title }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span
                                            :class="`px-3 py-1 rounded-full text-xs font-semibold capitalize ${getStatusColor(application.status)}`"
                                        >
                                            {{ application.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <p class="font-body text-gray-500 text-sm font-medium">
                                            {{ formatDate(application.created_at) }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center justify-center px-4 py-2 bg-white border border-emerald-200 text-emerald-700 rounded-lg text-sm font-semibold group-hover:bg-emerald-600 group-hover:text-white group-hover:border-emerald-600 transition-all shadow-sm">
                                            Review Application
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="applications.last_page > 1"
                        class="px-6 py-4 border-t border-neutral-light flex items-center justify-between"
                    >
                        <p class="font-body text-sm text-neutral-stone">
                            Showing
                            <span class="font-semibold">{{
                                applications.from
                            }}</span>
                            to
                            <span class="font-semibold">{{
                                applications.to
                            }}</span>
                            of
                            <span class="font-semibold">{{
                                applications.total
                            }}</span>
                            applications
                        </p>
                        <div class="flex gap-2">
                            <Link
                                v-if="applications.prev_page_url"
                                :href="applications.prev_page_url"
                                class="px-3 py-1 border border-neutral-light rounded-lg font-body text-sm hover:bg-gray-50 transition-colors"
                            >
                                Previous
                            </Link>
                            <Link
                                v-if="applications.next_page_url"
                                :href="applications.next_page_url"
                                class="px-3 py-1 border border-neutral-light rounded-lg font-body text-sm hover:bg-gray-50 transition-colors"
                            >
                                Next
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
