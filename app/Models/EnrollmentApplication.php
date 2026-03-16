<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EnrollmentApplication extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'portal_user_id',
        'admitted_student_id',
        'academic_year_id',
        'student_id',
        'validated_by',
        'status',
        'rejection_reason',
        'correction_fields',
        'submitted_at',
        'validated_at',
        'completed_at',
        // Section I
        'last_name',
        'first_name',
        'gender',
        'date_of_birth',
        'place_of_birth',
        'nationality',
        'father_name',
        'mother_name',
        'marital_status',
        'children_count',
        'phone',
        'email',
        'photo_url',
        // Statut
        'status_type',
        'matricule_fonctionnaire',
        'emploi',
        'echelon',
        'categorie',
        'classe',
        // Section II
        'diploma_cepe',
        'diploma_bepc',
        'diploma_bac',
        'diploma_bac_serie',
        'other_diplomas',
        // Section III
        'entry_date',
        // Section IV
        'address_quarter',
        'address_apartment',
        'address_phone',
        'postal_box',
        'vacation_address',
        'tutor_name',
        'tutor_address',
        'tutor_phone',
        // Section V
        'has_health_issues',
        'health_condition',
        'doctor_info',
        // Engagement
        'engagement_signed',
        'engagement_signed_at',
        // PDFs
        'fiche_identification_path',
        'fiche_inscription_path',
        'certificat_inscription_path',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth'         => 'date',
            'entry_date'            => 'date',
            'diploma_cepe'          => 'boolean',
            'diploma_bepc'          => 'boolean',
            'diploma_bac'           => 'boolean',
            'has_health_issues'     => 'boolean',
            'engagement_signed'     => 'boolean',
            'children_count'        => 'integer',
            'correction_fields'     => 'array',
            'submitted_at'          => 'datetime',
            'validated_at'          => 'datetime',
            'completed_at'          => 'datetime',
            'engagement_signed_at'  => 'datetime',
        ];
    }

    public function portalUser()
    {
        return $this->belongsTo(PortalUser::class);
    }

    public function admittedStudent()
    {
        return $this->belongsTo(AdmittedStudent::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function documents()
    {
        return $this->hasMany(ApplicationDocument::class, 'application_id');
    }

    public function signatures()
    {
        return $this->hasMany(ApplicationSignature::class, 'application_id');
    }

    public function signature(string $type): ?ApplicationSignature
    {
        return $this->signatures->firstWhere('signature_type', $type);
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['BROUILLON', 'CORRECTION_DEMANDEE']);
    }

    public function isReadyToSubmit(): bool
    {
        return $this->last_name
            && $this->first_name
            && $this->gender
            && $this->date_of_birth
            && $this->place_of_birth
            && $this->status_type
            && $this->engagement_signed
            && $this->photo_url;
    }
}
