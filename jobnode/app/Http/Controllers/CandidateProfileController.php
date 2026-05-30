<?php

namespace App\Http\Controllers;

use App\Models\CandidateProfile;
use App\Models\EmployerCandidateUnlock;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

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
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'skills' => 'nullable|string',
            'linkedin_url' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'resume' => 'nullable|file|mimes:pdf|max:5120', // 5MB Max PDF
        ]);

        $user = $request->user();
        $profile = $user->candidateProfile ?? new CandidateProfile(['user_id' => $user->id]);

        // Convert skills string back to JSON array
        $skillsArray = $validated['skills'] 
            ? array_map('trim', explode(',', $validated['skills'])) 
            : null;

        $profile->fill([
            'title' => $validated['title'],
            'skills' => $skillsArray,
            'linkedin_url' => $validated['linkedin_url'],
            'phone' => $validated['phone'],
        ]);

        if ($request->hasFile('resume')) {
            if ($profile->resume_path) {
                Storage::disk('local')->delete($profile->resume_path);
            }
            // Save to storage/app/resumes (NOT public)
            $profile->resume_path = $request->file('resume')->store('resumes', 'local');
            // Store original filename and time
            $profile->resume_original_filename = $request->file('resume')->getClientOriginalName();
            $profile->resume_uploaded_at = now();
        }

        $profile->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Download the candidate's resume securely.
     */
    public function downloadResume(Request $request, CandidateProfile $candidateProfile)
    {
        $user = $request->user();
        $canAccess = false;

        // 1. Owner can always access
        if ($user->id === $candidateProfile->user_id) {
            $canAccess = true;
        } 
        // 2. Admins can always access
        elseif ($user->role === 'admin') {
            $canAccess = true;
        } 
        // 3. Employers must have a valid unlock receipt
        elseif ($user->role === 'employer') {
            $hasUnlocked = EmployerCandidateUnlock::where('employer_id', $user->id)
                ->where('candidate_id', $candidateProfile->user_id)
                ->exists();
            
            if ($hasUnlocked) {
                $canAccess = true;
            }
        }

        if (!$canAccess || !$candidateProfile->resume_path) {
            abort(403, 'Unauthorized access or file not found.');
        }

        return Storage::disk('local')->download(
            $candidateProfile->resume_path, 
            $candidateProfile->resume_original_filename ?? "Resume_{$candidateProfile->user->name}.pdf"
        );
    }
}
