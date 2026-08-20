<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'pack_id',
        'profile_id',
        'priorite',
    ];

    public function pack()
    {
        return $this->belongsTo(Pack::class, 'pack_id');
    }

    public function profile()
    {
        return $this->belongsTo(CareerProfile::class, 'profile_id');
    }
}
