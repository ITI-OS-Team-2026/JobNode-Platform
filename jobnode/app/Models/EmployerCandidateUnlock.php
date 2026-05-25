<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployerCandidateUnlock extends Model
{
    protected $fillable = [
        'employer_id', 'candidate_id', 'payment_reference', 'unlocked_at',
        'application_id', 'payment_id'
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

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
}