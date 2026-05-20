<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployerCandidateUnlock extends Model
{
    protected $fillable = [
        'employer_id', 'candidate_id', 'payment_reference', 'unlocked_at'
    ];

    protected function casts(): array
    {
        return [
            'unlocked_at' => 'datetime',
        ];
    }

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function candidate()
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }
}