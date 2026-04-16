<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralGoal extends Model
{
    protected $fillable = ['user_id', 'project_id', 'palier_cible', 'statut', 'completed_at'];

    protected $casts = ['completed_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(EliteUser::class, 'user_id');
    }

    public function project()
    {
        return $this->belongsTo(ReferralProject::class, 'project_id');
    }
}