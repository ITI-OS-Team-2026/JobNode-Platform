<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('application_id')->nullable()->constrained('applications')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('EGP');
            $table->string('provider')->nullable();
            $table->string('provider_reference')->nullable();
            $table->string('session_id')->nullable()->index();
            $table->string('status')->default('pending')->index();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->json('provider_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
