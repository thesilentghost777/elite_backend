<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfileChoice extends Model
{
    use HasFactory;

    protected $table = 'user_profile_choices';

    protected $fillable = [
        'user_id',
        'profile_id',
        'from_recommendations',
    ];

    protected $casts = [
        'from_recommendations' => 'boolean',
    ];

    /**
     * L'utilisateur qui a fait le choix
     */
    public function user()
    {
        return $this->belongsTo(EliteUser::class, 'user_id');
    }

    /**
     * Le profil de carrière choisi
     */
    public function profile()
    {
        return $this->belongsTo(CareerProfile::class, 'profile_id');
    }
}