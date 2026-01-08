<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EliteUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'email',
        'dernier_diplome',
        'ville',
        'password',
        'referral_code',
        'referred_by',
        'solde_points',
        'correspondence_completed',
        'profile_chosen',
        'parcours_mode',
        'photo_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'correspondence_completed' => 'boolean',
        'profile_chosen' => 'boolean',
        'solde_points' => 'decimal:2',
    ];

      /**
     * Obtenir le profil de carrière actuellement choisi
     */
    public function getSelectedCareerProfile()
    {
        if (!$this->profile_chosen) {
            return null;
        }

        $choice = $this->profileChoice()->with('profile')->first();
        return $choice ? $choice->profile : null;
    }
    
    // Génère un code de parrainage unique
    public static function generateReferralCode(): string
    {
        do {
            $code = 'EL' . strtoupper(substr(md5(uniqid()), 0, 6));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    // Relations
    public function parrain()
    {
        return $this->belongsTo(EliteUser::class, 'referred_by', 'referral_code');
    }

    public function filleuls()
    {
        return $this->hasMany(EliteUser::class, 'referred_by', 'referral_code');
    }

    public function referralHistory()
    {
        return $this->hasMany(ReferralHistory::class, 'parrain_id');
    }

    public function correspondences()
    {
        return $this->hasMany(UserCorrespondence::class, 'user_id');
    }

    public function profileResults()
    {
        return $this->hasMany(UserProfileResult::class, 'user_id');
    }

    public function profileChoice()
    {
        return $this->hasOne(UserProfileChoice::class, 'user_id');
    }

    public function packs()
    {
        return $this->hasMany(UserPack::class, 'user_id');
    }

    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class, 'user_id');
    }

    public function quizResults()
    {
        return $this->hasMany(QuizResult::class, 'user_id');
    }

    public function chapterUnlocks()
    {
        return $this->hasMany(ChapterUnlock::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function sentTransfers()
    {
        return $this->hasMany(Transfer::class, 'sender_id');
    }

    public function receivedTransfers()
    {
        return $this->hasMany(Transfer::class, 'receiver_id');
    }

    // Helpers
    public function getFullNameAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function hasCompletedCorrespondence(): bool
    {
        return $this->correspondence_completed;
    }

    public function hasChosenProfile(): bool
    {
        return $this->profile_chosen;
    }

    public function getChosenProfile()
    {
        return $this->profileChoice?->profile;
    }

    public function hasPack(int $packId): bool
    {
        return $this->packs()->where('pack_id', $packId)->exists();
    }

    public function canAfford(int $points): bool
    {
        return $this->solde_points >= $points;
    }

    public function addPoints(float $points): void
    {
        $this->increment('solde_points', $points);
    }

    public function deductPoints(float $points): bool
    {
        if (!$this->canAfford($points)) {
            return false;
        }
        $this->decrement('solde_points', $points);
        return true;
    }
}
