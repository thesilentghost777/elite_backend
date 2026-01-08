<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrespondenceQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'question',
        'type',
        'ordre',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(CorrespondenceCategory::class, 'category_id');
    }

    public function answers()
    {
        return $this->hasMany(CorrespondenceAnswer::class, 'question_id')->orderBy('ordre');
    }

    public function userAnswers()
    {
        return $this->hasMany(UserCorrespondence::class, 'question_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
