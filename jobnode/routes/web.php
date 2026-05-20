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
    return Inertia::render('Welcome');
})->name('home');

Route::get('/jobs', function () {
    return Inertia::render('Jobs/Index');
})->name('jobs.index');

Route::get('/jobs/{job}', function () {
    return Inertia::render('Jobs/Show');
})->name('jobs.show');

/*
|--------------------------------------------------------------------------
| Candidate Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:candidate'])
    ->prefix('candidate')
    ->name('candidate.')
    ->group(function () {
        Route::inertia('/dashboard', 'Candidate/Dashboard')->name('dashboard');
        Route::inertia('/profile', 'Candidate/Profile')->name('profile');
        Route::inertia('/applications', 'Candidate/Applications')->name('applications');
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
        Route::inertia('/dashboard', 'Employer/Dashboard')->name('dashboard');
        Route::inertia('/company', 'Employer/CompanyProfile')->name('company.profile');
        Route::inertia('/jobs', 'Employer/Jobs/Index')->name('jobs.index');
        Route::inertia('/jobs/create', 'Employer/Jobs/Create')->name('jobs.create');
        // We will add dynamic parameters for editing/triage later when we build controllers
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
        Route::inertia('/dashboard', 'Admin/Dashboard')->name('dashboard');
        Route::inertia('/jobs/pending', 'Admin/Jobs/Pending')->name('jobs.pending');
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
});

require __DIR__.'/auth.php';