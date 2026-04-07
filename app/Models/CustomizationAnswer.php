<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomizationAnswer extends Model
{
    protected $fillable = ['request_id', 'question_key', 'question_text', 'answer'];

    public function request()
    {
        return $this->belongsTo(CustomizationRequest::class, 'request_id');
    }
}
