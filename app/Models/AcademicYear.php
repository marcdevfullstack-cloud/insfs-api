<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcademicYear extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['label', 'start_date', 'end_date', 'is_current'];

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
