<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicationDocument extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'application_id',
        'document_type',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function application()
    {
        return $this->belongsTo(EnrollmentApplication::class);
    }

    public function getFileUrlAttribute(): string
    {
        return url('storage/' . $this->file_path);
    }
}
