<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'montant_fcfa',
        'points',
        'reference',
        'description',
        'metadata',
        'statut',
    ];

    protected $casts = [
        'montant_fcfa' => 'decimal:2',
        'points' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(EliteUser::class, 'user_id');
    }

    public static function generateReference(): string
    {
        return 'TXN-' . strtoupper(Str::random(12));
    }

    public function scopeCompleted($query)
    {
        return $query->where('statut', 'complete');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
