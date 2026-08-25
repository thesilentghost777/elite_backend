<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerPaymentPlan extends Model
{
    use HasFactory;

    protected $fillable = ['partner_id', 'pack_id', 'nom', 'date_fin_formation', 'active'];

    protected $casts = ['date_fin_formation' => 'date', 'active' => 'boolean'];

    public function pack()
    {
        return $this->belongsTo(Pack::class);
    }

    public function installments()
    {
        return $this->hasMany(PartnerPaymentInstallment::class, 'plan_id')->orderBy('ordre');
    }
}