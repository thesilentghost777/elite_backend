<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['partner_id', 'pack_id', 'lesson_id', 'starts_at', 'ends_at', 'active'];

    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'active' => 'boolean'];

    public function pack() { return $this->belongsTo(Pack::class); }
    public function lesson() { return $this->belongsTo(Lesson::class); }

    public function isOpen(): bool
    {
        return $this->active && now()->greaterThanOrEqualTo($this->starts_at)
            && ($this->ends_at === null || now()->lessThanOrEqualTo($this->ends_at));
    }
}