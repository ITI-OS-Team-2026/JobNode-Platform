<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            // Candidate is just a user record under the hood
            $table->foreignId('candidate_id')->constrained('users')->cascadeOnDelete();
            
            $table->enum('status', ['applied', 'reviewed', 'accepted', 'rejected', 'cancelled'])->default('applied');
            $table->timestamps();

            // A candidate can only apply once per job
            $table->unique(['job_id', 'candidate_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};