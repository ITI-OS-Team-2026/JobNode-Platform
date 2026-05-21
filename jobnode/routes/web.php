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
        
        // Job Management Routes
        Route::get('/jobs', [\App\Http\Controllers\JobController::class, 'index'])->name('jobs.index');
        Route::get('/jobs/create', [\App\Http\Controllers\JobController::class, 'create'])->name('jobs.create');
        Route::post('/jobs', [\App\Http\Controllers\JobController::class, 'store'])->name('jobs.store');
        Route::get('/jobs/{job}/edit', [\App\Http\Controllers\JobController::class, 'edit'])->name('jobs.edit');
        Route::put('/jobs/{job}', [\App\Http\Controllers\JobController::class, 'update'])->name('jobs.update');
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