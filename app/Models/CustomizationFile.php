<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomizationFile extends Model
{
    protected $fillable = [
        'request_id', 'uploaded_by_type', 'uploaded_by_id',
        'file_category', 'original_name', 'extension', 'size_bytes',
        'local_path', 'bunny_path', 'bunny_synced',
    ];

    protected function casts(): array
    {
        return ['bunny_synced' => 'boolean'];
    }

    public function request()
    {
        return $this->belongsTo(CustomizationRequest::class, 'request_id');
    }

    public function getIsImageAttribute(): bool
    {
        return in_array(strtolower($this->extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    public function getIsPdfAttribute(): bool
    {
        return strtolower($this->extension) === 'pdf';
    }
}
