<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileMatching extends Model
{
    use HasFactory;

    protected $fillable = [
        'answer_id',
        'profile_id',
        'poids',
    ];

    public function answer()
    {
        return $this->belongsTo(CorrespondenceAnswer::class, 'answer_id');
    }

    public function profile()
    {
        return $this->belongsTo(CareerProfile::class, 'profile_id');
    }
}
