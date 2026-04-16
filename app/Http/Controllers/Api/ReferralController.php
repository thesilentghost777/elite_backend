<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EliteUser;
use App\Models\ReferralProject;
use App\Models\ReferralGoal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralController extends Controller
{
    /**
     * Obtenir le code de parrainage + lien web
     */
    public function myCode(Request $request): JsonResponse
    {
        $user = $request->user();

        // Vérifier que l'utilisateur a au moins un pack
        if (!$user->userPacks()->where('statut', 'actif')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez avoir au moins une formation active pour accéder au parrainage.',
                'requires_pack' => true,
            ], 403);
        }

        $referralLink   = config('app.url') . '/register?ref=' . $user->referral_code;
        $shareMessage   = "Inscrit toi a Elite 2.0 et beneficie jusqu'a 3 Millions de subvention a la fin de ta formation {$referralLink}";

        return response()->json([
            'success' => true,
            'data' => [
                'code'          => $user->referral_code,
                'referral_link' => $referralLink,
                'share_message' => $shareMessage,
            ],
        ]);
    }

    /**
     * Statistiques de parrainage avec arbre de filleuls
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        $tree  = $this->buildReferralTree($user->id, 5);
        $depth = $this->calculateTreeDepth($tree);

        // Filleuls directs (niveau 1)
        $directFilleuls = DB::table('referral_history')
            ->where('parrain_id', $user->id)
            ->count();

        // Filleuls directs ayant acheté un pack
        $directFilleulsWithPack = DB::table('referral_history')
            ->where('parrain_id', $user->id)
            ->where('has_purchased_pack', true)
            ->count();

        // Objectif actif
        $activeGoal = ReferralGoal::where('user_id', $user->id)
            ->where('statut', 'actif')
            ->with('project')
            ->first();

        // Palier atteint ?
        $palierAtteint = $this->checkPalierAtteint($user->id, $activeGoal?->palier_cible ?? null, $tree);

        return response()->json([
            'success' => true,
            'data' => [
                'direct_filleuls'              => $directFilleuls,
                'direct_filleuls_with_pack'    => $directFilleulsWithPack,
                'tree_depth'                   => $depth,
                'tree'                         => $tree,
                'palier_atteint'               => $palierAtteint,
                'active_goal'                  => $activeGoal ? [
                    'id'           => $activeGoal->id,
                    'palier_cible' => $activeGoal->palier_cible,
                    'gain_fcfa'    => $this->getPalierGain($activeGoal->palier_cible),
                    'project'      => [
                        'nom'     => $activeGoal->project->nom,
                        'pack_id' => $activeGoal->project->pack_id,
                    ],
                    'statut'       => $activeGoal->statut,
                ] : null,
                'paliers_info' => $this->getPaliersInfo(),
                'can_request_withdrawal' => $this->canRequestWithdrawal($user, $activeGoal, $palierAtteint, $directFilleulsWithPack),
            ],
        ]);
    }

    /**
     * Historique des filleuls directs
     */
    public function history(Request $request): JsonResponse
    {
        $user = $request->user();

        $history = DB::table('referral_history')
            ->join('elite_users', 'referral_history.filleul_id', '=', 'elite_users.id')
            ->where('referral_history.parrain_id', $user->id)
            ->select(
                'elite_users.prenom',
                'elite_users.nom',
                'elite_users.ville',
                'referral_history.has_purchased_pack',
                'referral_history.created_at as date_inscription'
            )
            ->orderBy('referral_history.created_at', 'desc')
            ->get()
            ->map(fn($item) => [
                'filleul' => [
                    'prenom' => $item->prenom,
                    'nom'    => $item->nom,
                    'ville'  => $item->ville,
                ],
                'has_purchased_pack' => (bool) $item->has_purchased_pack,
                'date_inscription'   => $item->date_inscription,
            ]);

        return response()->json(['success' => true, 'data' => $history]);
    }

    /**
     * Infos du parrain
     */
    public function myParrain(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->referred_by) {
            return response()->json(['success' => true, 'data' => null]);
        }

        $parrain = EliteUser::where('referral_code', $user->referred_by)
            ->select('id', 'prenom', 'nom', 'ville', 'referral_code')
            ->first();

        if (!$parrain) {
            return response()->json(['success' => false, 'message' => 'Parrain introuvable'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'prenom' => $parrain->prenom,
                'nom'    => $parrain->nom,
                'ville'  => $parrain->ville,
                'code'   => $parrain->referral_code,
            ],
        ]);
    }

    /**
     * Créer un projet de parrainage
     */
    public function createProject(Request $request): JsonResponse
    {
        $request->validate([
            'nom'          => 'required|string|max:255',
            'pack_id'      => 'required|integer|exists:packs,id',
            'palier_cible' => 'required|integer|in:2,3,4,5',
        ]);

        $user = $request->user();

        // Vérifier que l'utilisateur possède ce pack
        if (!$user->hasPack($request->pack_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez avoir acheté ce pack de formation pour créer ce projet.',
            ], 422);
        }

        // Vérifier qu'il n'y a pas déjà un objectif actif
        $existingGoal = ReferralGoal::where('user_id', $user->id)->where('statut', 'actif')->first();
        if ($existingGoal) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà un objectif de parrainage actif.',
            ], 422);
        }

        // Vérifier que le palier n'a pas déjà été atteint (bloqué après reset)
        $completedGoal = ReferralGoal::where('user_id', $user->id)
            ->where('palier_cible', $request->palier_cible)
            ->where('statut', 'complete')
            ->exists();
        if ($completedGoal) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà atteint et encaissé ce palier. Choisissez un palier supérieur.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $project = ReferralProject::create([
                'user_id' => $user->id,
                'nom'     => $request->nom,
                'pack_id' => $request->pack_id,
            ]);

            $goal = ReferralGoal::create([
                'user_id'      => $user->id,
                'project_id'   => $project->id,
                'palier_cible' => $request->palier_cible,
                'statut'       => 'actif',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'project' => ['id' => $project->id, 'nom' => $project->nom],
                    'goal'    => ['id' => $goal->id, 'palier_cible' => $goal->palier_cible, 'gain_fcfa' => $this->getPalierGain($goal->palier_cible)],
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create referral project error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de la création du projet.'], 500);
        }
    }

    /**
     * Demander un retrait (WhatsApp redirect)
     */
    public function requestWithdrawal(Request $request): JsonResponse
    {
        $user = $request->user();

        $activeGoal = ReferralGoal::where('user_id', $user->id)->where('statut', 'actif')->first();
        if (!$activeGoal) {
            return response()->json(['success' => false, 'message' => 'Aucun objectif actif.'], 422);
        }

        $tree       = $this->buildReferralTree($user->id, 5);
        $palierOk   = $this->checkPalierAtteint($user->id, $activeGoal->palier_cible, $tree);

        $directFilleulsWithPack = DB::table('referral_history')
            ->where('parrain_id', $user->id)
            ->where('has_purchased_pack', true)
            ->count();

        if (!$palierOk) {
            return response()->json(['success' => false, 'message' => 'Palier non encore atteint.'], 422);
        }

        if ($directFilleulsWithPack < 1) {
            return response()->json(['success' => false, 'message' => 'Aucun filleul direct n\'a acheté un pack.'], 422);
        }

        // Vérifier 100% formation complétée
        $packCompletion = DB::table('user_packs')
            ->where('user_id', $user->id)
            ->where('pack_id', $activeGoal->project->pack_id)
            ->value('progression');

        if ($packCompletion < 100) {
            return response()->json([
                'success' => false,
                'message' => "Vous devez compléter 100% de votre formation ({$packCompletion}% actuellement).",
            ], 422);
        }

        $gainFcfa = $this->getPalierGain($activeGoal->palier_cible);
        $unlocksAll = in_array($activeGoal->palier_cible, [4, 5]);

        // Préparer le message WhatsApp pré-établi
        $whatsappMessage = urlencode(
            "Bonjour, je souhaite effectuer un retrait Elite 2.0.\n" .
            "Nom : {$user->prenom} {$user->nom}\n" .
            "Code : {$user->referral_code}\n" .
            "Palier atteint : {$activeGoal->palier_cible}\n" .
            "Gain : {$gainFcfa} FCFA\n" .
            "Projet : {$activeGoal->project->nom}"
        );

        $whatsappUrl = "https://wa.me/237659292001?text={$whatsappMessage}";

        return response()->json([
            'success' => true,
            'data' => [
                'whatsapp_url'  => $whatsappUrl,
                'gain_fcfa'     => $gainFcfa,
                'unlocks_all'   => $unlocksAll,
                'palier'        => $activeGoal->palier_cible,
                'goal_id'       => $activeGoal->id,
            ],
        ]);
    }

    /**
     * Confirmer le retrait et réinitialiser le compte
     */
    public function confirmWithdrawal(Request $request): JsonResponse
    {
        $request->validate(['goal_id' => 'required|integer']);

        $user = $request->user();
        $goal = ReferralGoal::where('id', $request->goal_id)
            ->where('user_id', $user->id)
            ->where('statut', 'actif')
            ->with('project')
            ->first();

        if (!$goal) {
            return response()->json(['success' => false, 'message' => 'Objectif introuvable.'], 404);
        }

        DB::beginTransaction();
        try {
            $unlocksAll = in_array($goal->palier_cible, [4, 5]);

            // Marquer l'objectif comme complété
            $goal->update(['statut' => 'complete', 'completed_at' => now()]);

            // Si palier 4 ou 5 : débloquer toutes les formations
            if ($unlocksAll) {
                $packs = \App\Models\Pack::where('active', true)->get();
                foreach ($packs as $pack) {
                    if (!$user->hasPack($pack->id)) {
                        \App\Models\UserPack::create([
                            'user_id'       => $user->id,
                            'pack_id'       => $pack->id,
                            'duree_choisie' => 'illimité',
                            'prix_paye'     => 0,
                            'statut'        => 'actif',
                            'date_achat'    => now(),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Retrait confirmé. Votre compte a été réinitialisé.',
                'data' => ['unlocked_all_formations' => $unlocksAll],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Confirm withdrawal error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de la confirmation.'], 500);
        }
    }

    // ─── Helpers privés ────────────────────────────────────────────────────────

    /**
     * Construire l'arbre de filleuls jusqu'à une profondeur donnée
     */
    private function buildReferralTree(int $userId, int $maxDepth, int $currentDepth = 1): array
    {
        if ($currentDepth > $maxDepth) return [];

        $filleuls = DB::table('referral_history')
            ->join('elite_users', 'referral_history.filleul_id', '=', 'elite_users.id')
            ->where('referral_history.parrain_id', $userId)
            ->select('elite_users.id', 'elite_users.prenom', 'elite_users.nom', 'referral_history.has_purchased_pack')
            ->get();

        $result = [];
        foreach ($filleuls as $filleul) {
            $result[] = [
                'id'                => $filleul->id,
                'prenom'            => $filleul->prenom,
                'nom'               => $filleul->nom,
                'has_purchased_pack' => (bool) $filleul->has_purchased_pack,
                'depth'             => $currentDepth,
                'children'          => $this->buildReferralTree($filleul->id, $maxDepth, $currentDepth + 1),
            ];
        }

        return $result;
    }

    /**
     * Calculer la profondeur réelle de l'arbre (où chaque nœud a 5 enfants)
     */
    private function calculateTreeDepth(array $tree): int
    {
        if (empty($tree) || count($tree) < 5) return 1;

        $allChildrenComplete = true;
        foreach ($tree as $node) {
            if (count($node['children']) < 5) {
                $allChildrenComplete = false;
                break;
            }
        }

        if (!$allChildrenComplete) return 1;

        $minChildDepth = PHP_INT_MAX;
        foreach ($tree as $node) {
            $childDepth = $this->calculateTreeDepth($node['children']);
            $minChildDepth = min($minChildDepth, $childDepth);
        }

        return 1 + $minChildDepth;
    }

    /**
     * Vérifier si un palier cible est atteint
     */
    private function checkPalierAtteint(int $userId, ?int $palierCible, array $tree): bool
    {
        if (!$palierCible) return false;
        $depth = $this->calculateTreeDepth($tree);
        return $depth >= $palierCible;
    }

    /**
     * Gain en FCFA par palier
     */
    private function getPalierGain(int $palier): int
    {
        return match($palier) {
            2 => 50000,
            3 => 100000,
            4 => 500000,
            5 => 3000000,
            default => 0,
        };
    }

    /**
     * Info sur tous les paliers
     */
    private function getPaliersInfo(): array
    {
        return [
            ['palier' => 2, 'description' => '5 filleuls directs, chacun avec 5 filleuls', 'gain_fcfa' => 50000,   'unlocks_all' => false],
            ['palier' => 3, 'description' => 'Profondeur 3 complète',                      'gain_fcfa' => 100000,  'unlocks_all' => false],
            ['palier' => 4, 'description' => 'Profondeur 4 complète',                      'gain_fcfa' => 500000,  'unlocks_all' => true],
            ['palier' => 5, 'description' => 'Profondeur 5 complète',                      'gain_fcfa' => 3000000, 'unlocks_all' => true],
        ];
    }

    /**
     * Vérifier toutes les conditions pour demander un retrait
     */
    private function canRequestWithdrawal($user, $activeGoal, bool $palierAtteint, int $directFilleulsWithPack): bool
    {
        if (!$activeGoal || !$palierAtteint || $directFilleulsWithPack < 1) return false;

        $packCompletion = DB::table('user_packs')
            ->where('user_id', $user->id)
            ->where('pack_id', $activeGoal->project->pack_id ?? 0)
            ->value('progression');

        return $packCompletion >= 100;
    }
}