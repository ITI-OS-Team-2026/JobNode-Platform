<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateProfile extends Model
{
    protected $fillable = [
        'user_id', 'title', 'skills', 'linkedin_url', 'phone', 'resume_path',
        'resume_original_filename', 'resume_mime_type', 'resume_uploaded_at'
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'resume_uploaded_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the candidate profile is considered complete.
     * A profile is complete when it has both phone and resume.
     */
    public function isComplete(): bool
    {
        return !empty($this->phone) && !empty($this->resume_path);
    }

    /**
     * Get profile completeness percentage.
     */
    public function getCompletenessPercentage(): int
    {
        $required = ['phone', 'resume_path'];
        $optional = ['title', 'linkedin_url', 'skills'];
        
        $requiredCount = collect($required)
            ->filter(fn($field) => !empty($this->{$field}))
            ->count();
        
        $optionalCount = collect($optional)
            ->filter(fn($field) => !empty($this->{$field}))
            ->count();
        
        $totalPossible = count($required) + count($optional);
        $completed = $requiredCount + $optionalCount;
        
        return $totalPossible > 0 ? (int)(($completed / $totalPossible) * 100) : 0;
    }
}