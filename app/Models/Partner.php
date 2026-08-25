<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Partner extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = ['nom', 'code_partenaire', 'email', 'telephone', 'password', 'database_connection', 'active'];

    protected $hidden = ['password'];

    protected $casts = ['active' => 'boolean'];

    public static function generatePartnerCode(string $name = ''): string
    {
        $prefix = 'CFPAM';
        if (!empty($name)) {
            $clean = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($name));
            if (strlen($clean) >= 3) {
                $prefix = substr($clean, 0, 5);
            }
        }

        do {
            $code = $prefix . '-' . strtoupper(substr(md5(uniqid()), 0, 4));
        } while (self::where('code_partenaire', $code)->exists());

        return $code;
    }

    public function learners()
    {
        return $this->hasMany(EliteUser::class);
    }

    public function paymentPlans()
    {
        return $this->hasMany(PartnerPaymentPlan::class);
    }

    public function schedules()
    {
        return $this->hasMany(CourseSchedule::class);
    }

    public function packs()
    {
        return $this->belongsToMany(Pack::class, 'partner_pack_access')
            ->withPivot(['prix_fcfa', 'active'])->withTimestamps();
    }
}