<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerPaymentInstallment extends Model
{
    use HasFactory;

    protected $fillable = ['plan_id', 'libelle', 'montant_fcfa', 'delai_jours', 'ordre'];

    protected $casts = ['montant_fcfa' => 'decimal:2', 'delai_jours' => 'integer', 'ordre' => 'integer'];
}