<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->string('resume_original_filename')->nullable()->after('resume_path')->comment('Original filename of the uploaded resume');
            $table->string('resume_mime_type')->nullable()->after('resume_original_filename')->comment('MIME type of the uploaded resume');
            $table->timestamp('resume_uploaded_at')->nullable()->after('resume_mime_type')->comment('When the resume was uploaded');
        });
    }

    public function down(): void
    {
        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->dropColumn(['resume_original_filename', 'resume_mime_type', 'resume_uploaded_at']);
        });
    }
};
