<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class PortalUser extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasUuids;

    protected $table = 'portal_users';

    protected $fillable = [
        'admitted_student_id',
        'last_name',
        'first_name',
        'date_of_birth',
        'email',
        'password',
        'phone',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth'     => 'date',
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function admittedStudent()
    {
        return $this->belongsTo(AdmittedStudent::class);
    }

    public function application()
    {
        return $this->hasOne(EnrollmentApplication::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }
}
