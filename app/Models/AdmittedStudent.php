<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdmittedStudent extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'imported_by',
        'last_name',
        'first_name',
        'date_of_birth',
        'email',
        'phone',
        'entry_mode',
        'year_of_study',
        'status',
        'import_batch',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'year_of_study' => 'integer',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function importer()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    public function portalUser()
    {
        return $this->hasOne(PortalUser::class);
    }

    public function application()
    {
        return $this->hasOne(EnrollmentApplication::class);
    }
}
