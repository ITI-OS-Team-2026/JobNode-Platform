<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public Routes (Guest Access)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return \Inertia\Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('home');

Route::get('/jobs', [\App\Http\Controllers\PublicJobController::class, 'index'])->name('jobs.index');

Route::get('/jobs/{job}', [\App\Http\Controllers\PublicJobController::class, 'show'])->name('jobs.show');

/*
|--------------------------------------------------------------------------
| Candidate Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:candidate'])
    ->prefix('candidate')
    ->name('candidate.')
    ->group(function () {
        Route::get('/dashboard', \App\Http\Controllers\CandidateDashboardController::class)->name('dashboard');
        
        // Profile Management Routes
        Route::get('/profile', [\App\Http\Controllers\CandidateProfileController::class, 'show'])->name('profile');
        Route::post('/profile/update', [\App\Http\Controllers\CandidateProfileController::class, 'update'])->name('profile.update');
        
        Route::get('/applications', [\App\Http\Controllers\ApplicationController::class, 'index'])->name('applications');
        Route::post('/applications/{application}/cancel', [\App\Http\Controllers\ApplicationController::class, 'destroy'])->name('applications.cancel');
        
        // Job Application Route
        Route::post('/jobs/{job}/apply', [\App\Http\Controllers\ApplicationController::class, 'store'])->name('jobs.apply');
    });

/*
|--------------------------------------------------------------------------
| Employer Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:employer'])
    ->prefix('employer')
    ->name('employer.')
    ->group(function () {
        Route::get('/dashboard', \App\Http\Controllers\EmployerDashboardController::class)->name('dashboard');
        Route::get('/company', function (\Illuminate\Http\Request $request) {
            return \Inertia\Inertia::render('Employer/CompanyProfile', [
                'company' => $request->user()->company
            ]);
        })->name('company.profile');
        Route::post('/company/update', [\App\Http\Controllers\CompanyProfileController::class, 'update'])->name('company.profile.update');
        
        // Job Management Routes
        Route::get('/jobs', [\App\Http\Controllers\JobController::class, 'index'])->name('jobs.index');
        Route::get('/jobs/create', [\App\Http\Controllers\JobController::class, 'create'])->name('jobs.create');
        Route::post('/jobs', [\App\Http\Controllers\JobController::class, 'store'])->name('jobs.store');
        Route::get('/jobs/{job}/edit', [\App\Http\Controllers\JobController::class, 'edit'])->name('jobs.edit');
        Route::put('/jobs/{job}', [\App\Http\Controllers\JobController::class, 'update'])->name('jobs.update');
        Route::post('/jobs/{job}/comments', [\App\Http\Controllers\JobCommentController::class, 'store'])->name('jobs.comments.store');
        
        // Application Management Routes
        Route::get('/applications', [\App\Http\Controllers\EmployerApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}', [\App\Http\Controllers\EmployerApplicationController::class, 'show'])->name('applications.show');
        // Payments for unlocking applications
        Route::post('/applications/{application}/payments', [\App\Http\Controllers\PaymentController::class, 'store'])->name('applications.payments.store');
        // Resume download for unlocked candidates
        Route::get('/applications/{application}/resume/download', [\App\Http\Controllers\EmployerResumeController::class, 'download'])->name('applications.resume.download');
    });
    
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', \App\Http\Controllers\AdminDashboardController::class)->name('dashboard');
        Route::get('/jobs/pending', [\App\Http\Controllers\AdminJobController::class, 'index'])->name('jobs.pending');
        Route::patch('/jobs/{job}/status', [\App\Http\Controllers\AdminJobController::class, 'update'])->name('jobs.status.update');
    });

/*
|--------------------------------------------------------------------------
| Default Breeze Account Routes (Password/Email Management)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/account', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/account', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/account', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/resumes/{candidateProfile}/download', [\App\Http\Controllers\CandidateProfileController::class, 'downloadResume'])->name('resumes.download');
});

require __DIR__.'/auth.php';

// Kashier endpoints (webhook + redirects)
Route::post('/payments/kashier/webhook', [\App\Http\Controllers\KashierController::class, 'webhook'])->name('payments.kashier.webhook');
Route::get('/payments/kashier/success', [\App\Http\Controllers\KashierController::class, 'success'])->name('payments.kashier.success');
Route::get('/payments/kashier/failure', [\App\Http\Controllers\KashierController::class, 'failure'])->name('payments.kashier.failure');