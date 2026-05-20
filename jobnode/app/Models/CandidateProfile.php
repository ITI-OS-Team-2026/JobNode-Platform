<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateProfile extends Model
{
    protected $fillable = [
        'user_id', 'title', 'skills', 'linkedin_url', 'phone', 'resume_path'
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}