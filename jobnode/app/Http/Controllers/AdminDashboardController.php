<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $totalUsers = User::count();
        $totalCandidates = User::where('role', User::ROLE_CANDIDATE)->count();
        $totalEmployers = User::where('role', User::ROLE_EMPLOYER)->count();
        $totalAdmins = User::where('role', User::ROLE_ADMIN)->count();

        $totalJobs = Job::count();
        $approvedJobs = Job::where('status', 'approved')->count();
        $pendingJobs = Job::where('status', 'pending')->count();
        $rejectedJobs = Job::where('status', 'rejected')->count();

        $totalApplications = Application::count();
        $applicationsThisWeek = Application::where('created_at', '>=', now()->subDays(7))->count();
        $newUsersThisWeek = User::where('created_at', '>=', now()->subDays(7))->count();
        $totalRevenue = (float) Payment::where('status', Payment::STATUS_PAID)->sum('amount');

        $sevenDaysAgo = now()->subDays(6)->startOfDay();

        $jobsTrendRaw = Job::where('created_at', '>=', $sevenDaysAgo)
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $jobsTrend = [];
        for ($i = 0; $i < 7; $i++) {
            $dateStr = now()->subDays(6 - $i)->format('Y-m-d');
            $jobsTrend[] = [
                'date' => now()->subDays(6 - $i)->format('M d'),
                'count' => isset($jobsTrendRaw[$dateStr]) ? $jobsTrendRaw[$dateStr]->count : 0,
            ];
        }

        $userDistribution = [
            ['role' => 'Candidates', 'count' => $totalCandidates],
            ['role' => 'Employers', 'count' => $totalEmployers],
            ['role' => 'Admins', 'count' => $totalAdmins],
        ];

        $jobStatusBreakdown = [
            ['status' => 'Approved', 'count' => $approvedJobs],
            ['status' => 'Pending', 'count' => $pendingJobs],
            ['status' => 'Rejected', 'count' => $rejectedJobs],
        ];

        $pendingJobsPreview = Job::with('employer.company')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (Job $job) => [
                'id' => $job->id,
                'title' => $job->title,
                'company_name' => $job->employer->company?->company_name ?? 'Unknown Company',
                'location' => $job->location,
                'created_at' => $job->created_at,
            ]);

        $latestJobs = Job::with('employer')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (Job $job) => [
                'type' => 'job',
                'title' => $job->employer->name . ' posted "' . $job->title . '"',
                'date' => $job->created_at,
            ]);

        $latestUsers = User::latest()
            ->take(5)
            ->get()
            ->map(fn (User $user) => [
                'type' => 'user',
                'title' => ucfirst($user->role) . ' account created: ' . $user->name,
                'date' => $user->created_at,
            ]);

        $latestApplications = Application::with(['candidate', 'job'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (Application $application) => [
                'type' => 'application',
                'title' => $application->candidate->name . ' applied to ' . $application->job->title,
                'date' => $application->created_at,
            ]);

        $activityFeed = collect($latestJobs)
            ->concat($latestUsers)
            ->concat($latestApplications)
            ->sortByDesc('date')
            ->take(8)
            ->values();

        return Inertia::render('Admin/Dashboard', [
            'metrics' => [
                'total_users' => $totalUsers,
                'total_candidates' => $totalCandidates,
                'total_employers' => $totalEmployers,
                'total_jobs' => $totalJobs,
                'approved_jobs' => $approvedJobs,
                'pending_jobs' => $pendingJobs,
                'rejected_jobs' => $rejectedJobs,
                'total_applications' => $totalApplications,
                'applications_this_week' => $applicationsThisWeek,
                'new_users_this_week' => $newUsersThisWeek,
                'total_revenue' => $totalRevenue,
            ],
            'charts' => [
                'jobs_trend' => $jobsTrend,
                'user_distribution' => $userDistribution,
                'job_status_breakdown' => $jobStatusBreakdown,
            ],
            'pendingJobsPreview' => $pendingJobsPreview,
            'activityFeed' => $activityFeed,
        ]);
    }
}
