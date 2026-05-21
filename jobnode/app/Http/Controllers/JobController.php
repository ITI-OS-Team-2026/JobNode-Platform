<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;

class JobController extends Controller
{
    /**
     * Show the form for creating a new job.
     */
    public function create()
    {
        return Inertia::render('Employer/Jobs/Create');
    }

    /**
     * Store a newly created job in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'responsibilities' => 'required|string',
            'requirements' => 'required|string',
            'category' => 'required|string|max:255',
            'technologies' => 'nullable|string',
            'location' => 'required|string|max:255',
            'work_type' => 'required|in:remote,onsite,hybrid',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'benefits' => 'nullable|string',
            'application_deadline' => 'required|date|after:today',
        ]);

        $technologiesArray = $validated['technologies'] 
            ? array_map('trim', explode(',', $validated['technologies'])) 
            : null;

        Job::create([
            'employer_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'responsibilities' => $validated['responsibilities'],
            'requirements' => $validated['requirements'],
            'category' => $validated['category'],
            'technologies' => $technologiesArray,
            'location' => $validated['location'],
            'work_type' => $validated['work_type'],
            'min_salary' => $validated['min_salary'],
            'max_salary' => $validated['max_salary'],
            'benefits' => $validated['benefits'],
            'application_deadline' => $validated['application_deadline'],
            'status' => 'pending',
        ]);

        return redirect()->route('employer.jobs.index')->with('success', 'Job posted successfully and is pending admin approval.');
    }
}