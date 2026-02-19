<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeeSchedule extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'academic_year_id',
        'school_id',
        'student_status',
        'fee_type',
        'total_amount',
        'max_installments',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'max_installments' => 'integer',
        ];
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
