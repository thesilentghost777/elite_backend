<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPaymentInstallment extends Model
{
    use HasFactory;

    protected $fillable = ['user_pack_id', 'plan_installment_id', 'montant_fcfa', 'due_at', 'paid_at', 'statut'];

    protected $casts = ['montant_fcfa' => 'decimal:2', 'due_at' => 'datetime', 'paid_at' => 'datetime'];

    public function userPack() { return $this->belongsTo(UserPack::class); }
    public function planInstallment() { return $this->belongsTo(PartnerPaymentInstallment::class, 'plan_installment_id'); }
}