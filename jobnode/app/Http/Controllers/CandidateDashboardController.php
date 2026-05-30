<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CandidateDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $candidate = $request->user();
        $candidateId = $candidate->id;

        $activeApplicationsQuery = Application::where('candidate_id', $candidateId)
            ->where('status', '!=', 'cancelled');

        $activeApplications = (clone $activeApplicationsQuery)->count();
        $applicationsThisWeek = (clone $activeApplicationsQuery)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        $pendingCount = Application::where('candidate_id', $candidateId)
            ->where('status', 'applied')
            ->count();
        $acceptedCount = Application::where('candidate_id', $candidateId)
            ->where('status', 'accepted')
            ->count();
        $rejectedCount = Application::where('candidate_id', $candidateId)
            ->where('status', 'rejected')
            ->count();

        $profile = $candidate->candidateProfile;
        $profileCompleteness = $profile ? $profile->getCompletenessPercentage() : 0;
        $profileComplete = $profile ? $profile->isComplete() : false;

        $sevenDaysAgo = now()->subDays(6)->startOfDay();

        $applicationsTrendRaw = Application::where('candidate_id', $candidateId)
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
                'count' => isset($applicationsTrendRaw[$dateStr]) ? $applicationsTrendRaw[$dateStr]->count : 0,
            ];
        }

        $statusBreakdownRaw = Application::where('candidate_id', $candidateId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $statusBreakdown = collect(['applied', 'reviewed', 'accepted', 'rejected', 'cancelled'])
            ->map(fn (string $status) => [
                'status' => $status,
                'count' => (int) ($statusBreakdownRaw[$status] ?? 0),
            ])
            ->values();

        $recentApplications = Application::with('job.employer.company')
            ->where('candidate_id', $candidateId)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (Application $application) => [
                'id' => $application->id,
                'job_title' => $application->job->title,
                'company_name' => $application->job->employer->company?->company_name ?? 'Confidential Company',
                'location' => $application->job->location,
                'status' => $application->status,
                'created_at' => $application->created_at,
            ]);

        $activityFeed = Application::with('job')
            ->where('candidate_id', $candidateId)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (Application $application) => [
                'type' => $application->status === 'cancelled' ? 'cancelled' : 'application',
                'title' => match ($application->status) {
                    'cancelled' => 'Cancelled application for ' . $application->job->title,
                    'accepted' => 'Accepted for ' . $application->job->title,
                    'rejected' => 'Rejected for ' . $application->job->title,
                    'reviewed' => 'Application under review for ' . $application->job->title,
                    default => 'Applied to ' . $application->job->title,
                },
                'date' => $application->updated_at,
            ]);

        return Inertia::render('Candidate/Dashboard', [
            'metrics' => [
                'active_applications' => $activeApplications,
                'applications_this_week' => $applicationsThisWeek,
                'pending_count' => $pendingCount,
                'accepted_count' => $acceptedCount,
                'rejected_count' => $rejectedCount,
                'remaining_slots' => max(0, 12 - $activeApplications),
                'profile_completeness' => $profileCompleteness,
                'profile_complete' => $profileComplete,
            ],
            'charts' => [
                'applications_trend' => $applicationsTrend,
                'status_breakdown' => $statusBreakdown,
            ],
            'recentApplications' => $recentApplications,
            'activityFeed' => $activityFeed,
        ]);
    }
}
