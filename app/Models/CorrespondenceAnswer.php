<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrespondenceAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'texte',
        'ordre',
    ];

    public function question()
    {
        return $this->belongsTo(CorrespondenceQuestion::class, 'question_id');
    }

    public function profileMatchings()
    {
        return $this->hasMany(ProfileMatching::class, 'answer_id');
    }

    public function profiles()
    {
        return $this->belongsToMany(CareerProfile::class, 'profile_matchings', 'answer_id', 'profile_id')
            ->withPivot('poids')
            ->withTimestamps();
    }
}
