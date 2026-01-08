<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pack extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'nom',
        'slug',
        'description',
        'image_url',
        'niveau_requis',
        'durees_disponibles',
        'diplomes_possibles',
        'prix_points',
        'debouches',
        'ordre',
        'active',
    ];

    protected $casts = [
        'durees_disponibles' => 'array',
        'diplomes_possibles' => 'array',
        'debouches' => 'array',
        'active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'pack_id')->orderBy('ordre');
    }

    public function profiles()
    {
        return $this->belongsToMany(CareerProfile::class, 'pack_profiles', 'pack_id', 'profile_id')
            ->withPivot('priorite')
            ->withTimestamps();
    }

    public function userPacks()
    {
        return $this->hasMany(UserPack::class, 'pack_id');
    }

    public function roadmapSteps()
    {
        return $this->hasMany(RoadmapStep::class, 'pack_recommande_id');
    }

    public function getTotalLessonsAttribute(): int
    {
        return $this->modules()
            ->with('chapters.lessons')
            ->get()
            ->flatMap(fn($m) => $m->chapters)
            ->flatMap(fn($c) => $c->lessons)
            ->count();
    }

    public function getTotalDurationAttribute(): int
    {
        return $this->modules()
            ->with('chapters.lessons')
            ->get()
            ->flatMap(fn($m) => $m->chapters)
            ->flatMap(fn($c) => $c->lessons)
            ->sum('duree_minutes');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForProfile($query, int $profileId)
    {
        return $query->whereHas('profiles', function ($q) use ($profileId) {
            $q->where('career_profiles.id', $profileId);
        })->orderByRaw('(SELECT priorite FROM pack_profiles WHERE pack_profiles.pack_id = packs.id AND pack_profiles.profile_id = ?) DESC', [$profileId]);
    }
}
