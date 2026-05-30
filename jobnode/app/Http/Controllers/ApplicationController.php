<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the candidate's applications.
     */
    public function index(Request $request)
    {
        $applications = Application::with('job.employer.company')
            ->where('candidate_id', $request->user()->id)
            ->latest()
            ->get();

        return \Inertia\Inertia::render('Candidate/Applications', [
            'applications' => $applications
        ]);
    }

    /**
     * Store a newly created application in storage.
     */
    public function store(Request $request, Job $job): RedirectResponse
    {
        $candidateId = $request->user()->id;

        // 1. Prevent duplicate applications
        $alreadyApplied = Application::where('job_id', $job->id)
            ->where('candidate_id', $candidateId)
            ->exists();

        if ($alreadyApplied) {
            return back()->with('error', 'You have already applied to this position.');
        }

        // 2. Enforce the strict 12-application limit policy
        $applicationCount = Application::where('candidate_id', $candidateId)->count();

        if ($applicationCount >= 12) {
            return back()->with('error', 'Application limit reached. You can have a maximum of 12 active submissions on the platform.');
        }

        // 3. Process the application and update metrics atomically
        DB::transaction(function () use ($job, $candidateId) {
            Application::create([
                'job_id' => $job->id,
                'candidate_id' => $candidateId,
                'status' => 'applied',
            ]);

            // Increment our pre-aggregated analytic field
            $job->increment('applications_count');
        });

        return back()->with('success', 'Application submitted successfully!');
    }
}
