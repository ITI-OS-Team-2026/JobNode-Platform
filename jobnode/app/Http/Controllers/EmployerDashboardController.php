<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployerDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $employer = $request->user();

        // Base metrics
        $activeJobs = $employer->postedJobs()->where('status', 'approved')->count();
        $totalViews = (int) $employer->postedJobs()->sum('views_count');
        $totalApplicants = Application::whereHas('job', function ($query) use ($employer) {
            $query->where('employer_id', $employer->id);
        })->count();
        $candidateUnlocks = $employer->unlocks()->count();

        // Advanced Analytics Metrics
        $applicationsThisWeek = Application::whereHas('job', function ($query) use ($employer) {
            $query->where('employer_id', $employer->id);
        })->where('created_at', '>=', now()->subDays(7))->count();

        $conversionRate = $totalViews > 0 ? round(($totalApplicants / $totalViews) * 100, 1) : 0;
        $averageApplicationsPerJob = $activeJobs > 0 ? round($totalApplicants / $activeJobs, 1) : 0;
        
        $mostViewedJob = $employer->postedJobs()->orderBy('views_count', 'desc')->first();
        $mostAppliedJob = $employer->postedJobs()->withCount('applications')->orderBy('applications_count', 'desc')->first();
        
        $pendingApplicationsCount = Application::whereHas('job', function ($query) use ($employer) {
            $query->where('employer_id', $employer->id);
        })->where('status', 'applied')->count();

        $totalRevenueFromUnlocks = (float) $employer->payments()->where('status', Payment::STATUS_PAID)->sum('amount');

        // Top Performing Jobs (with views, applications, status)
        $topPerformingJobs = $employer->postedJobs()
            ->withCount('applications')
            ->orderBy('views_count', 'desc')
            ->take(5)
            ->get();

        // Charts Data
        // 1. Applications Trend (last 7 days)
        $sevenDaysAgo = now()->subDays(6)->startOfDay();
        
        $applicationsTrendRaw = Application::whereHas('job', function ($query) use ($employer) {
            $query->where('employer_id', $employer->id);
        })
        ->where('created_at', '>=', $sevenDaysAgo)
        ->selectRaw('DATE(created_at) as date, count(*) as count')
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date');

        $applicationsTrend = [];
        for ($i = 0; $i < 7; $i++) {
            $dateStr = now()->subDays(6 - $i)->format('Y-m-d');
            $applicationsTrend[] = [
                'date' => now()->subDays(6 - $i)->format('M d'),
                'count' => isset($applicationsTrendRaw[$dateStr]) ? $applicationsTrendRaw[$dateStr]->count : 0
            ];
        }

        // 2. Job Performance
        $jobPerformance = $topPerformingJobs->map(function ($job) {
            return [
                'title' => substr($job->title, 0, 15) . (strlen($job->title) > 15 ? '...' : ''),
                'views' => $job->views_count,
            ];
        });

        // 3. Unlock Revenue
        $revenueTrendRaw = $employer->payments()
            ->where('status', Payment::STATUS_PAID)
            ->where('paid_at', '>=', $sevenDaysAgo)
            ->selectRaw('DATE(paid_at) as date, sum(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $revenueTrend = [];
        for ($i = 0; $i < 7; $i++) {
            $dateStr = now()->subDays(6 - $i)->format('Y-m-d');
            $revenueTrend[] = [
                'date' => now()->subDays(6 - $i)->format('M d'),
                'total' => isset($revenueTrendRaw[$dateStr]) ? (float) $revenueTrendRaw[$dateStr]->total : 0
            ];
        }

        // Application Activity Feed
        $latestApps = Application::with(['candidate', 'job'])
            ->whereHas('job', function($q) use ($employer) {
                $q->where('employer_id', $employer->id);
            })->latest()->take(5)->get()->map(function($app) {
                return [
                    'type' => 'application',
                    'title' => $app->candidate->name . ' applied for ' . $app->job->title,
                    'date' => $app->created_at,
                ];
            });

        $latestUnlocks = $employer->unlocks()->with('candidate')
            ->latest()->take(5)->get()->map(function($unlock) {
                return [
                    'type' => 'unlock',
                    'title' => 'Unlocked profile for ' . $unlock->candidate->name,
                    'date' => $unlock->created_at,
                ];
            });
            
        $latestPayments = $employer->payments()
            ->where('status', Payment::STATUS_PAID)
            ->with('application.candidate')
            ->latest()->take(5)->get()->map(function($payment) {
                $candidateName = $payment->application && $payment->application->candidate 
                    ? $payment->application->candidate->name 
                    : 'a candidate';
                return [
                    'type' => 'payment',
                    'title' => 'Payment completed for ' . $candidateName,
                    'date' => $payment->paid_at ?? $payment->created_at,
                ];
            });

        $activityFeed = collect($latestApps)
            ->concat($latestUnlocks)
            ->concat($latestPayments)
            ->sortByDesc('date')
            ->take(5)
            ->values();

        // Recent applications (for the simple list if still needed, or merged into activity)
        $recentApplications = Application::with(['candidate', 'job'])
            ->whereHas('job', function ($query) use ($employer) {
                $query->where('employer_id', $employer->id);
            })
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Employer/Dashboard', [
            'metrics' => [
                'active_jobs' => $activeJobs,
                'total_views' => $totalViews,
                'total_applicants' => $totalApplicants,
                'candidate_unlocks' => $candidateUnlocks,
                
                // New metrics
                'applications_this_week' => $applicationsThisWeek,
                'conversion_rate' => $conversionRate,
                'average_applications_per_job' => $averageApplicationsPerJob,
                'pending_applications_count' => $pendingApplicationsCount,
                'total_revenue' => $totalRevenueFromUnlocks,
                
                'most_viewed_job' => $mostViewedJob ? $mostViewedJob->title : null,
                'most_applied_job' => $mostAppliedJob ? $mostAppliedJob->title : null,
            ],
            'charts' => [
                'applications_trend' => $applicationsTrend,
                'job_performance' => $jobPerformance,
                'revenue_trend' => $revenueTrend,
            ],
            'topPerformingJobs' => $topPerformingJobs,
            'activityFeed' => $activityFeed,
            'recentApplications' => $recentApplications,
        ]);
    }
}
