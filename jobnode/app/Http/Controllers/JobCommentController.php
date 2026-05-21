<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class JobCommentController extends Controller
{
    /**
     * Store a newly created comment/update for a specific job.
     */
    public function store(Request $request, Job $job): RedirectResponse
    {
        // Security: Ensure the user actually owns this job listing
        if ($job->employer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $job->comments()->create([
            'employer_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Status update posted successfully.');
    }
}