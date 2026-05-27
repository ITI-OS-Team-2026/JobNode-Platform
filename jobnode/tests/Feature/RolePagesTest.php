<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('role protected inertia pages render existing components', function (string $role, string $path, string $component) {
    $this->assertTrue(file_exists(resource_path("js/Pages/{$component}.vue")), "{$component}.vue is missing.");

    $user = User::factory()->create([
        'role' => $role,
    ]);

    $this->actingAs($user)
        ->get($path)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component($component));
})->with([
    [User::ROLE_CANDIDATE, '/candidate/dashboard', 'Candidate/Dashboard'],
    [User::ROLE_CANDIDATE, '/candidate/profile', 'Candidate/Profile'],
    [User::ROLE_CANDIDATE, '/candidate/applications', 'Candidate/Applications'],
    [User::ROLE_EMPLOYER, '/employer/dashboard', 'Employer/Dashboard'],
    [User::ROLE_EMPLOYER, '/employer/company', 'Employer/CompanyProfile'],
    [User::ROLE_EMPLOYER, '/employer/jobs', 'Employer/Jobs/Index'],
    [User::ROLE_EMPLOYER, '/employer/jobs/create', 'Employer/Jobs/Create'],
    [User::ROLE_ADMIN, '/admin/dashboard', 'Admin/Dashboard'],
    [User::ROLE_ADMIN, '/admin/jobs/pending', 'Admin/Jobs/Pending'],
]);
