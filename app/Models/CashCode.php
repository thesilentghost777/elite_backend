<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'montant_fcfa',
        'points',
        'created_by',
        'assigned_to',
        'used_by',
        'used_at',
        'expires_at',
        'active',
    ];

    protected $casts = [
        'montant_fcfa' => 'decimal:2',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedUser()
    {
        return $this->belongsTo(EliteUser::class, 'assigned_to');
    }

    public function usedByUser()
    {
        return $this->belongsTo(EliteUser::class, 'used_by');
    }

    public function isValid(): bool
    {
        return $this->active && 
               !$this->used_at && 
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function canBeUsedBy(EliteUser $user): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Si assigné à un utilisateur spécifique
        if ($this->assigned_to !== null && $this->assigned_to !== $user->id) {
            return false;
        }

        return true;
    }

    public static function generateCode(): string
    {
        do {
            $code = 'CASH-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
