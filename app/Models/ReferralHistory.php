<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralHistory extends Model
{
    use HasFactory;

    protected $table = 'referral_history';

    protected $fillable = [
        'parrain_id',
        'filleul_id',
        'points_gagnes',
    ];

    public function parrain()
    {
        return $this->belongsTo(EliteUser::class, 'parrain_id');
    }

    public function filleul()
    {
        return $this->belongsTo(EliteUser::class, 'filleul_id');
    }
}
