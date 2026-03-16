<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicationSignature extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'application_id',
        'signature_type',
        'signature_image',
        'signer_name',
        'ip_address',
        'signed_at',
    ];

    protected function casts(): array
    {
        return [
            'signed_at' => 'datetime',
        ];
    }

    public function application()
    {
        return $this->belongsTo(EnrollmentApplication::class);
    }
}
