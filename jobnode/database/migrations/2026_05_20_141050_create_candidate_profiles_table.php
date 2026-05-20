<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('title')->nullable()->comment('e.g. Senior Vue Developer');
            $table->json('skills')->nullable()->comment('Array of skills');
            $table->string('linkedin_url')->nullable();
            $table->string('phone')->nullable()->comment('Hard-gated behind payment wall');
            $table->string('resume_path')->nullable()->comment('Hard-gated behind payment wall');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_profiles');
    }
};