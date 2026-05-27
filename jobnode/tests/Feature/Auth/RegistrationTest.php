<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new candidates can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role' => User::ROLE_CANDIDATE,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'role' => User::ROLE_CANDIDATE,
    ]);
    $response->assertRedirect(route('candidate.dashboard', absolute: false));
});

test('new employers can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test Employer',
        'email' => 'employer@example.com',
        'role' => User::ROLE_EMPLOYER,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'employer@example.com',
        'role' => User::ROLE_EMPLOYER,
    ]);
    $response->assertRedirect(route('employer.dashboard', absolute: false));
});

test('registration rejects unsupported roles', function () {
    $response = $this->from('/register')->post('/register', [
        'name' => 'Test Admin',
        'email' => 'admin@example.com',
        'role' => User::ROLE_ADMIN,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect('/register');
    $response->assertSessionHasErrors('role');
    $this->assertDatabaseMissing('users', [
        'email' => 'admin@example.com',
    ]);
});
