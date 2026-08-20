<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Récupérer une valeur de paramètre
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            try {
                $setting = self::where('key', $key)->first();
                
                if (!$setting) {
                    Log::warning("Setting not found: {$key}");
                    return $default;
                }

                return match ($setting->type) {
                    'integer' => (int) $setting->value,
                    'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                    'json' => json_decode($setting->value, true),
                    'float' => (float) $setting->value,
                    default => $setting->value,
                };
            } catch (\Exception $e) {
                Log::error("Error getting setting {$key}: " . $e->getMessage());
                return $default;
            }
        });
    }

    /**
     * Alias pour get()
     */
    public static function getValue(string $key, $default = null)
    {
        return self::get($key, $default);
    }

    /**
     * Définir une valeur de paramètre
     */
    public static function set(string $key, $value, string $type = 'string', ?string $description = null): void
    {
        try {
            $valueToStore = $type === 'json' ? json_encode($value) : (string) $value;
            
            self::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $valueToStore,
                    'type' => $type,
                    'description' => $description,
                ]
            );
            
            Cache::forget("setting_{$key}");
        } catch (\Exception $e) {
            Log::error("Error setting {$key}: " . $e->getMessage());
        }
    }

    /**
     * Alias pour set()
     */
    public static function setValue(string $key, $value, string $type = 'string', ?string $description = null): void
    {
        self::set($key, $value, $type, $description);
    }

    /**
     * Effacer le cache pour une clé spécifique
     */
    public static function clearCache(string $key): void
    {
        Cache::forget("setting_{$key}");
    }

    /**
     * Effacer tout le cache des paramètres
     */
    public static function clearAllCache(): void
    {
        $settings = self::all();
        foreach ($settings as $setting) {
            Cache::forget("setting_{$setting->key}");
        }
    }

    // ========================================
    // MÉTHODES UTILITAIRES - CLÉS CORRECTES
    // ========================================

    /**
     * Taux de conversion FCFA vers Points
     * Clé BD: taux_conversion_fcfa_points
     */
    public static function getTauxConversionFcfaPoints(): int
    {
        return self::get('taux_conversion_fcfa_points', 650);
    }

    /**
     * Points par parrainage
     * Clé BD: points_parrainage
     */
    public static function getPointsParrainage(): int
    {
        return self::get('points_parrainage', 1);
    }

    /**
     * Code de parrainage par défaut
     * Clé BD: code_parrainage_defaut
     */
    public static function getCodeParrainageDefaut(): string
    {
        return self::get('code_parrainage_defaut', 'ELITE2026');
    }

    // ========================================
    // MÉTHODES DE COMPATIBILITÉ (anciennes clés)
    // ========================================

    /**
     * @deprecated Utiliser getTauxConversionFcfaPoints()
     */
    public static function getFcfaPerPoint(): int
    {
        return self::getTauxConversionFcfaPoints();
    }

    /**
     * @deprecated Utiliser getPointsParrainage()
     */
    public static function getPointsPerReferral(): int
    {
        return self::getPointsParrainage();
    }

    /**
     * @deprecated Utiliser getCodeParrainageDefaut()
     */
    public static function getDefaultReferralCode(): string
    {
        return self::getCodeParrainageDefaut();
    }

    /**
     * Prix par défaut d'un pack (non présent dans la BD actuellement)
     */
    public static function getDefaultPackPrice(): int
    {
        return self::get('prix_pack_defaut', 50);
    }

    // ========================================
    // MÉTHODES DE CONFIGURATION (SET)
    // ========================================

    /**
     * Définir le taux de conversion
     */
    public static function setTauxConversionFcfaPoints(int $value): void
    {
        self::set('taux_conversion_fcfa_points', $value, 'integer', 'Taux de conversion FCFA vers Points');
    }

    /**
     * Définir les points par parrainage
     */
    public static function setPointsParrainage(int $value): void
    {
        self::set('points_parrainage', $value, 'integer', 'Nombre de points accordés par parrainage');
    }

    /**
     * Définir le code de parrainage par défaut
     */
    public static function setCodeParrainageDefaut(string $value): void
    {
        self::set('code_parrainage_defaut', $value, 'string', 'Code de parrainage par défaut de la plateforme');
    }

    // ========================================
    // MÉTHODES DE CONVERSION
    // ========================================

    /**
     * Convertir des FCFA en Points
     */
    public static function convertFcfaToPoints(int $fcfa): int
    {
        $taux = self::getTauxConversionFcfaPoints();
        if ($taux <= 0) {
            return 0;
        }
        return (int) floor($fcfa / $taux);
    }

    /**
     * Convertir des Points en FCFA
     */
    public static function convertPointsToFcfa(int $points): int
    {
        $taux = self::getTauxConversionFcfaPoints();
        return $points * $taux;
    }

    // ========================================
    // MÉTHODES DE VÉRIFICATION
    // ========================================

    /**
     * Vérifier si une clé existe
     */
    public static function has(string $key): bool
    {
        return self::where('key', $key)->exists();
    }

    /**
     * Obtenir tous les paramètres sous forme de tableau
     */
    public static function getAll(): array
    {
        return Cache::remember('all_settings', 3600, function () {
            $settings = self::all();
            $result = [];
            
            foreach ($settings as $setting) {
                $result[$setting->key] = match ($setting->type) {
                    'integer' => (int) $setting->value,
                    'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                    'json' => json_decode($setting->value, true),
                    'float' => (float) $setting->value,
                    default => $setting->value,
                };
            }
            
            return $result;
        });
    }

    /**
     * Supprimer un paramètre
     */
    public static function remove(string $key): bool
    {
        try {
            self::where('key', $key)->delete();
            Cache::forget("setting_{$key}");
            return true;
        } catch (\Exception $e) {
            Log::error("Error removing setting {$key}: " . $e->getMessage());
            return false;
        }
    }
}