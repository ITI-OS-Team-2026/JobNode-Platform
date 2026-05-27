<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_EMPLOYER = 'employer';

    public const ROLE_CANDIDATE = 'candidate';

    public const REGISTRABLE_ROLES = [
        self::ROLE_CANDIDATE,
        self::ROLE_EMPLOYER,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function dashboardRouteName(): string
    {
        return match ($this->role) {
            self::ROLE_EMPLOYER => 'employer.dashboard',
            self::ROLE_CANDIDATE => 'candidate.dashboard',
            self::ROLE_ADMIN => 'admin.dashboard',
            default => 'home',
        };
    }

    public function dashboardPath(bool $absolute = false): string
    {
        return route($this->dashboardRouteName(), absolute: $absolute);
    }

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function candidateProfile()
    {
        return $this->hasOne(CandidateProfile::class);
    }

    public function postedJobs()
    {
        return $this->hasMany(Job::class, 'employer_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'candidate_id');
    }

    public function unlocks()
    {
        return $this->hasMany(EmployerCandidateUnlock::class, 'employer_id');
    }
}
