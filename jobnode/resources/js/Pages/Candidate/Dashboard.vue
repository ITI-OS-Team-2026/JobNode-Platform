<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    BriefcaseIcon,
    DocumentTextIcon,
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon,
    UserCircleIcon,
    ArrowTrendingUpIcon,
    ChartBarIcon,
    MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline';
import { computed } from 'vue';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    LineElement,
    PointElement,
    Filler,
} from 'chart.js';
import { Bar, Line } from 'vue-chartjs';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, LineElement, PointElement, Filler);

const props = defineProps({
    metrics: Object,
    charts: Object,
    recentApplications: Array,
    activityFeed: Array,
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
};

const statusLabel = (status) => {
    const labels = {
        applied: 'Pending',
        reviewed: 'Under Review',
        accepted: 'Accepted',
        rejected: 'Rejected',
        cancelled: 'Cancelled',
    };
    return labels[status] || status;
};

const statusBadgeClass = (status) => {
    const classes = {
        applied: 'bg-jobnode-emerald/10 text-jobnode-emeraldHover',
        reviewed: 'bg-blue-100 text-blue-800',
        accepted: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        cancelled: 'bg-gray-100 text-gray-600',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const applicationsChartData = computed(() => ({
    labels: props.charts.applications_trend.map(i => i.date),
    datasets: [{
        label: 'Applications',
        data: props.charts.applications_trend.map(i => i.count),
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        borderWidth: 2,
        tension: 0.4,
        fill: true,
        pointBackgroundColor: '#10b981',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 4,
    }],
}));

const statusChartData = computed(() => ({
    labels: props.charts.status_breakdown.map(i => statusLabel(i.status)),
    datasets: [{
        label: 'Applications',
        data: props.charts.status_breakdown.map(i => i.count),
        backgroundColor: ['#10b981', '#0ea5e9', '#22c55e', '#ef4444', '#9ca3af'],
        borderRadius: 4,
    }],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#f3f4f6' } },
        x: { grid: { display: false } },
    },
};

const isEmptyState = computed(() => props.metrics.active_applications === 0);
</script>

<template>
    <Head title="Candidate Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-display font-semibold text-xl text-gray-800 leading-tight">Application Overview</h2>
                <Link
                    :href="route('jobs.index')"
                    class="inline-flex items-center px-4 py-2 bg-charcoal text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition shadow-sm"
                >
                    <MagnifyingGlassIcon class="w-4 h-4 mr-2" />
                    Browse Jobs
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

                <template v-if="!isEmptyState">
                    <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl p-8 relative overflow-hidden shadow-dark-l1 flex flex-col md:flex-row md:items-center justify-between border border-jobnode-emerald/20">
                        <div class="absolute inset-0 bg-[url('/img/grid-pattern.svg')] opacity-10"></div>
                        <div class="relative z-10 text-white mb-6 md:mb-0">
                            <h3 class="font-display text-3xl font-bold mb-2">Welcome back, {{ $page.props.auth.user.name }}</h3>
                            <p class="font-body text-jobnode-emerald/90 text-lg">
                                You have {{ metrics.active_applications }} active application{{ metrics.active_applications === 1 ? '' : 's' }} and {{ metrics.remaining_slots }} slot{{ metrics.remaining_slots === 1 ? '' : 's' }} remaining.
                            </p>
                        </div>
                        <div class="relative z-10 flex flex-wrap gap-3">
                            <Link
                                :href="route('candidate.applications')"
                                class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-sm font-medium rounded-lg transition backdrop-blur-sm border border-white/10"
                            >
                                <DocumentTextIcon class="w-4 h-4 mr-2" />
                                My Applications
                                <span v-if="metrics.pending_count > 0" class="ml-2 bg-jobnode-emerald text-charcoal text-xs font-bold px-2 py-0.5 rounded-full">{{ metrics.pending_count }}</span>
                            </Link>
                            <Link
                                :href="route('candidate.profile')"
                                class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-sm font-medium rounded-lg transition backdrop-blur-sm border border-white/10"
                            >
                                <UserCircleIcon class="w-4 h-4 mr-2" />
                                {{ metrics.profile_complete ? 'View Profile' : 'Complete Profile' }}
                            </Link>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 group hover:shadow-md transition duration-300">
                            <div class="flex justify-between items-start mb-2">
                                <div class="bg-jobnode-emerald/10 text-jobnode-emerald p-2 rounded-lg"><DocumentTextIcon class="w-5 h-5" /></div>
                                <div class="flex items-center space-x-1 text-sm font-medium text-jobnode-emerald">
                                    <span>+{{ metrics.applications_this_week }}</span>
                                    <ArrowTrendingUpIcon class="w-3 h-3" />
                                </div>
                            </div>
                            <div class="text-gray-500 text-sm font-medium mb-1">Active Applications</div>
                            <div class="text-2xl font-bold text-gray-900">{{ metrics.active_applications }}</div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 group hover:shadow-md transition duration-300">
                            <div class="flex justify-between items-start mb-2">
                                <div class="bg-amber-50 text-amber-600 p-2 rounded-lg"><ClockIcon class="w-5 h-5" /></div>
                            </div>
                            <div class="text-gray-500 text-sm font-medium mb-1">Pending Review</div>
                            <div class="text-2xl font-bold text-gray-900">{{ metrics.pending_count }}</div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 group hover:shadow-md transition duration-300">
                            <div class="flex justify-between items-start mb-2">
                                <div class="bg-green-50 text-green-600 p-2 rounded-lg"><CheckCircleIcon class="w-5 h-5" /></div>
                            </div>
                            <div class="text-gray-500 text-sm font-medium mb-1">Accepted</div>
                            <div class="text-2xl font-bold text-gray-900">{{ metrics.accepted_count }}</div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 group hover:shadow-md transition duration-300">
                            <div class="flex justify-between items-start mb-2">
                                <div class="bg-red-50 text-red-600 p-2 rounded-lg"><XCircleIcon class="w-5 h-5" /></div>
                            </div>
                            <div class="text-gray-500 text-sm font-medium mb-1">Rejected</div>
                            <div class="text-2xl font-bold text-gray-900">{{ metrics.rejected_count }}</div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 group hover:shadow-md transition duration-300">
                            <div class="flex justify-between items-start mb-2">
                                <div class="bg-teal-50 text-teal-600 p-2 rounded-lg"><ChartBarIcon class="w-5 h-5" /></div>
                            </div>
                            <div class="text-gray-500 text-sm font-medium mb-1">Profile Complete</div>
                            <div class="text-2xl font-bold text-gray-900">{{ metrics.profile_completeness }}%</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Application Activity</h3>
                                    <p class="text-sm text-gray-500">Your submissions over the last 7 days</p>
                                </div>
                                <div class="text-sm font-medium text-gray-700 bg-gray-50 px-3 py-1 rounded-full border border-gray-200">
                                    This week: <span class="text-jobnode-emerald">{{ metrics.applications_this_week }}</span>
                                </div>
                            </div>
                            <div class="h-72 w-full">
                                <Line :data="applicationsChartData" :options="chartOptions" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Status Breakdown</h3>
                                <p class="text-sm text-gray-500">All your applications by current status</p>
                            </div>
                            <div class="h-64 w-full flex-grow">
                                <Bar :data="statusChartData" :options="chartOptions" />
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                            </div>
                            <div class="p-6">
                                <div class="flow-root">
                                    <ul role="list" class="-mb-8">
                                        <li v-for="(item, itemIdx) in activityFeed" :key="itemIdx">
                                            <div class="relative pb-8">
                                                <span v-if="itemIdx !== activityFeed.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span
                                                            class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white"
                                                            :class="{
                                                                'bg-teal-50 text-teal-600': item.type === 'application',
                                                                'bg-gray-100 text-gray-500': item.type === 'cancelled',
                                                            }"
                                                        >
                                                            <DocumentTextIcon class="w-5 h-5" />
                                                        </span>
                                                    </div>
                                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                        <p class="text-sm text-gray-500">{{ item.title }}</p>
                                                        <div class="whitespace-nowrap text-right text-xs text-gray-500">
                                                            {{ formatDate(item.date) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li v-if="!activityFeed.length">
                                            <div class="text-center text-sm text-gray-500 py-4">No recent activity</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Applications</h3>
                            <Link :href="route('candidate.applications')" class="text-sm font-medium text-emerald-600 hover:text-emerald-800 transition-colors">
                                View all
                            </Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <tr v-for="app in recentApplications" :key="app.id" class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ app.job_title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ app.company_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">{{ app.location }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize" :class="statusBadgeClass(app.status)">
                                                {{ statusLabel(app.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(app.created_at) }}</td>
                                    </tr>
                                    <tr v-if="!recentApplications.length">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 text-sm">No applications yet</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <div class="bg-white sm:rounded-2xl shadow-sm border border-gray-100 p-12 text-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-16 -mr-16 bg-jobnode-emerald/5 rounded-full w-64 h-64 blur-3xl"></div>
                        <div class="absolute bottom-0 left-0 -mb-16 -ml-16 bg-emerald-50 rounded-full w-64 h-64 blur-3xl"></div>

                        <div class="relative z-10">
                            <div class="mx-auto w-20 h-20 bg-gray-50 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-center mb-6">
                                <BriefcaseIcon class="w-10 h-10 text-gray-400" />
                            </div>
                            <h2 class="font-display text-2xl font-bold text-gray-900 mb-2">Start Your Job Search</h2>
                            <p class="text-gray-500 max-w-lg mx-auto mb-4 text-lg">
                                You haven't applied to any jobs yet. Browse open roles and submit up to 12 applications.
                            </p>
                            <p v-if="!metrics.profile_complete" class="text-amber-600 text-sm mb-8">
                                Tip: Complete your profile ({{ metrics.profile_completeness }}%) to improve your chances.
                            </p>
                            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                                <Link
                                    :href="route('jobs.index')"
                                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-charcoal hover:bg-gray-800 transition-all hover:shadow-lg"
                                >
                                    <MagnifyingGlassIcon class="w-5 h-5 mr-2" />
                                    Browse Open Jobs
                                </Link>
                                <Link
                                    :href="route('candidate.profile')"
                                    class="inline-flex items-center px-6 py-3 border border-gray-200 text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition-all"
                                >
                                    <UserCircleIcon class="w-5 h-5 mr-2" />
                                    Complete Profile
                                </Link>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
