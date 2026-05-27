<?php

namespace Database\Factories;

use App\Models\EmployerCandidateUnlock;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmployerCandidateUnlock>
 */
class EmployerCandidateUnlockFactory extends Factory
{
    protected $model = EmployerCandidateUnlock::class;

    public function definition(): array
    {
        $employer = User::factory()->state(['role' => User::ROLE_EMPLOYER])->create();
        $candidate = User::factory()->state(['role' => User::ROLE_CANDIDATE])->create();

        return [
            'employer_id' => $employer->id,
            'candidate_id' => $candidate->id,
            'payment_reference' => null,
            'unlocked_at' => now(),
            'application_id' => null,
            'payment_id' => Payment::factory(),
        ];
    }
}
