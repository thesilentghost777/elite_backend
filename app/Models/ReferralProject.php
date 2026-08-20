<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralProject extends Model
{
    protected $fillable = ['user_id', 'nom', 'pack_id'];

    public function user()
    {
        return $this->belongsTo(EliteUser::class, 'user_id');
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class);
    }

    public function goals()
    {
        return $this->hasMany(ReferralGoal::class, 'project_id');
    }
}