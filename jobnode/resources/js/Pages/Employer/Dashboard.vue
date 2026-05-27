<script setup>
import EmployerLayout from '@/Layouts/EmployerLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    BriefcaseIcon, 
    EyeIcon, 
    UsersIcon, 
    KeyIcon,
    ChevronRightIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    CurrencyDollarIcon,
    ChartBarIcon,
    PlusIcon,
    DocumentTextIcon,
    CheckCircleIcon,
    ClockIcon,
    UserCircleIcon
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
  Filler
} from 'chart.js';
import { Bar, Line } from 'vue-chartjs';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, LineElement, PointElement, Filler);

const props = defineProps({
    metrics: Object,
    charts: Object,
    topPerformingJobs: Array,
    activityFeed: Array,
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric',
        hour: '2-digit', minute:'2-digit'
    });
};

const getTrend = (metricName) => {
    // Simulated trends for the SaaS feel
    const trends = {
        'active_jobs': { value: '+2', type: 'up' },
        'total_views': { value: '+14%', type: 'up' },
        'total_applicants': { value: '+24%', type: 'up' },
        'conversion_rate': { value: '+1.2%', type: 'up' },
        'total_revenue': { value: '+8%', type: 'up' },
        'candidate_unlocks': { value: '+3', type: 'up' },
    };
    return trends[metricName] || { value: '0%', type: 'neutral' };
};

// Chart Data setup
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
    }]
}));

const revenueChartData = computed(() => ({
    labels: props.charts.revenue_trend.map(i => i.date),
    datasets: [{
        label: 'Revenue ($)',
        data: props.charts.revenue_trend.map(i => i.total),
        backgroundColor: '#6366f1',
        borderRadius: 4,
    }]
}));

const jobPerformanceChartData = computed(() => ({
    labels: props.charts.job_performance.map(i => i.title),
    datasets: [{
        label: 'Views',
        data: props.charts.job_performance.map(i => i.views),
        backgroundColor: '#f59e0b',
        borderRadius: 4,
    }]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                title: function(context) {
                    return context[0].label;
                }
            }
        }
    },
    scales: {
        y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#f3f4f6' } },
        x: { 
            grid: { display: false },
            ticks: {
                callback: function(value) {
                    const label = this.getLabelForValue(value);
                    if (label.length > 15) {
                        return label.substring(0, 15) + '...';
                    }
                    return label;
                }
            }
        }
    }
};

const isEmptyState = computed(() => {
    return props.metrics.active_jobs === 0 && props.metrics.total_views === 0 && props.metrics.total_applicants === 0;
});
</script>

<template>
    <Head title="Employer Analytics Workspace" />

    <EmployerLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-display font-semibold text-xl text-gray-800 leading-tight">Analytics Workspace</h2>
                <div class="flex items-center space-x-3">
                    <Link :href="route('employer.jobs.create')" class="inline-flex items-center px-4 py-2 bg-charcoal text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition shadow-sm">
                        <PlusIcon class="w-4 h-4 mr-2" />
                        Create Job
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

                <template v-if="!isEmptyState">
                    <!-- Welcome & Quick Actions -->
                    <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl p-8 relative overflow-hidden shadow-dark-l1 flex flex-col md:flex-row md:items-center justify-between border border-jobnode-emerald/20">
                        <div class="absolute inset-0 bg-[url('/img/grid-pattern.svg')] opacity-10"></div>
                        <div class="relative z-10 text-white mb-6 md:mb-0">
                            <h3 class="font-display text-3xl font-bold mb-2">Welcome back, {{ $page.props.auth.user.name }}</h3>
                            <p class="font-body text-jobnode-emerald/90 text-lg">Here's your advanced analytics overview for this week.</p>
                        </div>
                        <div class="relative z-10 flex flex-wrap gap-3">
                            <Link :href="route('employer.applications.index')" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-sm font-medium rounded-lg transition backdrop-blur-sm border border-white/10">
                                <DocumentTextIcon class="w-4 h-4 mr-2" /> Review Applications
                                <span v-if="metrics.pending_applications_count > 0" class="ml-2 bg-jobnode-emerald text-charcoal text-xs font-bold px-2 py-0.5 rounded-full">{{ metrics.pending_applications_count }}</span>
                            </Link>
                            <Link :href="route('employer.jobs.index')" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-sm font-medium rounded-lg transition backdrop-blur-sm border border-white/10">
                                <BriefcaseIcon class="w-4 h-4 mr-2" /> Manage Listings
                            </Link>
                        </div>
                    </div>

                    <!-- Primary Metric Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <!-- Views -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 group hover:shadow-md transition duration-300">
                            <div class="flex justify-between items-start mb-2">
                                <div class="bg-teal-50 text-teal-600 p-2 rounded-lg group-hover:scale-110 transition-transform"><EyeIcon class="w-5 h-5"/></div>
                                <div class="flex items-center space-x-1 text-sm font-medium" :class="getTrend('total_views').type === 'up' ? 'text-jobnode-emerald' : 'text-red-500'">
                                    <span>{{ getTrend('total_views').value }}</span>
                                    <ArrowTrendingUpIcon v-if="getTrend('total_views').type === 'up'" class="w-3 h-3" />
                                </div>
                            </div>
                            <div class="text-gray-500 text-sm font-medium mb-1">Total Views</div>
                            <div class="text-2xl font-bold text-gray-900">{{ metrics.total_views.toLocaleString() }}</div>
                        </div>

                        <!-- Applicants -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 group hover:shadow-md transition duration-300">
                            <div class="flex justify-between items-start mb-2">
                                <div class="bg-jobnode-emerald/10 text-jobnode-emerald p-2 rounded-lg group-hover:scale-110 transition-transform"><UsersIcon class="w-5 h-5"/></div>
                                <div class="flex items-center space-x-1 text-sm font-medium" :class="getTrend('total_applicants').type === 'up' ? 'text-jobnode-emerald' : 'text-red-500'">
                                    <span>{{ getTrend('total_applicants').value }}</span>
                                    <ArrowTrendingUpIcon v-if="getTrend('total_applicants').type === 'up'" class="w-3 h-3" />
                                </div>
                            </div>
                            <div class="text-gray-500 text-sm font-medium mb-1">Total Applicants</div>
                            <div class="text-2xl font-bold text-gray-900">{{ metrics.total_applicants.toLocaleString() }}</div>
                        </div>

                        <!-- Conversion Rate -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 group hover:shadow-md transition duration-300">
                            <div class="flex justify-between items-start mb-2">
                                <div class="bg-emerald-50 text-emerald-600 p-2 rounded-lg group-hover:scale-110 transition-transform"><ChartBarIcon class="w-5 h-5"/></div>
                                <div class="flex items-center space-x-1 text-sm font-medium" :class="getTrend('conversion_rate').type === 'up' ? 'text-jobnode-emerald' : 'text-red-500'">
                                    <span>{{ getTrend('conversion_rate').value }}</span>
                                    <ArrowTrendingUpIcon v-if="getTrend('conversion_rate').type === 'up'" class="w-3 h-3" />
                                </div>
                            </div>
                            <div class="text-gray-500 text-sm font-medium mb-1">Conversion Rate</div>
                            <div class="text-2xl font-bold text-gray-900">{{ metrics.conversion_rate }}%</div>
                        </div>

                        <!-- Unlocks -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 group hover:shadow-md transition duration-300">
                            <div class="flex justify-between items-start mb-2">
                                <div class="bg-amber-50 text-amber-600 p-2 rounded-lg group-hover:scale-110 transition-transform"><KeyIcon class="w-5 h-5"/></div>
                                <div class="flex items-center space-x-1 text-sm font-medium" :class="getTrend('candidate_unlocks').type === 'up' ? 'text-jobnode-emerald' : 'text-red-500'">
                                    <span>{{ getTrend('candidate_unlocks').value }}</span>
                                    <ArrowTrendingUpIcon v-if="getTrend('candidate_unlocks').type === 'up'" class="w-3 h-3" />
                                </div>
                            </div>
                            <div class="text-gray-500 text-sm font-medium mb-1">Profiles Unlocked</div>
                            <div class="text-2xl font-bold text-gray-900">{{ metrics.candidate_unlocks.toLocaleString() }}</div>
                        </div>

                        <!-- Revenue -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 group hover:shadow-md transition duration-300">
                            <div class="flex justify-between items-start mb-2">
                                <div class="bg-teal-50 text-teal-600 p-2 rounded-lg group-hover:scale-110 transition-transform"><CurrencyDollarIcon class="w-5 h-5"/></div>
                                <div class="flex items-center space-x-1 text-sm font-medium" :class="getTrend('total_revenue').type === 'up' ? 'text-jobnode-emerald' : 'text-red-500'">
                                    <span>{{ getTrend('total_revenue').value }}</span>
                                    <ArrowTrendingUpIcon v-if="getTrend('total_revenue').type === 'up'" class="w-3 h-3" />
                                </div>
                            </div>
                            <div class="text-gray-500 text-sm font-medium mb-1">Total Paid</div>
                            <div class="text-2xl font-bold text-gray-900">${{ metrics.total_revenue.toLocaleString() }}</div>
                        </div>
                    </div>

                    <!-- Charts Grid Row 1 -->
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Main Trend Chart -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Application Trend</h3>
                                    <p class="text-sm text-gray-500">Applications received over the last 7 days</p>
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

                    <!-- Charts Grid Row 2 -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Job Performance Chart -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Job Views</h3>
                                <p class="text-sm text-gray-500">Top performing jobs by view count</p>
                            </div>
                            <div class="h-64 w-full flex-grow">
                                <Bar :data="jobPerformanceChartData" :options="chartOptions" />
                            </div>
                        </div>

                        <!-- Revenue Chart -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Unlock Revenue</h3>
                                <p class="text-sm text-gray-500">Revenue generated from profile unlocks (7 Days)</p>
                            </div>
                            <div class="h-64 w-full flex-grow">
                                <Bar :data="revenueChartData" :options="chartOptions" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Top Performing Jobs Table -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                <h3 class="text-lg font-semibold text-gray-900">Top Performing Jobs</h3>
                                <Link :href="route('employer.jobs.index')" class="text-sm font-medium text-emerald-600 hover:text-emerald-800 transition-colors">
                                    View all jobs
                                </Link>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicants</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        <tr v-for="job in topPerformingJobs" :key="job.id" class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ job.title }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ job.views_count.toLocaleString() }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ job.applications_count.toLocaleString() }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800" v-if="job.status === 'approved'">Active</span>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800" v-else-if="job.status === 'pending'">Pending</span>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800" v-else>{{ job.status }}</span>
                                            </td>
                                        </tr>
                                        <tr v-if="!topPerformingJobs.length">
                                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 text-sm">No jobs available</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Application Activity Feed -->
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
                                                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white"
                                                            :class="{
                                                                'bg-teal-50 text-teal-600': item.type === 'application',
                                                                'bg-amber-50 text-amber-600': item.type === 'unlock',
                                                                'bg-emerald-50 text-emerald-600': item.type === 'payment'
                                                            }">
                                                            <UserCircleIcon v-if="item.type === 'application'" class="w-5 h-5" />
                                                            <KeyIcon v-else-if="item.type === 'unlock'" class="w-5 h-5" />
                                                            <CurrencyDollarIcon v-else-if="item.type === 'payment'" class="w-5 h-5" />
                                                        </span>
                                                    </div>
                                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                        <div>
                                                            <p class="text-sm text-gray-500">{{ item.title }}</p>
                                                        </div>
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
                </template>

                <!-- Empty State for New Employers -->
                <template v-else>
                    <div class="bg-white sm:rounded-2xl shadow-sm border border-gray-100 p-12 text-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-16 -mr-16 bg-jobnode-emerald/5 rounded-full w-64 h-64 blur-3xl"></div>
                        <div class="absolute bottom-0 left-0 -mb-16 -ml-16 bg-emerald-50 rounded-full w-64 h-64 blur-3xl"></div>
                        
                        <div class="relative z-10">
                            <div class="mx-auto w-20 h-20 bg-gray-50 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-center mb-6">
                                <ChartBarIcon class="w-10 h-10 text-gray-400" />
                            </div>
                            <h2 class="font-display text-2xl font-bold text-gray-900 mb-2">Welcome to your Analytics Workspace</h2>
                            <p class="text-gray-500 max-w-lg mx-auto mb-8 text-lg">
                                Your dashboard is currently empty. Start posting job listings to track views, receive applications, and hire top candidates.
                            </p>
                            <Link :href="route('employer.jobs.create')" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-charcoal hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all hover:shadow-lg">
                                <PlusIcon class="w-5 h-5 mr-2" />
                                Create Your First Job Listing
                            </Link>
                        </div>
                    </div>
                </template>

            </div>
        </div>
    </EmployerLayout>
</template>
