<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_comments', function (Blueprint $table) {
            $table->id();
            // Pointing to our custom job_listings table
            $table->foreignId('job_id')->constrained('job_listings')->cascadeOnDelete();
            // The employer writing the comment
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
            
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_comments');
    }
};