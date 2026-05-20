<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobComment extends Model
{
    protected $fillable = [
        'job_id', 'employer_id', 'content'
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }
}