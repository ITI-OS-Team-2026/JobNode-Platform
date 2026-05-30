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

        $activeApplicationCount = Application::where('candidate_id', $candidateId)
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($activeApplicationCount >= 12) {
            return back()->with('error', 'Application limit reached. You can have a maximum of 12 active submissions on the platform.');
        }

        $existingApplication = Application::where('job_id', $job->id)
            ->where('candidate_id', $candidateId)
            ->first();

        if ($existingApplication) {
            if ($existingApplication->status !== 'cancelled') {
                return back()->with('error', 'You have already applied to this position.');
            }

            DB::transaction(function () use ($job, $existingApplication) {
                $existingApplication->update(['status' => 'applied']);
                $job->increment('applications_count');
            });

            return back()->with('success', 'Application submitted successfully!');
        }

        DB::transaction(function () use ($job, $candidateId) {
            Application::create([
                'job_id' => $job->id,
                'candidate_id' => $candidateId,
                'status' => 'applied',
            ]);

            $job->increment('applications_count');
        });

        return back()->with('success', 'Application submitted successfully!');
    }

    /**
     * Cancel an active application submitted by the candidate.
     */
    public function destroy(Request $request, Application $application): RedirectResponse
    {
        if ($application->candidate_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($application->status !== 'applied') {
            return back()->with('error', 'Only pending applications can be cancelled.');
        }

        DB::transaction(function () use ($application) {
            $application->update(['status' => 'cancelled']);
            $application->job->decrement('applications_count');
        });

        return back()->with('success', 'Application cancelled successfully.');
    }
}
