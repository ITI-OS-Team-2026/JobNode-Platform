<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'employer_id' => User::factory()->state(['role' => User::ROLE_EMPLOYER]),
            'application_id' => null,
            'amount' => $this->faker->randomFloat(2, 5, 200),
            'currency' => 'USD',
            'provider' => 'manual',
            'provider_reference' => null,
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
            'metadata' => null,
        ];
    }
}
