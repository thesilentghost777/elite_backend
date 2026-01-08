<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'pack_id',
        'nom',
        'description',
        'type',
        'ordre',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function pack()
    {
        return $this->belongsTo(Pack::class, 'pack_id');
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'module_id')->orderBy('ordre');
    }

    public function getTotalLessonsAttribute(): int
    {
        return $this->chapters->flatMap(fn($c) => $c->lessons)->count();
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
