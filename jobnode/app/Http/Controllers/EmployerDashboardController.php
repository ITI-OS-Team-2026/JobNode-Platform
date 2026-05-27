<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployerDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $employer = $request->user();

        // 1. active_jobs
        $activeJobs = $employer->postedJobs()->where('status', 'approved')->count();

        // 2. total_views
        $totalViews = (int) $employer->postedJobs()->sum('views_count');

        // 3. total_applicants
        $totalApplicants = Application::whereHas('job', function ($query) use ($employer) {
            $query->where('employer_id', $employer->id);
        })->count();

        // 4. candidate_unlocks
        $candidateUnlocks = $employer->unlocks()->count();

        // Recent applications
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
            ],
            'recentApplications' => $recentApplications,
        ]);
    }
}
