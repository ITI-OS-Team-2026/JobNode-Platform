<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\EmployerCandidateUnlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployerResumeController extends Controller
{
    public function download(Request $request, Application $application)
    {
        // Ensure the authenticated employer owns the job
        if ($application->job->employer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $profile = $application->candidate->candidateProfile;
        if (!$profile || !$profile->resume_path) {
            abort(404, 'Resume not found.');
        }

        // Check unlock exists
        $unlocked = EmployerCandidateUnlock::where('employer_id', $request->user()->id)
            ->where('application_id', $application->id)
            ->whereNotNull('unlocked_at')
            ->exists();

        if (!$unlocked) {
            abort(403, 'Candidate not unlocked.');
        }

        $disk = Storage::disk('local');
        if (!$disk->exists($profile->resume_path)) {
            abort(404, 'Resume file missing.');
        }

        return $disk->download($profile->resume_path, $profile->resume_original_filename ?? 'resume');
    }
}
