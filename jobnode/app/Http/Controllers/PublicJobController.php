<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicJobController extends Controller
{
    /**
     * Display the public job board with filtering.
     */
    public function index(Request $request)
    {
        $query = Job::with(['employer.company'])
                    ->where('status', 'approved');

        // Apply Search Filter
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('technologies', 'like', $searchTerm);
            });
        }

        // Apply Category Filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Apply Work Type Filter
        if ($request->filled('work_type')) {
            $query->where('work_type', $request->work_type);
        }

        // Apply Location Filter
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Paginate results and maintain URL query strings
        $jobs = $query->latest()->paginate(12)->withQueryString();

        return Inertia::render('Jobs/Index', [
            'jobs' => $jobs,
            'filters' => $request->only(['search', 'category', 'work_type', 'location']),
        ]);
    }

    /**
     * Display the specified public job listing.
     */
    public function show(Job $job)
    {
        // Only allow viewing if the job is approved or if the owner is previewing it
        if ($job->status !== 'approved' && (!auth()->check() || auth()->id() !== $job->employer_id)) {
            // Note: If you want to allow admins to preview it, you can expand this logic later
            if (!auth()->user() || auth()->user()->role !== 'admin') {
                abort(404);
            }
        }

        // Increment the engagement metric cleanly
        $job->increment('views_count');

        // Eager load relationships
        $job->load(['employer.company', 'comments' => function ($query) {
            $query->latest();
        }]);

        return Inertia::render('Jobs/Show', [
            'job' => $job,
            // Pass whether the current user has already applied to this job
            'hasApplied' => auth()->user()
                ? \App\Models\Application::where('job_id', $job->id)
                    ->where('candidate_id', auth()->id())
                    ->where('status', '!=', 'cancelled')
                    ->exists()
                : false,
        ]);
    }
}

