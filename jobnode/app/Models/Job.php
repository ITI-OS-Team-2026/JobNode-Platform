<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'job_listings';

    protected $fillable = [
        'employer_id', 'title', 'description', 'responsibilities', 
        'requirements', 'category', 'technologies', 'location', 
        'work_type', 'min_salary', 'max_salary', 'benefits', 
        'application_deadline', 'status', 'views_count', 'applications_count'
    ];

    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'application_deadline' => 'date',
        ];
    }

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function comments()
    {
        return $this->hasMany(JobComment::class);
    }
}