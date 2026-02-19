<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'enrollment_id',
        'payment_type',
        'amount',
        'payment_date',
        'payment_method',
        'receipt_number',
        'installment_number',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
            'installment_number' => 'integer',
        ];
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
