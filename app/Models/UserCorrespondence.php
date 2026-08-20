<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCorrespondence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'answer_id',
    ];

    public function user()
    {
        return $this->belongsTo(EliteUser::class, 'user_id');
    }

    public function question()
    {
        return $this->belongsTo(CorrespondenceQuestion::class, 'question_id');
    }

    public function answer()
    {
        return $this->belongsTo(CorrespondenceAnswer::class, 'answer_id');
    }
}
