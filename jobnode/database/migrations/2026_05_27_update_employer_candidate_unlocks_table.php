<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employer_candidate_unlocks', function (Blueprint $table) {
            if (!Schema::hasColumn('employer_candidate_unlocks', 'application_id')) {
                $table->foreignId('application_id')->nullable()->after('candidate_id')->constrained('applications')->nullOnDelete();
            }

            if (!Schema::hasColumn('employer_candidate_unlocks', 'payment_id')) {
                $table->foreignId('payment_id')->nullable()->after('application_id')->constrained('payments')->nullOnDelete();
            }

            // ensure unlocked_at exists (migration added it earlier), add index on employer/candidate/application
            $table->index(['employer_id', 'candidate_id']);
            $table->index('application_id');
        });
    }

    public function down(): void
    {
        Schema::table('employer_candidate_unlocks', function (Blueprint $table) {
            if (Schema::hasColumn('employer_candidate_unlocks', 'payment_id')) {
                $table->dropConstrainedForeignId('payment_id');
            }
            if (Schema::hasColumn('employer_candidate_unlocks', 'application_id')) {
                $table->dropConstrainedForeignId('application_id');
            }
        });
    }
};
