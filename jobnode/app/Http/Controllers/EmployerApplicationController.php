<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\EmployerCandidateUnlock;
use App\Models\Job;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployerApplicationController extends Controller
{
    /**
     * Display a listing of applications for employer's jobs.
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Get all applications for jobs owned by this employer
        $applications = Application::with([
            'job',
            'candidate',
            'candidate.candidateProfile'
        ])
        ->whereHas('job', function ($query) use ($userId) {
            $query->where('employer_id', $userId);
        })
        ->latest()
        ->paginate(15)
        ->withQueryString();

        // Transform applications to remove sensitive data
        $applications->getCollection()->transform(function ($application) {
            return [
                'id' => $application->id,
                'candidate' => [
                    'id' => $application->candidate->id,
                    'name' => $application->candidate->name,
                    // SECURITY: Email NOT included
                ],
                'job' => [
                    'id' => $application->job->id,
                    'title' => $application->job->title,
                ],
                'status' => $application->status,
                'created_at' => $application->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return Inertia::render('Employer/Applications/Index', [
            'applications' => $applications,
        ]);
    }

    /**
     * Display a specific application.
     */
    public function show(Request $request, Application $application)
    {
        // Authorization: ensure the job belongs to the authenticated employer
        if ($application->job->employer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        // Eager load related data
        $application->load([
            'job',
            'candidate',
            'candidate.candidateProfile'
        ]);

        // Check if employer has unlocked this specific application (per-application unlock)
        $isUnlocked = EmployerCandidateUnlock::where('employer_id', $request->user()->id)
            ->where('application_id', $application->id)
            ->exists();

        // Format the data for frontend - SAFE FOR LOCKED STATE
        $candidateData = [
            'id' => $application->candidate->id,
            'name' => $application->candidate->name,
            // SECURITY: Email is NOT sent unless unlocked
        ];

        $profileData = $application->candidate->candidateProfile ? [
            'title'                  => $application->candidate->candidateProfile->title,
            'linkedin_url'           => $application->candidate->candidateProfile->linkedin_url,
            'skills'                 => $application->candidate->candidateProfile->skills ?? [],
            'is_complete'            => $application->candidate->candidateProfile->isComplete(),
            'completeness_percentage'=> $application->candidate->candidateProfile->getCompletenessPercentage(),
            // Safe boolean — reveals nothing sensitive, just whether a resume exists
            'has_resume'             => !empty($application->candidate->candidateProfile->resume_path),
            // SECURITY: Phone is NOT sent unless unlocked
            // SECURITY: Resume path is NOT sent unless unlocked
            // SECURITY: Resume filename is NOT sent unless unlocked
        ] : null;

        // If unlocked, add protected fields
        if ($isUnlocked) {
            $candidateData['email'] = $application->candidate->email;
            
            if ($profileData) {
                $profileData['phone'] = $application->candidate->candidateProfile->phone;
                $profileData['resume_path'] = $application->candidate->candidateProfile->resume_path;
                $profileData['resume_original_filename'] = $application->candidate->candidateProfile->resume_original_filename;
                $profileData['resume_uploaded_at'] = $application->candidate->candidateProfile->resume_uploaded_at?->format('Y-m-d H:i:s');
            }
        }

        // Prepare unlock price/currency for frontend (ensure numeric)
        $unlockPrice = (float) config('payments.unlock_candidate_price');
        $unlockCurrency = config('payments.unlock_candidate_currency', 'USD');

        $unlockDetails = null;
        if ($isUnlocked) {
            $unlock = EmployerCandidateUnlock::with('payment')
                ->where('employer_id', $request->user()->id)
                ->where('application_id', $application->id)
                ->first();
            
            if ($unlock) {
                $unlockDetails = [
                    'unlocked_at' => $unlock->unlocked_at->format('j M Y'),
                    'provider' => $unlock->payment ? ucfirst($unlock->payment->provider) : 'Kashier',
                    'amount' => $unlock->payment ? $unlock->payment->amount : $unlockPrice,
                    'currency' => $unlock->payment ? $unlock->payment->currency : $unlockCurrency,
                ];
            }
        }

        return Inertia::render('Employer/Applications/Show', [
            'application' => [
                'id' => $application->id,
                'status' => $application->status,
                'created_at' => $application->created_at->format('Y-m-d H:i:s'),
            ],
            'candidate' => $candidateData,
            'profile' => $profileData,
            'job' => [
                'id' => $application->job->id,
                'title' => $application->job->title,
            ],
            'isUnlocked' => $isUnlocked,
            'unlockPrice' => $unlockPrice,
            'unlockCurrency' => $unlockCurrency,
            'unlockDetails' => $unlockDetails,
            'paymentProvider' => 'kashier',
            // Expose a secure resume download URL only when unlocked
            'resumeDownloadUrl' => $isUnlocked ? route('employer.applications.resume.download', ['application' => $application->id]) : null,
        ]);
    }
}
