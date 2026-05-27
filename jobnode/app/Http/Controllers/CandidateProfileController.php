<?php

namespace App\Http\Controllers;

use App\Models\CandidateProfile;
use App\Http\Requests\UpdateCandidateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class CandidateProfileController extends Controller
{
    /**
     * Show the candidate profile page with edit form.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        
        // Get or create the candidate profile
        $profile = $user->candidateProfile()->firstOrCreate(
            ['user_id' => $user->id],
            []
        );

        return Inertia::render('Candidate/Profile', [
            'profile' => [
                'id' => $profile->id,
                'title' => $profile->title,
                'phone' => $profile->phone,
                'linkedin_url' => $profile->linkedin_url,
                'skills' => $profile->skills ?? [],
                'resume_path' => $profile->resume_path,
                'resume_original_filename' => $profile->resume_original_filename,
                'resume_uploaded_at' => $profile->resume_uploaded_at?->format('Y-m-d H:i:s'),
                'is_complete' => $profile->isComplete(),
                'completeness_percentage' => $profile->getCompletenessPercentage(),
            ],
        ]);
    }

    /**
     * Update the candidate profile.
     */
    public function update(UpdateCandidateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Get or create the candidate profile
        $profile = $user->candidateProfile()->firstOrCreate(
            ['user_id' => $user->id],
            []
        );

        // Handle resume upload
        if ($request->hasFile('resume')) {
            // Delete old resume if it exists
            if ($profile->resume_path && Storage::disk('local')->exists($profile->resume_path)) {
                Storage::disk('local')->delete($profile->resume_path);
            }

            // Store new resume
            $resumeFile = $request->file('resume');
            $resumePath = $resumeFile->store(
                'resumes/' . $user->id,
                'local'
            );

            $validated['resume_path'] = $resumePath;
            $validated['resume_original_filename'] = $resumeFile->getClientOriginalName();
            $validated['resume_mime_type'] = $resumeFile->getMimeType();
            $validated['resume_uploaded_at'] = now();
        }

        // Remove the resume file from validated data if it wasn't uploaded
        unset($validated['resume']);

        // Update the profile
        $profile->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Download the candidate's resume.
     */
    public function downloadResume(Request $request): ?object
    {
        $user = $request->user();
        $profile = $user->candidateProfile;

        if (!$profile || !$profile->resume_path) {
            return back()->with('error', 'Resume not found.');
        }

        if (!Storage::disk('local')->exists($profile->resume_path)) {
            return back()->with('error', 'Resume file not found.');
        }

        return Storage::disk('local')->download(
            $profile->resume_path,
            $profile->resume_original_filename
        );
    }
}
