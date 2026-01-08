<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrespondenceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'ordre',
    ];

    public function questions()
    {
        return $this->hasMany(CorrespondenceQuestion::class, 'category_id')->orderBy('ordre');
    }
}
