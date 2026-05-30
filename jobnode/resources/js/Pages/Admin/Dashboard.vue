<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    UsersIcon,
    BriefcaseIcon,
    DocumentTextIcon,
    ClockIcon,
    CurrencyDollarIcon,
    ArrowTrendingUpIcon,
    UserCircleIcon,
    ShieldCheckIcon,
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
    pendingJobsPreview: Array,
    activityFeed: Array,
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
};

const jobsChartData = computed(() => ({
    labels: props.charts.jobs_trend.map(i => i.date),
    datasets: [{
        label: 'New Jobs',
        data: props.charts.jobs_trend.map(i => i.count),
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

const userChartData = computed(() => ({
    labels: props.charts.user_distribution.map(i => i.role),
    datasets: [{
        label: 'Users',
        data: props.charts.user_distribution.map(i => i.count),
        backgroundColor: ['#10b981', '#0ea5e9', '#6366f1'],
        borderRadius: 4,
    }],
}));

const jobStatusChartData = computed(() => ({
    labels: props.charts.job_status_breakdown.map(i => i.status),
    datasets: [{
        label: 'Jobs',
        data: props.charts.job_status_breakdown.map(i => i.count),
        backgroundColor: ['#22c55e', '#f59e0b', '#ef4444'],
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

const activityIcon = (type) => {
    if (type === 'job') return BriefcaseIcon;
    if (type === 'user') return UserCircleIcon;
    return DocumentTextIcon;
};

const activityClass = (type) => {
    if (type === 'job') return 'bg-amber-50 text-amber-600';
    if (type === 'user') return 'bg-indigo-50 text-indigo-600';
    return 'bg-teal-50 text-teal-600';
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>


        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

                <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl p-8 relative overflow-hidden shadow-dark-l1 flex flex-col md:flex-row md:items-center justify-between border border-jobnode-emerald/20">
                    <div class="absolute inset-0 bg-[url('/img/grid-pattern.svg')] opacity-10"></div>
                    <div class="relative z-10 text-white mb-6 md:mb-0">
                        <h3 class="font-display text-3xl font-bold mb-2">Welcome, {{ $page.props.auth.user.name }}</h3>
                        <p class="font-body text-jobnode-emerald/90 text-lg">
                            Platform overview — {{ metrics.total_users }} users, {{ metrics.total_jobs }} jobs, {{ metrics.pending_jobs }} awaiting review.
                        </p>
                    </div>
                    <div class="relative z-10 flex flex-wrap gap-3">
                        <Link
                            :href="route('admin.jobs.pending')"
                            class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-sm font-medium rounded-lg transition backdrop-blur-sm border border-white/10"
                        >
                            <ClockIcon class="w-4 h-4 mr-2" />
                            Pending Jobs
                            <span v-if="metrics.pending_jobs > 0" class="ml-2 bg-jobnode-emerald text-charcoal text-xs font-bold px-2 py-0.5 rounded-full">{{ metrics.pending_jobs }}</span>
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 group hover:shadow-md transition duration-300">
                        <div class="flex justify-between items-start mb-2">
                            <div class="bg-indigo-50 text-indigo-600 p-2 rounded-lg"><UsersIcon class="w-5 h-5" /></div>
                            <div class="flex items-center space-x-1 text-sm font-medium text-jobnode-emerald">
                                <span>+{{ metrics.new_users_this_week }}</span>
                                <ArrowTrendingUpIcon class="w-3 h-3" />
                            </div>
                        </div>
                        <div class="text-gray-500 text-sm font-medium mb-1">Total Users</div>
                        <div class="text-2xl font-bold text-gray-900">{{ metrics.total_users.toLocaleString() }}</div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 group hover:shadow-md transition duration-300">
                        <div class="flex justify-between items-start mb-2">
                            <div class="bg-teal-50 text-teal-600 p-2 rounded-lg"><BriefcaseIcon class="w-5 h-5" /></div>
                        </div>
                        <div class="text-gray-500 text-sm font-medium mb-1">Total Jobs</div>
                        <div class="text-2xl font-bold text-gray-900">{{ metrics.total_jobs.toLocaleString() }}</div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 group hover:shadow-md transition duration-300">
                        <div class="flex justify-between items-start mb-2">
                            <div class="bg-amber-50 text-amber-600 p-2 rounded-lg"><ClockIcon class="w-5 h-5" /></div>
                        </div>
                        <div class="text-gray-500 text-sm font-medium mb-1">Pending Review</div>
                        <div class="text-2xl font-bold text-gray-900">{{ metrics.pending_jobs.toLocaleString() }}</div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 group hover:shadow-md transition duration-300">
                        <div class="flex justify-between items-start mb-2">
                            <div class="bg-jobnode-emerald/10 text-jobnode-emerald p-2 rounded-lg"><DocumentTextIcon class="w-5 h-5" /></div>
                            <div class="flex items-center space-x-1 text-sm font-medium text-jobnode-emerald">
                                <span>+{{ metrics.applications_this_week }}</span>
                                <ArrowTrendingUpIcon class="w-3 h-3" />
                            </div>
                        </div>
                        <div class="text-gray-500 text-sm font-medium mb-1">Applications</div>
                        <div class="text-2xl font-bold text-gray-900">{{ metrics.total_applications.toLocaleString() }}</div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 group hover:shadow-md transition duration-300">
                        <div class="flex justify-between items-start mb-2">
                            <div class="bg-emerald-50 text-emerald-600 p-2 rounded-lg"><CurrencyDollarIcon class="w-5 h-5" /></div>
                        </div>
                        <div class="text-gray-500 text-sm font-medium mb-1">Platform Revenue</div>
                        <div class="text-2xl font-bold text-gray-900">${{ metrics.total_revenue.toLocaleString() }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Job Posting Trend</h3>
                                <p class="text-sm text-gray-500">New job listings over the last 7 days</p>
                            </div>
                        </div>
                        <div class="h-72 w-full">
                            <Line :data="jobsChartData" :options="chartOptions" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">User Distribution</h3>
                            <p class="text-sm text-gray-500">{{ metrics.total_candidates }} candidates · {{ metrics.total_employers }} employers</p>
                        </div>
                        <div class="h-64 w-full flex-grow">
                            <Bar :data="userChartData" :options="chartOptions" />
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Job Status Overview</h3>
                            <p class="text-sm text-gray-500">{{ metrics.approved_jobs }} approved · {{ metrics.rejected_jobs }} rejected</p>
                        </div>
                        <div class="h-64 w-full flex-grow">
                            <Bar :data="jobStatusChartData" :options="chartOptions" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <h3 class="text-lg font-semibold text-gray-900">Pending Approvals</h3>
                            <Link :href="route('admin.jobs.pending')" class="text-sm font-medium text-emerald-600 hover:text-emerald-800 transition-colors">
                                View queue
                            </Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <tr v-for="job in pendingJobsPreview" :key="job.id" class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ job.title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ job.company_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">{{ job.location }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(job.created_at) }}</td>
                                    </tr>
                                    <tr v-if="!pendingJobsPreview.length">
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 text-sm">No pending jobs — all caught up!</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <h3 class="text-lg font-semibold text-gray-900">Platform Activity</h3>
                        </div>
                        <div class="p-6">
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    <li v-for="(item, itemIdx) in activityFeed" :key="itemIdx">
                                        <div class="relative pb-8">
                                            <span v-if="itemIdx !== activityFeed.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white" :class="activityClass(item.type)">
                                                        <component :is="activityIcon(item.type)" class="w-5 h-5" />
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
                                        <div class="text-center text-sm text-gray-500 py-4">No recent platform activity</div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
