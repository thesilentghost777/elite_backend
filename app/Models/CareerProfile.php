<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'image_url',
        'secteur',
        'debouches',
        'niveau_minimum',
        'is_cfpam',
        'active',
    ];

    protected $casts = [
        'debouches' => 'array',
        'is_cfpam' => 'boolean',
        'active' => 'boolean',
    ];

    public function matchings()
    {
        return $this->hasMany(ProfileMatching::class, 'profile_id');
    }

    public function answers()
    {
        return $this->belongsToMany(CorrespondenceAnswer::class, 'profile_matchings', 'profile_id', 'answer_id')
            ->withPivot('poids')
            ->withTimestamps();
    }

    public function roadmaps()
    {
        return $this->hasMany(Roadmap::class, 'profile_id');
    }

    public function packs()
    {
        return $this->belongsToMany(Pack::class, 'pack_profiles', 'profile_id', 'pack_id')
            ->withPivot('priorite')
            ->withTimestamps()
            ->orderByPivot('priorite', 'desc');
    }

    public function userResults()
    {
        return $this->hasMany(UserProfileResult::class, 'profile_id');
    }

    public function userChoices()
    {
        return $this->hasMany(UserProfileChoice::class, 'profile_id');
    }

    public function getRoadmapForLevel(string $niveau)
    {
        return $this->roadmaps()->where('niveau_depart', $niveau)->first();
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeCfpam($query)
    {
        return $query->where('is_cfpam', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_cfpam', false);
    }
}
