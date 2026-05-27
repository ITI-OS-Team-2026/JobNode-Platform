<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'employer_id',
        'application_id',
        'amount',
        'currency',
        'provider',
        'provider_reference',
        'session_id',
        'provider_response',
        'status',
        'paid_at',
        'metadata'
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'metadata' => 'array',
            'provider_response' => 'array',
        ];
    }

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function unlocks()
    {
        return $this->hasMany(EmployerCandidateUnlock::class, 'payment_id');
    }
}
