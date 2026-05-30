<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;

class AdminJobController extends Controller
{
    /**
     * Display a listing of pending jobs requiring moderation.
     */
    public function index()
    {
        $pendingJobs = Job::with('employer.company')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return Inertia::render('Admin/Jobs/Pending', [
            'jobs' => $pendingJobs
        ]);
    }

    /**
     * Update the status of the specified job (Approve/Reject).
     */
    public function update(Request $request, Job $job): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $job->update([
            'status' => $validated['status'],
        ]);

        $message = $validated['status'] === 'approved' 
            ? 'Job approved and is now live on the public board.' 
            : 'Job rejected and returned to the employer.';

        return back()->with('success', $message);
    }
}
