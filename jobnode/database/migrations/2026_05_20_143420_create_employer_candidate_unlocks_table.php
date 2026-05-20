<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employer_candidate_unlocks', function (Blueprint $table) {
            $table->id();
            // Both foreign keys point to the users table
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('users')->cascadeOnDelete();
            
            $table->string('payment_reference')->comment('Stripe/PayPal transaction ID');
            $table->timestamp('unlocked_at')->useCurrent();
            $table->timestamps();

            // Prevent double-charging an employer for the same candidate
            $table->unique(['employer_id', 'candidate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employer_candidate_unlocks');
    }
};