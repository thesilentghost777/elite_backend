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
        'partner_id',
        'pack_id',
        'tranches',
        'assigned_to',
        'used_by',
        'used_at',
        'expires_at',
        'active',
    ];

    protected $casts = [
        'montant_fcfa' => 'decimal:2',
        'tranches' => 'array',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class, 'pack_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(EliteUser::class, 'assigned_to');
    }

    public function user()
    {
        return $this->belongsTo(EliteUser::class, 'assigned_to');
    }

    public function usedByUser()
    {
        return $this->belongsTo(EliteUser::class, 'used_by');
    }

    public function getTranchesAttribute($value): array
    {
        if (is_array($value)) {
            return array_map('intval', $value);
        }
        if (empty($value)) {
            return [];
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return array_map('intval', $decoded);
            }
            return array_map('intval', array_filter(explode(',', $value)));
        }
        return [];
    }

    public function getTranchesLabelsAttribute(): array
    {
        $labels = [
            1 => 'Tranche 1 - Inscription (10k)',
            2 => 'Tranche 2 - Scolaire (200k)',
            3 => 'Tranche 3 - Matière d\'œuvre (135k)',
            4 => 'Tranche 4 - Inscription Examens (55k)',
            5 => 'Tranche 5 - Stage & Soutenance (55k)',
        ];
        $tranches = $this->tranches;
        return array_map(fn($t) => $labels[$t] ?? "Tranche {$t}", $tranches);
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

    public static function generateCode(?string $prefix = null): string
    {
        $p = $prefix ? strtoupper(trim($prefix)) . '-' : 'CASH-';
        do {
            $code = $p . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
