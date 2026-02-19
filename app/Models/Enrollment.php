<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enrollment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'student_id',
        'school_id',
        'academic_year_id',
        'year_of_study',
        'cycle',
        'quality',
        'enrollment_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
            'year_of_study' => 'integer',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function getTotalOwedAttribute(): float
    {
        $studentStatus = $this->student?->status_type;
        if (!$studentStatus) {
            return 0.0;
        }

        return (float) FeeSchedule::where('academic_year_id', $this->academic_year_id)
            ->where('school_id', $this->school_id)
            ->where('student_status', $studentStatus)
            ->sum('total_amount');
    }

    public function getBalanceAttribute(): float
    {
        return max(0, $this->total_owed - $this->total_paid);
    }
}
