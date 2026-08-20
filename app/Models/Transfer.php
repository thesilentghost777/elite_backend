<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'points',
        'motif',
        'transaction_envoi_id',
        'transaction_recu_id',
    ];

    protected $casts = [
        'points' => 'decimal:2',
    ];

    public function sender()
    {
        return $this->belongsTo(EliteUser::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(EliteUser::class, 'receiver_id');
    }

    public function transactionEnvoi()
    {
        return $this->belongsTo(Transaction::class, 'transaction_envoi_id');
    }

    public function transactionRecu()
    {
        return $this->belongsTo(Transaction::class, 'transaction_recu_id');
    }
}
