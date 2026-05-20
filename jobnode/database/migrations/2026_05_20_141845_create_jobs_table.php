<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
            
            // Job Details
            $table->string('title');
            $table->text('description');
            $table->text('responsibilities');
            $table->text('requirements');
            $table->string('category')->comment('e.g., programming, management');
            $table->json('technologies')->nullable()->comment('Specific tech stack requirements');
            
            // Logistics & Comp
            $table->string('location');
            $table->enum('work_type', ['remote', 'onsite', 'hybrid']);
            $table->decimal('min_salary', 10, 2)->nullable();
            $table->decimal('max_salary', 10, 2)->nullable();
            $table->text('benefits')->nullable();
            $table->date('application_deadline');
            
            // State & Analytics
            $table->enum('status', ['pending', 'approved', 'rejected', 'closed'])->default('pending');
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('applications_count')->default(0);
            
            $table->timestamps();

            // Indexes for faster filtering on the frontend
            $table->index('status');
            $table->index('category');
            $table->index('work_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};