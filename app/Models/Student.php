<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'matricule',
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
        'address',
        'photo_url',
        'status_type',
        'matricule_fonctionnaire',
        'emploi',
        'echelon',
        'categorie',
        'classe',
        'entry_mode',
        'diploma_cepe',
        'diploma_bepc',
        'diploma_bac',
        'diploma_bac_serie',
        'other_diplomas',
        'is_blocked',
        'block_reason',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'diploma_cepe' => 'boolean',
            'diploma_bepc' => 'boolean',
            'diploma_bac' => 'boolean',
            'children_count' => 'integer',
            'is_blocked' => 'boolean',
        ];
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function currentEnrollment()
    {
        return $this->hasOne(Enrollment::class)->whereHas('academicYear', function ($q) {
            $q->where('is_current', true);
        });
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('matricule', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('first_name', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%");
        });
    }
}
