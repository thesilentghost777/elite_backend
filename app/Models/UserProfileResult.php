<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfileResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_id',
        'score',
        'rang',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(EliteUser::class, 'user_id');
    }

    public function profile()
    {
        return $this->belongsTo(CareerProfile::class, 'profile_id');
    }
}
