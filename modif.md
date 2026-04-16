voici les modifications qu'il faut

=> desormais l'argent n'est utiliser que pour payer les cours
=> on ne peux plus payer les points
=> les points ne sont obtenu que via invitation (parrainage)
=> toutes les formations c'est 10.000 FCFA a payer depuis money fusion (directement en argent)
=> ce qui est 10k ce nest plus les formations unitaires mais uniquement les packs (exemple : audiovisuel tout le pack a 10k , digital: tout le pack a 10k ; ... payer 10k deploque donc tout les cours du pack)

=>pour pouvoir commencer le parrainage il faut absolument payer au moins une formation: donc loption de parrainage ne sera disponible que pour ceux qui suivent deja une formation

=>le nouveau systeme de parrainage est le suivant:
    ->quelqun ne peux avoir que 5 filleuls direct
    ->sil continue avec son code de parrainage , les nouveaux combleront au fur et a mesure son arbre de filleul en s'assurant de completer un niveau de profondeur totalement avant de passer au suivant
    ->voici les palier de gain en fonction du niveau

    si on a 5 filleuls et que notre arbre de filleul est  en profondeur 2 (les 5 filleuls ont deja tous chacun deja 5 filleuls chacun) : on gagne 50000 Fcfa

    si on est en profondeur 3 : on gagne 100000FCFA

    si on est en profondeur 4 : on gagne 500000FCFA + toutes les formations debloquer

    si on est en profondeur 5 : on gagne 3 Millions + toutes les formations debloquer


et le parrainage ne sera pas demarrer au hasard:

au debut la personne doit choisir quel palier elle veut atteindre et gagner
des quelle a chosi , on la laisse travailler et si elle n'arrive pas a atteindre le palier choisi alors c son probleme
de plus le choix du palier doit etre lier a un projet : un projet est caracteriser par un nom et un pack de formation qui a deja ete payer (pour etre sur que largent gagner lors du parrainage sera absolument lier a un projet)

et pour que le bouton d'obtention de son parrainage soit actif, il faut absolument que au moins un de tes filleuls ait acheter un pack formation (10000F)

si ce n'est ps le cas alors le bouton de demande de reception de son gain n'apparaitra jamais et sans justification

un utilisateur meme sil arrive a atteindre lobjectif de parrainage , le bouton de demande de retrait ne sactive que sil a completer 100% de son pack de formation

une fois l'objectif de parrrainage atteint et toutes les conditions respecter, quelqun peut enfin avoir acces au bouton demander un retrait et la ce sera un bouton whatsapp qui va rediriger vers la discussion whatsapp vers le numero 6 59 29 20 01 en envoyant un mesage deja preetali qui 
va faciliter la prise en main

deuxiemement une fois la demande de retrait effectuer , et confirmer par lutilisateur (avant redirection vers whatsapp) son compte sera renitialiser

renitialiser signifie simplement : debloquer tous les cours si la personne avait pris un palier qui permet cela : ensuite empecher a lutilisateur de choisir encore le palier quil a reussi et le bloquer pour empecher qu'il redemande encore le retrait

il doit aussi avoir au lieu du solde , le progression de llutilisateur par rapport a son pack de formation et par rapport a ses  objectif de palier et quelque kpi

de plus nous avons 3 autres nouvelles modifications a faire


3-il yaura desormais une version web (blade laravel) pour la screen inscription qui vont permettre au personnes de remplir automatiquement leur code promo au moment de linscription (le code d'inviation sera directement dans le lien en temps que parametre facultatif) au moment du parrainage on va directement partager le lien avec le code de celui qui partage depuis app

tu vas donc creer les vues blade , le controlleur et les routes pour gerer cela
dans la version web aussi on pourra sincrire par google ou apple et si on se connecte pas email ou phone on devra verifier via un otp : et directement apres,linscription tu dirigera lutilisateur vers playstore ou appstore en fonction de son appareil (pour quil continue avec lapp mobile)

donne moi les codes complet a copier coller pour les nouveaux fichiers a remplacer

enfin le texte de parrainage doit desormais etre "Inscrit toi a Elite 2.0 et beneficie jusqu'a 3 Millions de subvention a la fin de a formation  {lien parrainage web}"

NB: ici un filleul est toute personne qui a pour code de parrain ton code et qio a deja activer son compte (1000F)

desormais le depot dans le wallet ne va servir qua acheter les packs de formation / activer le compte
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MoneyFusionService;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private MoneyFusionService $moneyFusionService) {}

    /**
     * Étape 1 : Initier un dépôt — retourne l'URL de paiement MoneyFusion
     */
    public function initiateDeposit(Request $request): JsonResponse
    {
        $request->validate([
            'montant_fcfa' => 'required|numeric|min:500',
        ]);

        $user       = $request->user();
        $montant    = (float) $request->montant_fcfa;
        $reference  = Transaction::generateReference();

        // Créer la transaction en attente AVANT d'appeler MoneyFusion
        Transaction::create([
            'user_id'      => $user->id,
            'type'         => 'depot',
            'montant_fcfa' => $montant,
            'points'       => $this->getDepositPoints($montant),
            'reference'    => $reference,
            'description'  => "Dépôt de {$montant} FCFA",
            'statut'       => 'en_attente',
        ]);

        try {
            $paymentData = $this->moneyFusionService->formatPaymentData(
                $montant,
                $user->telephone,
                $user->nom . ' ' . $user->prenom,
                $user->id,
                $reference
            );

            $result = $this->moneyFusionService->initiatePayment($paymentData);

            if (!$result['success'] || empty($result['payment_url'])) {
                // Annuler la transaction en attente
                Transaction::where('reference', $reference)->update(['statut' => 'echoue']);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Impossible d\'initier le paiement',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data'    => [
                    'payment_url'   => $result['payment_url'],
                    'payment_token' => $result['token'],
                    'reference'     => $reference,
                ],
            ]);

        } catch (\Exception $e) {
            Transaction::where('reference', $reference)->update(['statut' => 'echoue']);

            Log::error('Deposit initiation error', [
                'user_id' => $user->id,
                'montant' => $montant,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Étape 2 : Vérifier le statut du paiement et créditer si confirmé
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        $request->validate([
            'payment_token' => 'required|string',
        ]);

        $user  = $request->user();
        $token = $request->payment_token;

        try {
            $result = $this->moneyFusionService->checkPaymentStatus($token);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Impossible de vérifier le paiement',
                ], 400);
            }

            $data       = $result['data'];
            $statut     = $data['statut']    ?? 'en_attente';
            $reference  = $data['personal_Info'][0]['transactionRef'] ?? null;

            // Retrouver la transaction en_attente
            $transaction = Transaction::where('reference', $reference)
                ->where('user_id', $user->id)
                ->where('statut', 'en_attente')
                ->first();

            // Créditer une seule fois si payé
            if (in_array($statut, ['paid', 'complete']) && $transaction) {
                DB::transaction(function () use ($user, $transaction) {
                    $user->addPoints($transaction->points);
                    $transaction->update(['statut' => 'complete']);
                });
            } elseif (in_array($statut, ['failure', 'echoue', 'annule']) && $transaction) {
                $transaction->update(['statut' => 'echoue']);
            }

            return response()->json([
                'success' => true,
                'data'    => [
                    'statut'  => $statut,
                    'montant' => $data['montant'] ?? null,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Payment status check error', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Webhook MoneyFusion — appelé automatiquement par MoneyFusion après paiement
     */
    public function webhook(Request $request): JsonResponse
{
    Log::info('MoneyFusion webhook received', $request->all());

    // ✅ Lire directement depuis le payload (plus besoin d'appel HTTP)
    $statut    = $request->input('statut');
    $reference = $request->input('personal_Info.0.transactionRef')
              ?? ($request->input('personal_Info')[0]['transactionRef'] ?? null);

    if (!$reference) {
        Log::error('Webhook: transactionRef manquant', $request->all());
        return response()->json(['success' => false, 'message' => 'Référence manquante'], 400);
    }

    $transaction = Transaction::where('reference', $reference)
        ->where('statut', 'en_attente')
        ->first();

    if (!$transaction) {
        Log::warning('Webhook: transaction non trouvée ou déjà traitée', ['reference' => $reference]);
        return response()->json(['success' => true]); // 200 pour éviter les retentatives
    }

    try {
        if (in_array($statut, ['paid', 'complete'])) {
            DB::transaction(function () use ($transaction) {
                $user = $transaction->user;
                $user->addPoints($transaction->points);
                $transaction->update(['statut' => 'complete']);
            });

            Log::info('Webhook: points crédités', [
                'reference' => $reference,
                'points'    => $transaction->points,
                'user_id'   => $transaction->user_id,
            ]);

        } elseif (in_array($statut, ['failure', 'echoue', 'annule', 'failed'])) {
            $transaction->update(['statut' => 'echoue']);
        }
        // "pending" → on ne fait rien, on attend le prochain webhook

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        Log::error('Webhook error', ['error' => $e->getMessage(), 'reference' => $reference]);
        return response()->json(['success' => false], 500);
    }
}

    /**
     * URL de retour après paiement (redirige l'utilisateur)
     */
    public function returnUrl(Request $request)
    {
        // Cette route est ouverte dans la WebView
        // Le frontend détecte cette URL et ferme la WebView
        return response()->json([
            'success' => true,
            'message' => 'Paiement traité',
        ]);
    }

    /**
     * Barème de conversion FCFA → points
     */
    private function getDepositPoints(float $montantFcfa): int
    {
        $bareme = [
            1000   => 3,
            2000   => 7,
            3000   => 10,
            5000   => 17,
            10000  => 35,
            20000  => 72,
            30000  => 110,
            50000  => 185,
            75000  => 280,
            100000 => 375,
        ];

        return $bareme[(int) $montantFcfa] ?? (int) ($montantFcfa / 650);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralController extends Controller
{
    /**
     * Obtenir le code de parrainage de l'utilisateur connecté
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function myCode(Request $request)
    {
        try {
            $user = $request->user();

            $shareMessage = "🚀 Lance ta carrière avec Elite 2.0 ! Code {$user->referral_code} pour débloquer ton accès et rejoindre des milliers de professionnels en devenir. Ta réussite commence maintenant ! 💼 Lien vers Elite 2.0 : https://play.google.com/store/apps/details?id=com.ghost777xsorganization.elite20";
            return response()->json([
                'success' => true,
                'data' => [
                    'code' => $user->referral_code,
                    'share_message' => $shareMessage
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du code de parrainage',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les statistiques de parrainage de l'utilisateur
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats(Request $request)
    {
        try {
            $user = $request->user();

            // Nombre total de filleuls
            $totalFilleuls = DB::table('referral_history')
                ->where('parrain_id', $user->id)
                ->count();

            // Points gagnés via parrainage
            $pointsGagnes = DB::table('referral_history')
                ->where('parrain_id', $user->id)
                ->sum('points_gagnes');

            // Filleuls ce mois
            $fillleulsCeMois = DB::table('referral_history')
                ->where('parrain_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_filleuls' => $totalFilleuls,
                    'points_gagnes' => (int) $pointsGagnes,
                    'filleuls_ce_mois' => $fillleulsCeMois
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir l'historique des parrainages
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Request $request)
    {
        try {
            $user = $request->user();

            $history = DB::table('referral_history')
                ->join('elite_users', 'referral_history.filleul_id', '=', 'elite_users.id')
                ->where('referral_history.parrain_id', $user->id)
                ->select(
                    'elite_users.prenom',
                    'elite_users.nom',
                    'elite_users.ville',
                    'referral_history.points_gagnes',
                    'referral_history.created_at as date_inscription'
                )
                ->orderBy('referral_history.created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'filleul' => [
                            'prenom' => $item->prenom,
                            'nom' => $item->nom,
                            'ville' => $item->ville
                        ],
                        'points_gagnes' => $item->points_gagnes,
                        'date_inscription' => $item->date_inscription
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $history
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'historique',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les informations du parrain de l'utilisateur connecté
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function myParrain(Request $request)
    {
        try {
            $user = $request->user();

            // Si l'utilisateur n'a pas de parrain
            if (!$user->referred_by) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'Vous n\'avez pas été parrainé'
                ], 200);
            }

            // Récupérer les infos du parrain
            $parrain = DB::table('elite_users')
                ->where('referral_code', $user->referred_by)
                ->select('id', 'prenom', 'nom', 'ville', 'referral_code')
                ->first();

            if (!$parrain) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parrain introuvable'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'parrain' => [
                        'prenom' => $parrain->prenom,
                        'nom' => $parrain->nom,
                        'ville' => $parrain->ville,
                        'code' => $parrain->referral_code
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du parrain',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}
    
    public function balance(Request $request): JsonResponse { 
        return response()->json([
            'success' => true, 
            'data' => $this->paymentService->getBalance($request->user())
        ]); 
    }
    
    public function deposit(Request $request): JsonResponse { 
        $request->validate(['montant_fcfa' => 'required|numeric|min:500']); 
        return response()->json([
            'success' => true, 
            'data' => $this->paymentService->deposit($request->user(), $request->montant_fcfa)
        ]); 
    }
    
    public function useCashCode(Request $request): JsonResponse { 
        $request->validate(['code' => 'required|string']); 
        return response()->json([
            'success' => true, 
            'data' => $this->paymentService->useCashCode($request->user(), $request->code)
        ]); 
    }
    
    public function findUser(Request $request): JsonResponse { 
        $request->validate(['telephone' => 'required|string']); 
        return response()->json([
            'success' => true, 
            'data' => $this->paymentService->findUserForTransfer($request->telephone)
        ]); 
    }
    
    public function transfer(Request $request): JsonResponse { 
        $request->validate([
            'telephone' => 'required|string', 
            'points' => 'required|numeric|min:1', 
            'motif' => 'nullable|string'
        ]); 
        return response()->json([
            'success' => true, 
            'data' => $this->paymentService->transfer(
                $request->user(), 
                $request->telephone, 
                $request->points, 
                $request->motif
            )
        ]); 
    }
    
    public function transactions(Request $request): JsonResponse { 
        return response()->json([
            'success' => true, 
            'data' => $this->paymentService->getTransactionHistory($request->user())
        ]); 
    }
    
    // ✅ CORRECTION: Retirer la validation de 'duree'
    public function purchasePack(Request $request, int $id): JsonResponse { 
        // Pas de validation - le pack est maintenant illimité par défaut
        return response()->json([
            'success' => true, 
            'data' => $this->paymentService->purchasePack($request->user(), $id)
        ]); 
    }
    
    public function myPacks(Request $request): JsonResponse { 
        return response()->json([
            'success' => true, 
            'data' => $this->paymentService->getUserPacks($request->user())
        ]); 
    }
}


<?php

namespace App\Services;

use App\Models\EliteUser;
use App\Models\ReferralHistory;
use App\Models\SystemSetting;

class ReferralService
{
    /**
     * Récupérer le code de parrainage de l'utilisateur
     */
    public function getMyCode(EliteUser $user): array
    {
        return [
            'code' => $user->referral_code,
            'lien_partage' => config('app.url') . '/register?ref=' . $user->referral_code,
            'points_par_parrainage' => SystemSetting::getPointsPerReferral(),
        ];
    }

    /**
     * Récupérer les statistiques de parrainage
     */
    public function getStats(EliteUser $user): array
    {
        $history = ReferralHistory::where('parrain_id', $user->id)->get();
        
        $totalFilleuls = $history->count();
        $totalPoints = $history->sum('points_gagnes');
        
        // Filleuls ce mois
        $filleulsCeMois = ReferralHistory::where('parrain_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'total_filleuls' => $totalFilleuls,
            'total_points_gagnes' => $totalPoints,
            'filleuls_ce_mois' => $filleulsCeMois,
            'points_par_parrainage' => SystemSetting::getPointsPerReferral(),
        ];
    }

    /**
     * Récupérer l'historique des parrainages
     */
    public function getHistory(EliteUser $user, int $limit = 20): array
    {
        $history = ReferralHistory::where('parrain_id', $user->id)
            ->with('filleul:id,nom,prenom,telephone,created_at')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $history->map(function ($item) {
            return [
                'id' => $item->id,
                'filleul' => [
                    'nom' => $item->filleul->nom,
                    'prenom' => $item->filleul->prenom,
                    'telephone' => substr($item->filleul->telephone, 0, 4) . '****' . substr($item->filleul->telephone, -2),
                ],
                'points_gagnes' => $item->points_gagnes,
                'date' => $item->created_at->format('d/m/Y H:i'),
            ];
        })->toArray();
    }

    /**
     * Récupérer les infos du parrain de l'utilisateur
     */
    public function getMyParrain(EliteUser $user): ?array
    {
        if (!$user->referred_by || $user->referred_by === SystemSetting::getDefaultReferralCode()) {
            return null;
        }

        $parrain = EliteUser::where('referral_code', $user->referred_by)
            ->select('id', 'nom', 'prenom', 'ville')
            ->first();

        if (!$parrain) {
            return null;
        }

        return [
            'nom' => $parrain->nom,
            'prenom' => $parrain->prenom,
            'ville' => $parrain->ville,
        ];
    }
}


<?php

namespace App\Services;

use App\Models\CashCode;
use App\Models\EliteUser;
use App\Models\Pack;
use App\Models\Module;
use App\Models\Chapter;
use App\Models\ChapterUnlock;
use App\Models\SystemSetting;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\UserPack;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Récupérer le solde de l'utilisateur
     */
    public function getBalance(EliteUser $user): array
    {
        $fcfaPerPoint = SystemSetting::getFcfaPerPoint();
        
        return [
            'points' => $user->solde_points,
            'equivalent_fcfa' => $user->solde_points * $fcfaPerPoint,
            'taux_conversion' => "1 point = {$fcfaPerPoint} FCFA",
        ];
    }

    /**
     * Effectuer un dépôt (simulation API de paiement)
     */
    public function deposit(EliteUser $user, float $montantFcfa): array
    {
        $points = $this->getDepositPoints($montantFcfa);

        return DB::transaction(function () use ($user, $montantFcfa, $points) {
            $paymentSuccess = true;

            if (!$paymentSuccess) {
                throw ValidationException::withMessages([
                    'payment' => ['Le paiement a échoué. Veuillez réessayer.']
                ]);
            }

            $user->addPoints($points);

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'depot',
                'montant_fcfa' => $montantFcfa,
                'points' => $points,
                'reference' => Transaction::generateReference(),
                'description' => "Dépôt de {$montantFcfa} FCFA",
                'statut' => 'complete',
            ]);

            return [
                'success' => true,
                'transaction' => [
                    'reference' => $transaction->reference,
                    'montant_fcfa' => $montantFcfa,
                    'points_credites' => $points,
                ],
                'nouveau_solde' => $user->solde_points,
            ];
        });
    }

    // Ajouter cette méthode privée
private function getDepositPoints(float $montantFcfa): int
{
    $bareme = [
        1000  => 3,
        2000  => 7,
        3000  => 10,
        5000  => 17,
        10000 => 35,
        20000 => 72,
        30000 => 110,
        50000 => 185,
        75000 => 280,
        100000 => 375,
    ];

    return $bareme[(int) $montantFcfa] ?? (int) ($montantFcfa / 650);
}

    /**
     * Utiliser un code caisse
     */
    public function useCashCode(EliteUser $user, string $code): array
    {
        $cashCode = CashCode::where('code', $code)->first();

        if (!$cashCode) {
            throw ValidationException::withMessages([
                'code' => ['Code caisse invalide.']
            ]);
        }

        if (!$cashCode->canBeUsedBy($user)) {
            if ($cashCode->used_at) {
                throw ValidationException::withMessages([
                    'code' => ['Ce code a déjà été utilisé.']
                ]);
            }
            if ($cashCode->assigned_to && $cashCode->assigned_to !== $user->id) {
                throw ValidationException::withMessages([
                    'code' => ['Ce code est assigné à un autre utilisateur.']
                ]);
            }
            if ($cashCode->expires_at && $cashCode->expires_at->isPast()) {
                throw ValidationException::withMessages([
                    'code' => ['Ce code a expiré.']
                ]);
            }
            throw ValidationException::withMessages([
                'code' => ['Ce code n\'est pas valide.']
            ]);
        }

        return DB::transaction(function () use ($user, $cashCode) {
            $cashCode->update([
                'used_by' => $user->id,
                'used_at' => now(),
                'active' => false,
            ]);

            $user->addPoints($cashCode->points);

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'code_caisse',
                'montant_fcfa' => $cashCode->montant_fcfa,
                'points' => $cashCode->points,
                'reference' => Transaction::generateReference(),
                'description' => "Code caisse: {$cashCode->code}",
                'metadata' => [
                    'cash_code_id' => $cashCode->id,
                    'code' => $cashCode->code,
                ],
                'statut' => 'complete',
            ]);

            return [
                'success' => true,
                'transaction' => [
                    'reference' => $transaction->reference,
                    'montant_fcfa' => $cashCode->montant_fcfa,
                    'points_credites' => $cashCode->points,
                ],
                'nouveau_solde' => $user->solde_points,
            ];
        });
    }

    /**
     * Rechercher un utilisateur pour transfert
     */
    public function findUserForTransfer(string $telephone): array
    {
        $user = EliteUser::where('telephone', $telephone)
            ->select('id', 'nom', 'prenom', 'telephone', 'ville')
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'telephone' => ['Aucun utilisateur trouvé avec ce numéro.']
            ]);
        }

        return [
            'id' => $user->id,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'telephone' => $user->telephone,
            'ville' => $user->ville,
        ];
    }

    /**
     * Effectuer un transfert
     */
    public function transfer(EliteUser $sender, string $receiverPhone, float $points, ?string $motif = null): array
    {
        if ($sender->telephone === $receiverPhone) {
            throw ValidationException::withMessages([
                'receiver' => ['Vous ne pouvez pas vous transférer des points à vous-même.']
            ]);
        }

        $receiver = EliteUser::where('telephone', $receiverPhone)->first();

        if (!$receiver) {
            throw ValidationException::withMessages([
                'receiver' => ['Destinataire non trouvé.']
            ]);
        }

        if (!$sender->canAfford($points)) {
            throw ValidationException::withMessages([
                'points' => ['Solde insuffisant pour ce transfert.']
            ]);
        }

        return DB::transaction(function () use ($sender, $receiver, $points, $motif) {
            $sender->deductPoints($points);
            $receiver->addPoints($points);

            $transactionEnvoi = Transaction::create([
                'user_id' => $sender->id,
                'type' => 'transfert_envoi',
                'points' => -$points,
                'reference' => Transaction::generateReference(),
                'description' => "Transfert vers {$receiver->full_name}",
                'metadata' => [
                    'receiver_id' => $receiver->id,
                    'receiver_nom' => $receiver->full_name,
                    'motif' => $motif,
                ],
                'statut' => 'complete',
            ]);

            $transactionRecu = Transaction::create([
                'user_id' => $receiver->id,
                'type' => 'transfert_recu',
                'points' => $points,
                'reference' => Transaction::generateReference(),
                'description' => "Transfert reçu de {$sender->full_name}",
                'metadata' => [
                    'sender_id' => $sender->id,
                    'sender_nom' => $sender->full_name,
                    'motif' => $motif,
                ],
                'statut' => 'complete',
            ]);

            Transfer::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'points' => $points,
                'motif' => $motif,
                'transaction_envoi_id' => $transactionEnvoi->id,
                'transaction_recu_id' => $transactionRecu->id,
            ]);

            return [
                'success' => true,
                'transfer' => [
                    'reference' => $transactionEnvoi->reference,
                    'points' => $points,
                    'destinataire' => [
                        'nom' => $receiver->full_name,
                        'telephone' => $receiver->telephone,
                    ],
                ],
                'nouveau_solde' => $sender->fresh()->solde_points,
            ];
        });
    }

    /**
     * Récupérer l'historique des transactions
     */
    public function getTransactionHistory(EliteUser $user, int $limit = 20): array
    {
        $transactions = Transaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'type' => $transaction->type,
                'type_label' => $this->getTransactionTypeLabel($transaction->type),
                'montant_fcfa' => $transaction->montant_fcfa,
                'points' => $transaction->points,
                'reference' => $transaction->reference,
                'description' => $transaction->description,
                'statut' => $transaction->statut,
                'date' => $transaction->created_at->format('d/m/Y H:i'),
            ];
        })->toArray();
    }

   public function purchasePack(EliteUser $user, int $packId): array
    {
        $pack = Pack::findOrFail($packId);

        if ($user->hasPack($packId)) {
            throw ValidationException::withMessages([
                'pack' => ['Vous possédez déjà ce pack.']
            ]);
        }

        if (!$user->canAfford($pack->prix_points)) {
            throw ValidationException::withMessages([
                'solde' => ['Solde insuffisant. Il vous manque ' . ($pack->prix_points - $user->solde_points) . ' points.']
            ]);
        }

        return DB::transaction(function () use ($user, $pack) {
            $user->deductPoints($pack->prix_points);

            // Créer l'accès au pack
            $userPack = UserPack::create([
                'user_id' => $user->id,
                'pack_id' => $pack->id,
                'duree_choisie' => 'illimité',
                'prix_paye' => $pack->prix_points,
                'statut' => 'actif',
                'date_achat' => now(),
                'date_expiration' => null,
            ]);

            // ✅ CORRECTION: Débloquer UNIQUEMENT le premier chapitre du premier module
            $firstModule = Module::where('pack_id', $pack->id)
                ->where('active', true)
                ->orderBy('ordre')
                ->first();

            if ($firstModule) {
                $firstChapter = Chapter::where('module_id', $firstModule->id)
                    ->where('active', true)
                    ->orderBy('ordre')
                    ->first();

                if ($firstChapter) {
                    ChapterUnlock::firstOrCreate([
                        'user_id' => $user->id,
                        'chapter_id' => $firstChapter->id,
                    ],
                    [
                        'unlock_method' => 'score',  // Or 'score' depending on your business logic
                        'unlocked_at' => now(),
                    ]);
                }
            }

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'achat_pack',
                'points' => -$pack->prix_points,
                'reference' => Transaction::generateReference(),
                'description' => "Achat du pack: {$pack->nom}",
                'metadata' => [
                    'pack_id' => $pack->id,
                    'pack_nom' => $pack->nom,
                ],
                'statut' => 'complete',
            ]);

            return [
                'success' => true,
                'pack' => [
                    'id' => $pack->id,
                    'nom' => $pack->nom,
                    'acces' => 'illimité',
                ],
                'transaction' => [
                    'reference' => $transaction->reference,
                    'points_debites' => $pack->prix_points,
                ],
                'nouveau_solde' => $user->fresh()->solde_points,
            ];
        });
    }

    /**
     * Récupérer les packs de l'utilisateur
     */
    public function getUserPacks(EliteUser $user): array
    {
        $userPacks = UserPack::where('user_id', $user->id)
            ->with('pack.category')
            ->orderByDesc('date_achat')
            ->get();

        return $userPacks->map(function ($userPack) {
            return [
                'id' => $userPack->id,
                'pack' => [
                    'id' => $userPack->pack->id,
                    'nom' => $userPack->pack->nom,
                    'slug' => $userPack->pack->slug,
                    'category' => $userPack->pack->category->nom,
                ],
                'duree_choisie' => $userPack->duree_choisie,
                'statut' => $userPack->statut,
                'progression' => $userPack->progression,
                'date_achat' => $userPack->date_achat->format('d/m/Y'),
                'date_expiration' => $userPack->date_expiration?->format('d/m/Y'),
                'jours_restants' => $userPack->date_expiration ? 
                    max(0, now()->diffInDays($userPack->date_expiration, false)) : null,
            ];
        })->toArray();
    }

    /**
     * Libellé du type de transaction
     */
    private function getTransactionTypeLabel(string $type): string
    {
        return match($type) {
            'depot' => 'Dépôt',
            'achat_pack' => 'Achat de pack',
            'parrainage' => 'Bonus parrainage',
            'transfert_envoi' => 'Transfert envoyé',
            'transfert_recu' => 'Transfert reçu',
            'code_caisse' => 'Code caisse',
            'bourse' => 'Bourse',
            default => $type,
        };
    }
}

// database/migrations/xxxx_add_trial_fields_to_elite_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elite_users', function (Blueprint $table) {
            $table->timestamp('trial_started_at')->nullable()->after('profile_chosen');
            $table->timestamp('trial_expires_at')->nullable()->after('trial_started_at');
            $table->boolean('account_activated')->default(false)->after('trial_expires_at');
            $table->timestamp('activated_at')->nullable()->after('account_activated');
        });
    }

    public function down(): void
    {
        Schema::table('elite_users', function (Blueprint $table) {
            $table->dropColumn(['trial_started_at', 'trial_expires_at', 'account_activated', 'activated_at']);
        });
    }
};

import { create } from 'zustand';
import { api } from '../services/api';

interface Filleul {
  filleul: {
    prenom: string;
    nom: string;
    ville: string;
  };
  points_gagnes: number;
  date_inscription: string;
}

interface ReferralState {
  myCode: string;
  shareMessage: string;
  totalFilleuls: number;
  pointsGagnes: number;
  filleulsCeMois: number;
  history: Filleul[];
  parrain: any | null;
  isLoading: boolean;

  fetchMyCode: () => Promise<void>;
  fetchStats: () => Promise<void>;
  fetchHistory: () => Promise<void>;
  fetchMyParrain: () => Promise<void>;
}

export const useReferralStore = create<ReferralState>((set) => ({
  myCode: '',
  shareMessage: '',
  totalFilleuls: 0,
  pointsGagnes: 0,
  filleulsCeMois: 0,
  history: [],
  parrain: null,
  isLoading: false,

  fetchMyCode: async () => {
    try {
      const response = await api.getMyReferralCode();
      const { code, share_message } = response.data.data;
      set({ myCode: code, shareMessage: share_message });
    } catch (error) {
      console.log('Fetch referral code error:', error);
    }
  },

  fetchStats: async () => {
    try {
      set({ isLoading: true });
      const response = await api.getReferralStats();
      const { total_filleuls, points_gagnes, filleuls_ce_mois } = response.data.data;
      set({
        totalFilleuls: total_filleuls,
        pointsGagnes: points_gagnes,
        filleulsCeMois: filleuls_ce_mois,
        isLoading: false,
      });
    } catch (error) {
      set({ isLoading: false });
    }
  },

  fetchHistory: async () => {
    try {
      set({ isLoading: true });
      const response = await api.getReferralHistory();
      set({ history: response.data.data, isLoading: false });
    } catch (error) {
      set({ isLoading: false });
    }
  },

  fetchMyParrain: async () => {
    try {
      const response = await api.getMyParrain();
      set({ parrain: response.data.data });
    } catch (error) {
      console.log('Fetch parrain error:', error);
    }
  },
}));


import { create } from 'zustand';
import { api } from '../services/api';

interface Transaction {
  id: number;
  type: string;
  type_label: string;
  montant_fcfa?: string | number;
  points: string | number;
  reference: string;
  description: string;
  statut: string;
  date: string;
  created_at?: string;
}

interface WalletState {
  balance: number;
  equivalentFcfa: number;
  transactions: Transaction[];
  allTransactions: Transaction[];
  isLoading: boolean;

  fetchBalance: () => Promise<void>;
  initiateDeposit: (montant: number) => Promise<any>;
  checkPaymentStatus: (paymentToken: string) => Promise<any>;
  useCashCode: (code: string) => Promise<any>;
  findUser: (telephone: string) => Promise<any>;
  transfer: (telephone: string, points: number, motif?: string) => Promise<any>;
  fetchTransactions: (params?: { type?: string; page?: number }) => Promise<void>;
  filterTransactions: (type: string) => void;
}

export const useWalletStore = create<WalletState>((set, get) => ({
  balance: 0,
  equivalentFcfa: 0,
  transactions: [],
  allTransactions: [],
  isLoading: false,

  fetchBalance: async () => {
    try {
      set({ isLoading: true });
      const response = await api.getBalance();
      
      const { points, equivalent_fcfa } = response.data.data;
      
      const balanceNumber = typeof points === 'string' 
        ? parseFloat(points) 
        : points;
        
      const fcfaNumber = typeof equivalent_fcfa === 'string' 
        ? parseFloat(equivalent_fcfa) 
        : equivalent_fcfa;
      
      const finalBalance = isNaN(balanceNumber) ? 0 : balanceNumber;
      const finalFcfa = isNaN(fcfaNumber) ? 0 : fcfaNumber;
      
      set({
        balance: finalBalance,
        equivalentFcfa: finalFcfa,
        isLoading: false,
      });
    } catch (error) {
      console.error('Error fetching balance:', error);
      set({ 
        balance: 0, 
        equivalentFcfa: 0, 
        isLoading: false 
      });
    }
  },

  /**
   * Initier un dépôt via MoneyFusion
   * Retourne l'URL de paiement et le token
   */
  initiateDeposit: async (montant: number) => {
    try {
      const response = await api.initiateDeposit(montant);
      return response.data;
    } catch (error) {
      console.error('Error initiating deposit:', error);
      throw error;
    }
  },

  /**
   * Vérifier le statut d'un paiement
   */
  checkPaymentStatus: async (paymentToken: string) => {
    try {
      const response = await api.checkPaymentStatus(paymentToken);
      return response.data;
    } catch (error) {
      console.error('Error checking payment status:', error);
      throw error;
    }
  },

  useCashCode: async (code: string) => {
    try {
      const response = await api.useCashCode(code);
      const { nouveau_solde } = response.data.data;
      
      const newBalance = typeof nouveau_solde === 'string' 
        ? parseFloat(nouveau_solde) 
        : nouveau_solde;
      
      const finalBalance = isNaN(newBalance) ? 0 : newBalance;
      set({ balance: finalBalance });
      return response.data;
    } catch (error) {
      console.error('Error using cash code:', error);
      throw error;
    }
  },

  findUser: async (telephone: string) => {
    try {
      const response = await api.findUser(telephone);
      return response.data.data;
    } catch (error) {
      console.error('Error finding user:', error);
      throw error;
    }
  },

  transfer: async (telephone: string, points: number, motif?: string) => {
    try {
      const response = await api.transfer(telephone, points, motif);
      const { nouveau_solde } = response.data.data;
      
      const newBalance = typeof nouveau_solde === 'string' 
        ? parseFloat(nouveau_solde) 
        : nouveau_solde;
      
      const finalBalance = isNaN(newBalance) ? 0 : newBalance;
      set({ balance: finalBalance });
      return response.data;
    } catch (error) {
      console.error('Error transferring:', error);
      throw error;
    }
  },

  fetchTransactions: async (params) => {
    try {
      set({ isLoading: true });
      
      const response = await api.getTransactions(params);
      
      console.log('📥 API Response:', response.data);
      
      let transactionsData;
      
      if (response.data?.data?.transactions) {
        transactionsData = response.data.data.transactions;
      } else if (Array.isArray(response.data?.data)) {
        transactionsData = response.data.data;
      } else if (response.data?.transactions) {
        transactionsData = response.data.transactions;
      } else if (Array.isArray(response.data)) {
        transactionsData = response.data;
      }
      
      const allTrans = Array.isArray(transactionsData) ? transactionsData : [];
      console.log('✅ Transactions chargées:', allTrans.length);
      
      set({
        allTransactions: allTrans,
        transactions: allTrans,
        isLoading: false,
      });
      
      if (params?.type) {
        get().filterTransactions(params.type);
      }
      
    } catch (error) {
      console.error('❌ Error fetching transactions:', error);
      set({ 
        transactions: [],
        allTransactions: [],
        isLoading: false 
      });
    }
  },

  filterTransactions: (type: string) => {
    const { allTransactions } = get();
    
    console.log('🔍 Filtrage par type:', type);
    console.log('📊 Total transactions:', allTransactions.length);
    
    if (!type || type === '') {
      set({ transactions: allTransactions });
      console.log('✅ Affichage de toutes les transactions');
    } else {
      const filtered = allTransactions.filter(t => {
        const transactionType = t.type.toLowerCase().replace(/_/g, '');
        const filterType = type.toLowerCase().replace(/_/g, '');
        
        console.log(`Comparing: "${transactionType}" with "${filterType}"`);
        
        return transactionType.includes(filterType) || filterType.includes(transactionType);
      });
      
      console.log('✅ Transactions filtrées:', filtered.length);
      set({ transactions: filtered });
    }
  },
}));

import axios, { AxiosInstance } from 'axios';
import * as SecureStore from 'expo-secure-store';

const BASE_URL = 'https://elite.supahuman.site/api';
//const BASE_URL = 'http://192.168.1.166:8000/api';

class ApiService {
  private api: AxiosInstance;

  constructor() {
    this.api = axios.create({
      baseURL: BASE_URL,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    });

    this.api.interceptors.request.use(async (config) => {
      const token = await SecureStore.getItemAsync('auth_token');
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
      return config;
    });

    this.api.interceptors.response.use(
      (response) => response,
      async (error) => {
        if (error.response?.status === 401) {
          await SecureStore.deleteItemAsync('auth_token');
        }
        return Promise.reject(error);
      }
    );
  }

  // ============ WALLET & PAYMENT ============
  
  async getBalance() {
    return this.api.get('/wallet/balance');
  }

  /**
   * Initier un dépôt via MoneyFusion
   * Retourne l'URL de paiement à ouvrir dans une WebView
   */
  async initiateDeposit(montant_fcfa: number) {
    return this.api.post('/payment/initiate-deposit', { montant_fcfa });
  }

  /**
   * Vérifier le statut d'un paiement
   */
  async checkPaymentStatus(payment_token: string) {
    return this.api.post('/payment/check-status', { payment_token });
  }

  async useCashCode(code: string) {
    return this.api.post('/wallet/use-cash-code', { code });
  }

  async findUser(telephone: string) {
    return this.api.post('/wallet/find-user', { telephone });
  }

  async transfer(telephone: string, points: number, motif?: string) {
    return this.api.post('/wallet/transfer', { telephone, points, motif });
  }

  async getTransactions(params?: { type?: string; page?: number }) {
    return this.api.get('/wallet/transactions', { params });
  }

  // ============ AUTH ============
  async register(data: {
    nom: string;
    prenom: string;
    telephone: string;
    email?: string;
    dernier_diplome: string;
    ville: string;
    password: string;
    password_confirmation: string;
    referral_code: string;
  }) {
    return this.api.post('/auth/register', data);
  }

  async login(telephone: string, password: string) {
    return this.api.post('/auth/login', { telephone, password });
  }

  async logout() {
    return this.api.post('/auth/logout');
  }

  async getProfile() {
    return this.api.get('/auth/profile');
  }

  async updateProfile(data: {
    nom?: string;
    prenom?: string;
    email?: string;
    ville?: string;
    password?: string;
    password_confirmation?: string;
    photo_url?: string;
  }) {
    return this.api.put('/auth/profile', data);
  }

  async checkReferralCode(code: string) {
    return this.api.post('/auth/check-referral-code', { code });
  }

  // ============ CORRESPONDENCE ============
  async getCorrespondenceQuestions() {
    return this.api.get('/correspondence/questions');
  }

  async submitCorrespondence(responses: { question_id: number; answer_id: number }[]) {
    return this.api.post('/correspondence/submit', { responses });
  }

  async getCorrespondenceResults() {
    return this.api.get('/correspondence/results');
  }

  async chooseProfile(profile_id: number) {
    return this.api.post('/correspondence/choose-profile', { profile_id });
  }

  async choosePath(mode: 'en_ligne' | 'presentiel' | 'externe') {
    return this.api.post('/correspondence/choose-path', { mode });
  }

  // ============ PROFILES & ROADMAPS ============
  async getProfiles(params?: { secteur?: string; is_cfpam?: boolean }) {
    return this.api.get('/profiles', { params });
  }

  async getProfileDetails(id: number) {
    return this.api.get(`/profiles/${id}`);
  }

  async getProfileRoadmap(id: number, niveau?: string) {
    return this.api.get(`/profiles/${id}/roadmap`, { params: { niveau } });
  }

  async getMyRoadmap() {
    return this.api.get('/my-roadmap');
  }

  async getSecteurs() {
    return this.api.get('/profiles/secteurs');
  }

  // ============ COURSES & PACKS ============
  async getCategories() {
    return this.api.get('/categories');
  }

  async getPacks(params?: { category_id?: number; niveau?: string }) {
    return this.api.get('/packs', { params });
  }

  async getPackDetails(id: number) {
    return this.api.get(`/packs/${id}`);
  }

  async getRecommendedPacks() {
    return this.api.get('/packs/recommended');
  }

  async purchasePack(id: number) {
    return this.api.post(`/packs/${id}/purchase`);
  }

  async getMyPacks() {
    return this.api.get('/user/packs');
  }

  async getPackModules(packId: number) {
    return this.api.get(`/packs/${packId}/modules`);
  }

  async getModuleChapters(moduleId: number) {
    return this.api.get(`/modules/${moduleId}/chapters`);
  }

  async getChapterLessons(chapterId: number) {
    return this.api.get(`/chapters/${chapterId}/lessons`);
  }

  async getLesson(lessonId: number) {
    return this.api.get(`/lessons/${lessonId}`);
  }

  async completeLesson(lessonId: number) {
    return this.api.post(`/lessons/${lessonId}/complete`);
  }

  async getChapterQuiz(chapterId: number) {
    return this.api.get(`/chapters/${chapterId}/quiz`);
  }

  async getChapterQuizInfo(chapterId: number) {
    return this.api.get(`/chapters/${chapterId}/quiz-info`);
  }

  async submitQuiz(quizId: number, responses: { question_id: number; answer_id: number }[]) {
    return this.api.post(`/quiz/${quizId}/submit`, { responses });
  }

  async unlockChapterByReferral(chapterId: number) {
    return this.api.post(`/chapters/${chapterId}/unlock-by-referral`);
  }

  // ============ REFERRAL ============
  async getMyReferralCode() {
    return this.api.get('/referral/my-code');
  }

  async getReferralStats() {
    return this.api.get('/referral/stats');
  }

  async getReferralHistory() {
    return this.api.get('/referral/history');
  }

  async getMyParrain() {
    return this.api.get('/referral/my-parrain');
  }

  // ============================================================
// À AJOUTER dans votre classe ApiService (api.ts)
// Collez ces méthodes à la fin de la classe, avant la fermeture
// ============================================================

  // ============ TRIAL / ACTIVATION ============

  /**
   * Récupère le statut du trial depuis le backend
   * Le backend stocke trial_started_at, trial_expires_at, account_activated
   */
  async getTrialStatus() {
    return this.api.get('/trial/status');
  }

  /**
   * Démarre le trial (appelé automatiquement au premier login)
   * Le backend enregistre trial_started_at = now(), trial_expires_at = now() + 10min
   */
  async startTrial() {
    return this.api.post('/trial/start');
  }

  /**
   * Active le compte en déduisant 1000 points du solde
   */
  async activateAccount() {
    return this.api.post('/trial/activate');
  }
}


export const api = new ApiService();

// navigation/index.tsx - Version avec système Trial 10 minutes
import React, { useState, useCallback } from 'react';
import { View, Platform, StatusBar } from 'react-native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createMaterialTopTabNavigator } from '@react-navigation/material-top-tabs';
import { Ionicons } from '@expo/vector-icons';
import { COLORS } from '../constants/theme';

// Auth Screens
import { WelcomeScreen } from '../screens/auth/WelcomeScreen';
import { LoginScreen } from '../screens/auth/LoginScreen';
import { RegisterScreen } from '../screens/auth/RegisterScreen';

// Correspondence Screens
import { CorrespondenceScreen } from '../screens/correspondence/CorrespondenceScreen';
import { CorrespondenceResultsScreen } from '../screens/correspondence/CorrespondenceResultsScreen';
import { PathChoiceScreen } from '../screens/correspondence/PathChoiceScreen';

// Main Screens
import { DashboardScreen } from '../screens/main/DashboardScreen';
import { ProfileScreen } from '../screens/main/ProfileScreen';
import { RoadmapScreen } from '../screens/main/RoadmapScreen';

// Course Screens
import { PacksScreen } from '../screens/courses/PacksScreen';
import { PackDetailsScreen } from '../screens/courses/PackDetailsScreen';
import { MyPacksScreen } from '../screens/courses/MyPacksScreen';
import { PackModulesScreen } from '../screens/courses/PackModulesScreen';
import { ModuleChaptersScreen } from '../screens/courses/ModuleChaptersScreen';
import { ChapterLessonsScreen } from '../screens/courses/ChapterLessonsScreen';
import { LessonScreen } from '../screens/courses/LessonScreen';
import { QuizScreen } from '../screens/courses/QuizScreen';

// Wallet Screens
import { WalletScreen } from '../screens/wallet/WalletScreen';
import { DepositScreen } from '../screens/wallet/DepositScreen';
import { CashCodeScreen } from '../screens/wallet/CashCodeScreen';
import { TransferScreen } from '../screens/wallet/TransferScreen';
import { TransactionsScreen } from '../screens/wallet/TransactionsScreen';

// Library Screen
import { LibraryScreen } from '../screens/library/LibraryScreen';

// Other Screens
import { ReferralScreen } from '../screens/referral/ReferralScreen';
import { FAQScreen } from '../screens/other/FAQScreen';
import { JobsScreen } from '../screens/other/JobsScreen';
import { ContestsScreen } from '../screens/other/ContestsScreen';

// ── TRIAL SYSTEM ─────────────────────────────────────────────
import { useTrialStore } from '../store/trialStore';
import { useTrialTimer } from '../hooks/useTrialTimer';
import { ActivateAccountModal } from '../components/ActivateAccountModal';
import { TrialBlockScreen } from '../screens/trial/TrialBlockScreen';

const Stack = createNativeStackNavigator();
const Tab = createMaterialTopTabNavigator();

const STATUS_BAR_HEIGHT = Platform.OS === 'ios' ? 44 : StatusBar.currentHeight || 0;
const SAFE_TOP_PADDING = STATUS_BAR_HEIGHT + 10;

// ─────────────────────────────────────────────────────────────
// HOC : enveloppe un écran de tab et le bloque si trial expiré
// ─────────────────────────────────────────────────────────────
const withTrialGuard = (
  WrappedScreen: React.ComponentType<any>,
  tabName: string,
  tabIcon: string
) => {
  return (props: any) => {
    const { accountActivated, trialExpired } = useTrialStore();
    const [showModal, setShowModal] = useState(false);

    if (!accountActivated && trialExpired) {
      return (
        <>
          <TrialBlockScreen
            tabName={tabName}
            tabIcon={tabIcon}
            onActivatePress={() => setShowModal(true)}
          />
          <ActivateAccountModal
            visible={showModal}
            dismissable={true}
            onClose={() => setShowModal(false)}
            onActivated={() => setShowModal(false)}
            navigation={props.navigation}
          />
        </>
      );
    }

    return <WrappedScreen {...props} />;
  };
};

// Versions des tabs avec protection trial
// ✅ Roadmap et Bibliothèque retirées — plus bloquées
const GuardedPacksScreen = withTrialGuard(PacksScreen, 'Formations', 'book');
const GuardedMyPacksScreen = withTrialGuard(MyPacksScreen, 'Mes cours', 'school');

// ─────────────────────────────────────────────────────────────
// TabNavigator — le cœur du système de blocage
// ─────────────────────────────────────────────────────────────
const TabNavigator = ({ navigation }: { navigation: any }) => {
  const [showActivateModal, setShowActivateModal] = useState(false);

  // ⬇️ C'est ici que le timer est lancé — au niveau du TabNavigator
  useTrialTimer();

  return (
    <View style={{ flex: 1, paddingTop: SAFE_TOP_PADDING, backgroundColor: COLORS.white }}>
      <Tab.Navigator
        screenOptions={{
          tabBarStyle: {
            backgroundColor: COLORS.white,
            elevation: 4,
            shadowColor: '#000',
            shadowOffset: { width: 0, height: 2 },
            shadowOpacity: 0.08,
            shadowRadius: 4,
            height: 62,
          },
          tabBarActiveTintColor: COLORS.primary,
          tabBarInactiveTintColor: '#555555',
          tabBarIndicatorStyle: {
            backgroundColor: COLORS.primary,
            height: 3,
          },
          tabBarLabelStyle: {
            fontWeight: '600',
            fontSize: 10,
            textTransform: 'none',
            marginTop: 1,
          },
          tabBarItemStyle: {
            paddingVertical: 6,
          },
          tabBarPressColor: COLORS.primary + '15',
          tabBarPressOpacity: 0.8,
          swipeEnabled: true,
          tabBarScrollEnabled: false,
        }}
      >
        {/* ✅ Dashboard — TOUJOURS accessible (contient le modal d'activation) */}
        <Tab.Screen
          name="Dashboard"
          options={{
            tabBarLabel: 'Accueil',
            tabBarIcon: ({ focused, color }) => (
              <Ionicons name={focused ? 'home' : 'home-outline'} size={20} color={color} />
            ),
          }}
        >
          {(props) => (
            <>
              <DashboardScreen
                {...props}
                onActivatePress={() => setShowActivateModal(true)}
              />
              {/* Modal global d'activation, accessible depuis Dashboard */}
              <ActivateAccountModal
                visible={showActivateModal}
                dismissable={true}
                onClose={() => setShowActivateModal(false)}
                onActivated={() => {
                  setShowActivateModal(false);
                }}
                navigation={navigation}
              />
            </>
          )}
        </Tab.Screen>

        {/* 🔒 Formations — bloqué si trial expiré */}
        <Tab.Screen
          name="Packs"
          component={GuardedPacksScreen}
          options={{
            tabBarLabel: 'Formations',
            tabBarIcon: ({ focused, color }) => (
              <Ionicons name={focused ? 'book' : 'book-outline'} size={20} color={color} />
            ),
          }}
        />

        {/* 🔒 Mes cours — bloqué si trial expiré */}
        <Tab.Screen
          name="MyPacks"
          component={GuardedMyPacksScreen}
          options={{
            tabBarLabel: 'Mes cours',
            tabBarIcon: ({ focused, color }) => (
              <Ionicons name={focused ? 'school' : 'school-outline'} size={20} color={color} />
            ),
          }}
        />

        {/* ✅ Bibliothèque — TOUJOURS accessible (plus bloquée) */}
        <Tab.Screen
          name="Library"
          component={LibraryScreen}
          options={{
            tabBarLabel: 'Biblio',
            tabBarIcon: ({ focused, color }) => (
              <Ionicons name={focused ? 'library' : 'library-outline'} size={20} color={color} />
            ),
          }}
        />

        {/* ✅ Roadmap — TOUJOURS accessible (plus bloquée) */}
        <Tab.Screen
          name="Roadmap"
          component={RoadmapScreen}
          options={{
            tabBarLabel: 'Roadmap',
            tabBarIcon: ({ focused, color }) => (
              <Ionicons name={focused ? 'map' : 'map-outline'} size={20} color={color} />
            ),
          }}
        />
      </Tab.Navigator>
    </View>
  );
};

// ─────────────────────────────────────────────────────────────
// Navigateurs Auth, Correspondence, Main (inchangés sauf TabNavigator)
// ─────────────────────────────────────────────────────────────

export const AuthNavigator = () => (
  <Stack.Navigator screenOptions={{ headerShown: false }}>
    <Stack.Screen name="Welcome" component={WelcomeScreen} />
    <Stack.Screen name="Login" component={LoginScreen} />
    <Stack.Screen name="Register" component={RegisterScreen} />
  </Stack.Navigator>
);

interface CorrespondenceNavigatorProps {
  initialScreen?: 'Correspondence' | 'CorrespondenceResults' | 'PathChoice';
}

export const CorrespondenceNavigator = ({
  initialScreen = 'Correspondence',
}: CorrespondenceNavigatorProps) => (
  <Stack.Navigator
    screenOptions={{ headerShown: false }}
    initialRouteName={initialScreen}
  >
    <Stack.Screen name="Correspondence" component={CorrespondenceScreen} />
    <Stack.Screen name="CorrespondenceResults" component={CorrespondenceResultsScreen} />
    <Stack.Screen name="PathChoice" component={PathChoiceScreen} />
  </Stack.Navigator>
);

export const MainNavigator = () => (
  <Stack.Navigator
    screenOptions={{
      headerShown: false,
      animation: 'slide_from_right',
    }}
  >
    {/* Onglets principaux — contient useTrialTimer */}
    <Stack.Screen name="MainTabs" component={TabNavigator} />

    {/* Profile */}
    <Stack.Screen
      name="Profile"
      component={ProfileScreen}
      options={{ animation: 'slide_from_bottom' }}
    />

    {/* Correspondence */}
    <Stack.Screen name="Correspondence" component={CorrespondenceScreen} />
    <Stack.Screen name="CorrespondenceResults" component={CorrespondenceResultsScreen} />
    <Stack.Screen name="PathChoice" component={PathChoiceScreen} />

    {/* Course Screens */}
    <Stack.Screen name="PackDetails" component={PackDetailsScreen} />
    <Stack.Screen name="PackModules" component={PackModulesScreen} />
    <Stack.Screen name="ModuleChapters" component={ModuleChaptersScreen} />
    <Stack.Screen name="ChapterLessons" component={ChapterLessonsScreen} />
    <Stack.Screen name="Lesson" component={LessonScreen} />
    <Stack.Screen name="Quiz" component={QuizScreen} />

    {/* Wallet Screens — Deposit toujours accessible pour recharger avant activation */}
    <Stack.Screen name="Wallet" component={WalletScreen} />
    <Stack.Screen name="Deposit" component={DepositScreen} />
    <Stack.Screen name="CashCode" component={CashCodeScreen} />
    <Stack.Screen name="Transfer" component={TransferScreen} />
    <Stack.Screen name="Transactions" component={TransactionsScreen} />

    {/* Other Screens */}
    <Stack.Screen name="Referral" component={ReferralScreen} />
    <Stack.Screen name="FAQ" component={FAQScreen} />
    <Stack.Screen name="Jobs" component={JobsScreen} />
    <Stack.Screen name="Contests" component={ContestsScreen} />
  </Stack.Navigator>
);

import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { COLORS, FONTS, SPACING, RADIUS, SHADOWS } from '../../constants/theme';
import { PackCard, Card } from '../../components';
import { useCourseStore } from '../../store/courseStore';
import { SafeAreaProvider, SafeAreaView } from 'react-native-safe-area-context';

interface PacksScreenProps {
  navigation: any;
}

export const PacksScreen: React.FC<PacksScreenProps> = ({ navigation }) => {
  const {
    categories,
    packs,
    recommendedPacks,
    isLoading,
    fetchCategories,
    fetchPacks,
    fetchRecommendedPacks,
    fetchMyPacks,
  } = useCourseStore();

  const [selectedCategory, setSelectedCategory] = useState<number | null>(null);
  const [showRecommended, setShowRecommended] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      await fetchMyPacks();
      await Promise.all([
        fetchCategories(),
        fetchPacks(),
        fetchRecommendedPacks(),
      ]);
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  const onRefresh = async () => {
    setRefreshing(true);
    await loadData();
    setRefreshing(false);
  };

  const handleCategorySelect = async (categoryId: number | null) => {
    setSelectedCategory(categoryId);
    setShowRecommended(categoryId === null);
    try {
      if (categoryId) {
        await fetchPacks({ category_id: categoryId });
      } else {
        await fetchPacks();
      }
    } catch (error) {
      console.error('Error fetching packs:', error);
    }
  };

  const displayedPacks = showRecommended && recommendedPacks.length > 0 
    ? recommendedPacks 
    : packs;

  return (
    <SafeAreaProvider>
      <SafeAreaView style={styles.container} edges={['left', 'right']}>
        <ScrollView
          style={styles.scrollView}
          showsVerticalScrollIndicator={false}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
          }
        >
          {/* Header scrollable */}
          <LinearGradient
            colors={[COLORS.primary, COLORS.primaryLight]}
            style={styles.header}
          >
            <TouchableOpacity
              style={styles.backButton}
              onPress={() => navigation.goBack()}
            >
              <Ionicons name="arrow-back" size={24} color={COLORS.white} />
            </TouchableOpacity>
            <Text style={styles.title}>Formations</Text>
            <Text style={styles.subtitle}>
              Découvrez nos packs de formation professionnelle
            </Text>
          </LinearGradient>

          {/* Toggle Recommandés / Tous */}
          {recommendedPacks.length > 0 && (
            <View style={styles.toggleContainer}>
              <TouchableOpacity
                style={[
                  styles.toggleButton,
                  showRecommended && styles.toggleButtonActive,
                ]}
                onPress={() => {
                  setShowRecommended(true);
                  setSelectedCategory(null);
                }}
              >
                <Ionicons 
                  name="star" 
                  size={18} 
                  color={showRecommended ? COLORS.white : COLORS.primary} 
                />
                <Text
                  style={[
                    styles.toggleText,
                    showRecommended && styles.toggleTextActive,
                  ]}
                >
                  Recommandés
                </Text>
              </TouchableOpacity>
              
              <TouchableOpacity
                style={[
                  styles.toggleButton,
                  !showRecommended && styles.toggleButtonActive,
                ]}
                onPress={() => {
                  setShowRecommended(false);
                  setSelectedCategory(null);
                }}
              >
                <Ionicons 
                  name="grid" 
                  size={18} 
                  color={!showRecommended ? COLORS.white : COLORS.primary} 
                />
                <Text
                  style={[
                    styles.toggleText,
                    !showRecommended && styles.toggleTextActive,
                  ]}
                >
                  Tous les packs
                </Text>
              </TouchableOpacity>
            </View>
          )}

          {/* Categories - visible seulement si "Tous les packs" est sélectionné */}
          {!showRecommended && (
            <ScrollView
              horizontal
              showsHorizontalScrollIndicator={false}
              style={styles.categoriesContainer}
              contentContainerStyle={styles.categoriesContent}
            >
              <TouchableOpacity
                style={[
                  styles.categoryChip,
                  selectedCategory === null && styles.categoryChipActive,
                ]}
                onPress={() => handleCategorySelect(null)}
              >
                <Text
                  style={[
                    styles.categoryText,
                    selectedCategory === null && styles.categoryTextActive,
                  ]}
                >
                  Tous
                </Text>
              </TouchableOpacity>
              
              {Array.isArray(categories) && categories.map((category: any) => {
                const categoryName = category?.nom || 'Catégorie';
                const categoryId = category?.id ?? 0;

                return (
                  <TouchableOpacity
                    key={categoryId}
                    style={[
                      styles.categoryChip,
                      selectedCategory === categoryId && styles.categoryChipActive,
                    ]}
                    onPress={() => handleCategorySelect(categoryId)}
                  >
                    <Text
                      style={[
                        styles.categoryText,
                        selectedCategory === categoryId && styles.categoryTextActive,
                      ]}
                    >
                      {categoryName}
                    </Text>
                  </TouchableOpacity>
                );
              })}
            </ScrollView>
          )}

          {/* Loading Indicator */}
          {isLoading && !refreshing && (
            <View style={styles.loadingContainer}>
              <ActivityIndicator size="large" color={COLORS.primary} />
            </View>
          )}

          {/* Section Title */}
          {!isLoading && displayedPacks.length > 0 && (
            <View style={styles.section}>
              <View style={styles.sectionHeader}>
                {showRecommended && (
                  <View style={styles.sectionTitleContainer}>
                    <Ionicons name="star" size={20} color={COLORS.secondary} />
                    <Text style={styles.sectionTitle}>Pour vous</Text>
                  </View>
                )}
                {!showRecommended && (
                  <Text style={styles.sectionTitle}>
                    {selectedCategory ? 'Formations de cette catégorie' : 'Toutes les formations'}
                  </Text>
                )}
                <Text style={styles.packCount}>{displayedPacks.length} formation(s)</Text>
              </View>

              {/* Packs List */}
              {displayedPacks.map((pack: any) => (
                <PackCard
                  key={pack?.id ?? Math.random()}
                  pack={pack}
                  onPress={() => navigation.navigate('PackDetails', { packId: pack.id })}
                  recommended={showRecommended}
                />
              ))}
            </View>
          )}

          {/* Empty State */}
          {!isLoading && displayedPacks.length === 0 && (
            <Card style={styles.emptyCard}>
              <Ionicons name="school-outline" size={48} color={COLORS.gray300} />
              <Text style={styles.emptyText}>
                {showRecommended 
                  ? 'Aucune formation recommandée pour le moment' 
                  : 'Aucune formation disponible'}
              </Text>
              {showRecommended && (
                <TouchableOpacity
                  style={styles.exploreButton}
                  onPress={() => setShowRecommended(false)}
                >
                  <Text style={styles.exploreButtonText}>Explorer toutes les formations</Text>
                </TouchableOpacity>
              )}
            </Card>
          )}
        </ScrollView>
      </SafeAreaView>
    </SafeAreaProvider>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  scrollView: {
    flex: 1,
  },
  header: {
    paddingTop: 50,
    paddingBottom: SPACING.xl,
    paddingHorizontal: SPACING.lg,
    borderBottomLeftRadius: RADIUS.xxl,
    borderBottomRightRadius: RADIUS.xxl,
  },
  backButton: {
    marginBottom: SPACING.md,
  },
  title: {
    fontSize: FONTS.sizes.xxl,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  subtitle: {
    fontSize: FONTS.sizes.md,
    color: 'rgba(255,255,255,0.8)',
    marginTop: SPACING.xs,
  },
  toggleContainer: {
    flexDirection: 'row',
    marginHorizontal: SPACING.lg,
    marginTop: SPACING.lg,
    marginBottom: SPACING.md,
    backgroundColor: COLORS.white,
    borderRadius: RADIUS.xl,
    padding: SPACING.xs,
    ...SHADOWS.small,
  },
  toggleButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: SPACING.sm,
    paddingHorizontal: SPACING.md,
    borderRadius: RADIUS.lg,
    gap: SPACING.xs,
  },
  toggleButtonActive: {
    backgroundColor: COLORS.primary,
  },
  toggleText: {
    fontSize: FONTS.sizes.md,
    fontWeight: '600',
    color: COLORS.primary,
  },
  toggleTextActive: {
    color: COLORS.white,
  },
  loadingContainer: {
    padding: SPACING.xxl,
    alignItems: 'center',
    justifyContent: 'center',
  },
  categoriesContainer: {
    maxHeight: 60,
  },
  categoriesContent: {
    paddingHorizontal: SPACING.lg,
    paddingVertical: SPACING.md,
    gap: SPACING.sm,
  },
  categoryChip: {
    paddingHorizontal: SPACING.lg,
    paddingVertical: SPACING.sm,
    borderRadius: RADIUS.full,
    backgroundColor: COLORS.white,
    marginRight: SPACING.sm,
    ...SHADOWS.small,
  },
  categoryChipActive: {
    backgroundColor: COLORS.primary,
  },
  categoryText: {
    fontSize: FONTS.sizes.md,
    fontWeight: '600',
    color: COLORS.gray600,
  },
  categoryTextActive: {
    color: COLORS.white,
  },
  section: {
    paddingHorizontal: SPACING.lg,
    marginBottom: SPACING.xl,
  },
  sectionHeader: {
    marginBottom: SPACING.md,
  },
  sectionTitleContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: SPACING.sm,
    marginBottom: SPACING.xs,
  },
  sectionTitle: {
    fontSize: FONTS.sizes.xl,
    fontWeight: 'bold',
    color: COLORS.black,
  },
  packCount: {
    fontSize: FONTS.sizes.sm,
    color: COLORS.gray500,
    marginTop: SPACING.xs,
  },
  emptyCard: {
    alignItems: 'center',
    marginHorizontal: SPACING.lg,
    paddingVertical: SPACING.xxl,
  },
  emptyText: {
    fontSize: FONTS.sizes.lg,
    color: COLORS.gray400,
    marginTop: SPACING.md,
    textAlign: 'center',
  },
  exploreButton: {
    marginTop: SPACING.lg,
    paddingVertical: SPACING.sm,
    paddingHorizontal: SPACING.lg,
    backgroundColor: COLORS.primary,
    borderRadius: RADIUS.lg,
  },
  exploreButtonText: {
    fontSize: FONTS.sizes.md,
    fontWeight: '600',
    color: COLORS.white,
  },
});

import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { COLORS, FONTS, SPACING, RADIUS, SHADOWS } from '../../constants/theme';
import { Button, Card, AlertModal } from '../../components';
import { useCourseStore } from '../../store/courseStore';
import { useWalletStore } from '../../store/walletStore';
import { SafeAreaProvider, SafeAreaView } from 'react-native-safe-area-context';

interface PackDetailsScreenProps {
  navigation: any;
  route: any;
}

export const PackDetailsScreen: React.FC<PackDetailsScreenProps> = ({ navigation, route }) => {
  const { packId } = route.params;
  const { currentPack, isLoading, fetchPackDetails, purchasePack } = useCourseStore();
  const { balance, fetchBalance } = useWalletStore();
  const [purchasing, setPurchasing] = useState(false);

  const [alert, setAlert] = useState<{
    visible: boolean;
    type: 'success' | 'error';
    title: string;
    message: string;
  }>({ visible: false, type: 'error', title: '', message: '' });

  useEffect(() => {
    fetchPackDetails(packId);
    fetchBalance();
  }, [packId]);

  const handlePurchase = async () => {
    if (balance < (currentPack?.prix_points || 0)) {
      setAlert({
        visible: true,
        type: 'error',
        title: 'Solde insuffisant',
        message: `Vous avez ${balance} points. Ce pack coûte ${currentPack?.prix_points} points.`,
      });
      return;
    }

    setPurchasing(true);
    try {
      await purchasePack(packId);
      await fetchBalance();
      setAlert({
        visible: true,
        type: 'success',
        title: 'Achat réussi !',
        message: 'Vous avez maintenant un accès illimité à cette formation.',
      });
    } catch (error: any) {
      setAlert({
        visible: true,
        type: 'error',
        title: 'Erreur',
        message: error.response?.data?.message || "Erreur lors de l'achat.",
      });
    } finally {
      setPurchasing(false);
    }
  };

  if (isLoading || !currentPack) {
    return (
      <View style={styles.container}>
        <LinearGradient
          colors={[COLORS.primary, COLORS.primaryLight, COLORS.secondary]}
          style={styles.header}
        >
          <TouchableOpacity
            style={styles.backButton}
            onPress={() => navigation.goBack()}
          >
            <Ionicons name="arrow-back" size={24} color={COLORS.white} />
          </TouchableOpacity>
        </LinearGradient>
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color={COLORS.primary} />
        </View>
      </View>
    );
  }

  return (
    <SafeAreaProvider>
        <SafeAreaView style={styles.container}>
      <LinearGradient
        colors={[COLORS.primary, COLORS.primaryLight, COLORS.secondary]}
        style={styles.header}
      >
        <TouchableOpacity
          style={styles.backButton}
          onPress={() => navigation.goBack()}
        >
          <Ionicons name="arrow-back" size={24} color={COLORS.white} />
        </TouchableOpacity>
        <View style={styles.headerContent}>
          <View style={styles.badge}>
            <Text style={styles.badgeText}>{currentPack.niveau_requis}</Text>
          </View>
          <Text style={styles.title}>{currentPack.nom}</Text>
        </View>
      </LinearGradient>

      <ScrollView style={styles.content} showsVerticalScrollIndicator={false}>
        {/* Price Card */}
        <Card style={styles.priceCard}>
          <View style={styles.priceHeader}>
            <View>
              <Text style={styles.priceLabel}>Accès illimité</Text>
              <Text style={styles.priceValue}>{currentPack.prix_points} points</Text>
            </View>
            <View style={styles.unlimitedBadge}>
              <Ionicons name="infinite" size={24} color={COLORS.secondary} />
              <Text style={styles.unlimitedText}>∞</Text>
            </View>
          </View>
          <View style={styles.balanceInfo}>
            <Ionicons name="wallet-outline" size={18} color={COLORS.gray500} />
            <Text style={styles.balanceText}>
              Votre solde : <Text style={styles.balanceValue}>{balance} pts</Text>
            </Text>
          </View>
        </Card>

        {/* Description */}
        {currentPack.description && (
          <Card style={styles.section}>
            <Text style={styles.sectionTitle}>Description</Text>
            <Text style={styles.description}>{currentPack.description}</Text>
          </Card>
        )}

        {/* Features */}
        <Card style={styles.section}>
          <Text style={styles.sectionTitle}>Ce que vous obtenez</Text>
          <View style={styles.featureItem}>
            <Ionicons name="checkmark-circle" size={24} color={COLORS.success} />
            <Text style={styles.featureText}>Accès illimité à tous les contenus</Text>
          </View>
          <View style={styles.featureItem}>
            <Ionicons name="checkmark-circle" size={24} color={COLORS.success} />
            <Text style={styles.featureText}>Aucune limite de temps</Text>
          </View>
          <View style={styles.featureItem}>
            <Ionicons name="checkmark-circle" size={24} color={COLORS.success} />
            <Text style={styles.featureText}>Certificat à la fin de la formation</Text>
          </View>
          <View style={styles.featureItem}>
            <Ionicons name="checkmark-circle" size={24} color={COLORS.success} />
            <Text style={styles.featureText}>Support et suivi personnalisé</Text>
          </View>
        </Card>

        {/* Diplomas */}
        {currentPack.diplomes_possibles && 
         Array.isArray(currentPack.diplomes_possibles) && 
         currentPack.diplomes_possibles.length > 0 && (
          <Card style={styles.section}>
            <Text style={styles.sectionTitle}>Diplômes possibles</Text>
            <View style={styles.diplomesContainer}>
              {currentPack.diplomes_possibles.map((diplome, index) => (
                <View key={index} style={styles.diplomeItem}>
                  <Ionicons name="ribbon" size={18} color={COLORS.secondary} />
                  <Text style={styles.diplomeText}>{diplome}</Text>
                </View>
              ))}
            </View>
          </Card>
        )}

        {/* Debouchés */}
        {currentPack.debouches && 
         Array.isArray(currentPack.debouches) && 
         currentPack.debouches.length > 0 && (
          <Card style={styles.section}>
            <Text style={styles.sectionTitle}>Débouchés professionnels</Text>
            {currentPack.debouches.map((debouche: string, index: number) => (
              <View key={index} style={styles.deboucheItem}>
                <Ionicons name="briefcase" size={18} color={COLORS.primary} />
                <Text style={styles.deboucheText}>{debouche}</Text>
              </View>
            ))}
          </Card>
        )}

        {/* Stats */}
        <View style={styles.statsContainer}>
          <View style={styles.statItem}>
            <Ionicons name="book" size={24} color={COLORS.primary} />
            <Text style={styles.statValue}>
              {(currentPack as any).total_modules || 0}
            </Text>
            <Text style={styles.statLabel}>Modules</Text>
          </View>
          <View style={styles.statItem}>
            <Ionicons name="document-text" size={24} color={COLORS.secondary} />
            <Text style={styles.statValue}>
              {(currentPack as any).total_chapters || 0}
            </Text>
            <Text style={styles.statLabel}>Chapitres</Text>
          </View>
        </View>

        {/* Purchase Button */}
        <View style={styles.actionContainer}>
          <Button
            title={`Acheter pour ${currentPack.prix_points} pts`}
            onPress={handlePurchase}
            loading={purchasing}
            gradient
            disabled={balance < currentPack.prix_points}
            style={styles.purchaseButton}
          />
          {balance < currentPack.prix_points && (
            <TouchableOpacity
              style={styles.rechargeLink}
              onPress={() => navigation.navigate('Deposit')}
            >
              <Ionicons name="add-circle" size={18} color={COLORS.primary} />
              <Text style={styles.rechargeLinkText}>Recharger mon compte</Text>
            </TouchableOpacity>
          )}
        </View>
      </ScrollView>

      <AlertModal
  visible={alert.visible}
  type={alert.type}
  title={alert.title}
  message={alert.message}
  onClose={() => {
    setAlert({ ...alert, visible: false });
    if (alert.type === 'success') {
      // Navigation corrigée vers l'écran MyPacks dans les tabs
      navigation.navigate('MainTabs', { screen: 'MyPacks' });
    }
  }}
/>
    </SafeAreaView>
    </SafeAreaProvider>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  header: {
    paddingTop: 50,
    paddingBottom: SPACING.xxl,
    paddingHorizontal: SPACING.lg,
    borderBottomLeftRadius: RADIUS.xxl,
    borderBottomRightRadius: RADIUS.xxl,
  },
  backButton: {
    marginBottom: SPACING.lg,
  },
  headerContent: {
    alignItems: 'center',
  },
  badge: {
    backgroundColor: 'rgba(255,255,255,0.2)',
    paddingHorizontal: SPACING.md,
    paddingVertical: SPACING.xs,
    borderRadius: RADIUS.full,
    marginBottom: SPACING.sm,
  },
  badgeText: {
    fontSize: FONTS.sizes.sm,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  title: {
    fontSize: FONTS.sizes.xxl,
    fontWeight: 'bold',
    color: COLORS.white,
    textAlign: 'center',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  content: {
    flex: 1,
    padding: SPACING.lg,
    marginTop: -SPACING.xl,
  },
  priceCard: {
    marginBottom: SPACING.lg,
  },
  priceHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.md,
  },
  priceLabel: {
    fontSize: FONTS.sizes.md,
    color: COLORS.gray500,
    marginBottom: SPACING.xs,
  },
  priceValue: {
    fontSize: FONTS.sizes.xxl,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  unlimitedBadge: {
    alignItems: 'center',
    backgroundColor: COLORS.secondaryLight,
    padding: SPACING.sm,
    borderRadius: RADIUS.lg,
  },
  unlimitedText: {
    fontSize: FONTS.sizes.xs,
    fontWeight: 'bold',
    color: COLORS.secondary,
    marginTop: -SPACING.xs,
  },
  balanceInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingTop: SPACING.md,
    borderTopWidth: 1,
    borderTopColor: COLORS.gray100,
    gap: SPACING.sm,
  },
  balanceText: {
    fontSize: FONTS.sizes.md,
    color: COLORS.gray500,
  },
  balanceValue: {
    fontWeight: 'bold',
    color: COLORS.black,
  },
  section: {
    marginBottom: SPACING.lg,
  },
  sectionTitle: {
    fontSize: FONTS.sizes.lg,
    fontWeight: 'bold',
    color: COLORS.black,
    marginBottom: SPACING.md,
  },
  description: {
    fontSize: FONTS.sizes.md,
    color: COLORS.gray600,
    lineHeight: 22,
  },
  featureItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: SPACING.sm,
    marginBottom: SPACING.md,
  },
  featureText: {
    flex: 1,
    fontSize: FONTS.sizes.md,
    color: COLORS.gray700,
    fontWeight: '500',
  },
  diplomesContainer: {
    gap: SPACING.sm,
  },
  diplomeItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: SPACING.sm,
  },
  diplomeText: {
    fontSize: FONTS.sizes.md,
    color: COLORS.gray600,
  },
  deboucheItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: SPACING.sm,
    marginBottom: SPACING.sm,
  },
  deboucheText: {
    flex: 1,
    fontSize: FONTS.sizes.md,
    color: COLORS.gray600,
  },
  statsContainer: {
    flexDirection: 'row',
    gap: SPACING.md,
    marginBottom: SPACING.xl,
  },
  statItem: {
    flex: 1,
    backgroundColor: COLORS.white,
    borderRadius: RADIUS.xl,
    padding: SPACING.lg,
    alignItems: 'center',
    ...SHADOWS.small,
  },
  statValue: {
    fontSize: FONTS.sizes.xxl,
    fontWeight: 'bold',
    color: COLORS.black,
    marginTop: SPACING.sm,
  },
  statLabel: {
    fontSize: FONTS.sizes.sm,
    color: COLORS.gray500,
  },
  actionContainer: {
    marginBottom: SPACING.xxl,
  },
  purchaseButton: {
    marginBottom: SPACING.md,
  },
  rechargeLink: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: SPACING.xs,
  },
  rechargeLinkText: {
    fontSize: FONTS.sizes.md,
    color: COLORS.primary,
    fontWeight: '600',
  },
});

// screens/main/DashboardScreen.tsx
import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  Linking,
  Share,
  Alert,
  Animated,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import * as Clipboard from 'expo-clipboard';
import { COLORS, FONTS, SPACING, RADIUS, SHADOWS } from '../../constants/theme';
import { BalanceCard, StatCard, Card, PackCard } from '../../components';
import { useAuthStore } from '../../store/authStore';
import { useWalletStore } from '../../store/walletStore';
import { useCourseStore } from '../../store/courseStore';
import { useReferralStore } from '../../store/referralStore';
import { useTrialStore } from '../../store/trialStore';
import { TrialBanner } from '../../components/TrialBanner';
import { ActivateAccountModal } from '../../components/ActivateAccountModal';
import { SafeAreaProvider, SafeAreaView } from 'react-native-safe-area-context';

interface DashboardScreenProps {
  navigation: any;
  onActivatePress?: () => void; // fourni par TabNavigator (navigation/index.tsx)
}

const MOTIVATIONAL_QUOTES = [
  { text: "Un voyage de mille lieues commence toujours par un premier pas", author: "Lao Tseu" },
  { text: "L'excellence n'est pas une destination, c'est un voyage continu", author: "Brian Tracy" },
  { text: "Le succès, c'est tomber sept fois et se relever huit", author: "Proverbe japonais" },
  { text: "La seule limite à notre épanouissement sera notre doute", author: "Franklin D. Roosevelt" },
  { text: "N'ayez pas peur d'échouer, ayez peur de ne pas essayer", author: "Roy T. Bennett" },
  { text: "L'échec est le fondement de la réussite", author: "Lao Tseu" },
  { text: "Le génie, c'est 1% d'inspiration et 99% de transpiration", author: "Thomas Edison" },
  { text: "Visez la lune. Même si vous la manquez, vous atterrirez parmi les étoiles", author: "Les Brown" },
  { text: "Le succès n'est pas final, l'échec n'est pas fatal : c'est le courage de continuer qui compte", author: "Winston Churchill" },
  { text: "Crois en toi-même et tout devient possible", author: "Proverbe" },
  { text: "La persévérance est la clé de toute réussite", author: "Charlie Chaplin" },
  { text: "Votre temps est limité, ne le gaspillez pas à vivre la vie de quelqu'un d'autre", author: "Steve Jobs" },
  { text: "L'éducation est l'arme la plus puissante pour changer le monde", author: "Nelson Mandela" },
  { text: "Ce n'est pas parce que les choses sont difficiles que nous n'osons pas, c'est parce que nous n'osons pas qu'elles sont difficiles", author: "Sénèque" },
  { text: "La discipline est le pont entre les objectifs et l'accomplissement", author: "Jim Rohn" },
  { text: "Chaque expert a d'abord été un débutant", author: "Proverbe" },
  { text: "L'avenir appartient à ceux qui croient en la beauté de leurs rêves", author: "Eleanor Roosevelt" },
  { text: "Ne limite pas tes défis, défie tes limites", author: "Jerry Dunn" },
  { text: "L'échec est simplement l'opportunité de recommencer de manière plus intelligente", author: "Henry Ford" },
  { text: "La connaissance s'acquiert par l'expérience, tout le reste n'est que de l'information", author: "Albert Einstein" },
];

export const DashboardScreen: React.FC<DashboardScreenProps> = ({ navigation, onActivatePress }) => {
  const { user, refreshProfile } = useAuthStore();
  const { balance, equivalentFcfa, fetchBalance } = useWalletStore();
  const { myPacks, recommendedPacks, fetchMyPacks, fetchRecommendedPacks } = useCourseStore();
  const { totalFilleuls, fetchStats, myCode, shareMessage, fetchMyCode } = useReferralStore();

  // ── Trial ─────────────────────────────────────────────────
  const [showActivateModal, setShowActivateModal] = useState(false);

  // ── UI state ──────────────────────────────────────────────
  const [refreshing, setRefreshing] = useState(false);
  const [currentQuote, setCurrentQuote] = useState(MOTIVATIONAL_QUOTES[0]);

  // Animation bouton profil
  const [pulseAnim] = useState(new Animated.Value(1));
  const [glowAnim] = useState(new Animated.Value(0));

  // ─────────────────────────────────────────────────────────
  useEffect(() => {
    loadData();

    // Rotation des citations toutes les 10s
    const quoteInterval = setInterval(() => {
      setCurrentQuote(MOTIVATIONAL_QUOTES[Math.floor(Math.random() * MOTIVATIONAL_QUOTES.length)]);
    }, 10000);

    // Animation pulse du bouton profil pendant 10s
    const pulseAnimation = Animated.loop(
      Animated.sequence([
        Animated.timing(pulseAnim, { toValue: 1.15, duration: 800, useNativeDriver: true }),
        Animated.timing(pulseAnim, { toValue: 1, duration: 800, useNativeDriver: true }),
      ])
    );
    const glowAnimation = Animated.loop(
      Animated.sequence([
        Animated.timing(glowAnim, { toValue: 1, duration: 1000, useNativeDriver: true }),
        Animated.timing(glowAnim, { toValue: 0, duration: 1000, useNativeDriver: true }),
      ])
    );

    pulseAnimation.start();
    glowAnimation.start();

    const animationTimeout = setTimeout(() => {
      pulseAnimation.stop();
      glowAnimation.stop();
      Animated.timing(pulseAnim, { toValue: 1, duration: 300, useNativeDriver: true }).start();
      Animated.timing(glowAnim, { toValue: 0, duration: 300, useNativeDriver: true }).start();
    }, 10000);

    return () => {
      clearInterval(quoteInterval);
      clearTimeout(animationTimeout);
      pulseAnimation.stop();
      glowAnimation.stop();
    };
  }, []);

  // ─────────────────────────────────────────────────────────
  const { accountActivated, trialExpired, syncWithServer } = useTrialStore();

const loadData = async () => {
  try {
    await Promise.all([
      fetchBalance(),
      fetchMyPacks(),
      fetchRecommendedPacks(),
      fetchStats(),
      fetchMyCode(),
      refreshProfile(),
      syncWithServer(), // ✅ toujours vérifier le trial
    ]);
  } catch (error) {
    console.error('Error loading dashboard data:', error);
  }
};

const onRefresh = async () => {
  setRefreshing(true);
  await loadData(); // syncWithServer est déjà dedans
  setRefreshing(false);
};

  // ── Handlers ──────────────────────────────────────────────
  const openWhatsAppDiploma = () => {
    const url = 'https://wa.me/237659292001?text=Bonjour,%20je%20veux%20avoir%20un%20diplôme%20reconnu%20par%20le%20MINEFOP%20depuis%20Elite2.0';
    Linking.openURL(url).catch(() => Alert.alert('Erreur', "Impossible d'ouvrir WhatsApp"));
  };

  const openWhatsAppScholarship = () => {
    const url = 'https://wa.me/237659292001?text=Bonjour,%20je%20souhaite%20obtenir%20une%20bourse%20de%20formation%20depuis%20Elite2.0';
    Linking.openURL(url).catch(() => Alert.alert('Erreur', "Impossible d'ouvrir WhatsApp"));
  };

  const handleCopyReferralCode = async () => {
    const code = myCode || user?.referral_code || '';
    await Clipboard.setStringAsync(code);
    Alert.alert('Copié !', 'Code de parrainage copié dans le presse-papiers');
  };

  const handleShareReferralCode = async () => {
    try {
      const code = myCode || user?.referral_code || '';
      const message = shareMessage || `Rejoignez-moi sur l'app avec mon code : ${code}`;
      await Share.share({ message });
    } catch (error) {
      console.log('Error sharing:', error);
    }
  };

  /**
   * Ouvre le modal d'activation.
   * Utilise onActivatePress (fourni par TabNavigator) en priorité,
   * sinon ouvre le modal local.
   */
  const handleOpenActivate = () => {
    if (onActivatePress) {
      onActivatePress();
    } else {
      setShowActivateModal(true);
    }
  };

  /**
   * Navigation vers Transfer uniquement si compte activé,
   * sinon ouvre le modal d'activation.
   */
  const handleTransferPress = () => {
    if (accountActivated) {
      navigation.navigate('Transfer');
    } else {
      handleOpenActivate();
    }
  };

  // ─────────────────────────────────────────────────────────
  const safeBalance = balance ?? 0;
  const safeEquivalentFcfa = equivalentFcfa ?? 0;

  const glowOpacity = glowAnim.interpolate({
    inputRange: [0, 1],
    outputRange: [0, 0.6],
  });

  return (
    <SafeAreaProvider>
      <SafeAreaView style={styles.container}>
        <ScrollView
          style={styles.scrollView}
          showsVerticalScrollIndicator={false}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        >
          {/* ── Header ─────────────────────────────────────── */}
          <LinearGradient
            colors={[COLORS.primary, COLORS.primaryLight]}
            style={styles.header}
          >
            <View style={styles.headerTop}>
              <View style={styles.greetingContainer}>
                <Text style={styles.greeting}>Bienvenue,</Text>
                <Text style={styles.userName}>{user?.prenom} {user?.nom}</Text>
              </View>
              <TouchableOpacity
                style={styles.profileButton}
                onPress={() => navigation.navigate('Profile')}
                activeOpacity={0.7}
              >
                <Animated.View style={{ transform: [{ scale: pulseAnim }] }}>
                  <Animated.View style={[styles.profileGlow, { opacity: glowOpacity }]} />
                  <Ionicons name="person-circle" size={40} color={COLORS.white} />
                </Animated.View>
              </TouchableOpacity>
            </View>

            {/* Citation motivante */}
            <View style={styles.quoteContainer}>
              <Ionicons
                name="chatbox-outline"
                size={16}
                color="rgba(255,255,255,0.6)"
                style={styles.quoteIcon}
              />
              <View style={styles.quoteContent}>
                <Text style={styles.quoteText}>"{currentQuote.text}"</Text>
                <Text style={styles.quoteAuthor}>— {currentQuote.author}</Text>
              </View>
            </View>
          </LinearGradient>

          {/* ── Contenu principal ──────────────────────────── */}
          <View style={styles.content}>

            {/* TrialBanner — affiché si compte non activé */}
            {!accountActivated && (
              <TrialBanner onActivatePress={handleOpenActivate} />
            )}

            {/* Balance Card */}
            <View style={styles.balanceContainer}>
              <BalanceCard
                balance={safeBalance}
                equivalentFcfa={safeEquivalentFcfa}
                onDeposit={() => navigation.navigate('Deposit')}
                onTransfer={handleTransferPress}
                // Si votre BalanceCard supporte ces props optionnelles :
                // transferLabel={accountActivated ? 'Transférer' : 'Activer'}
                // transferIcon={accountActivated ? 'swap-horizontal' : 'rocket'}
              />

              {/* Bouton Activer visible sous la BalanceCard si trial expiré */}
              {!accountActivated && trialExpired && (
                <TouchableOpacity
                  style={styles.activateInlineBtn}
                  onPress={handleOpenActivate}
                  activeOpacity={0.85}
                >
                  <LinearGradient
                    colors={['#E53935', '#C62828']}
                    style={styles.activateInlineBtnGradient}
                    start={{ x: 0, y: 0 }}
                    end={{ x: 1, y: 0 }}
                  >
                    <Ionicons name="rocket-outline" size={20} color={COLORS.white} />
                    <Text style={styles.activateInlineBtnText}>
                      Activer mon compte — 1 000 FCFA
                    </Text>
                    <Ionicons name="chevron-forward" size={18} color="rgba(255,255,255,0.7)" />
                  </LinearGradient>
                </TouchableOpacity>
              )}
            </View>

            {/* Quick Stats */}
            <View style={styles.statsContainer}>
              <StatCard
                title="Mes Formations"
                value={myPacks.length}
                icon="book"
                color={COLORS.primary}
                onPress={() => navigation.navigate('MyPacks')}
              />
              <StatCard
                title="Filleuls"
                value={totalFilleuls ?? 0}
                icon="people"
                color={COLORS.secondary}
                onPress={() => navigation.navigate('Referral')}
              />
            </View>

            {/* Scholarship Section */}
            <Card style={styles.scholarshipCard}>
              <LinearGradient
                colors={['#FF6B35', '#F7931E']}
                start={{ x: 0, y: 0 }}
                end={{ x: 1, y: 1 }}
                style={styles.scholarshipGradient}
              >
                <View style={styles.scholarshipIcon}>
                  <Ionicons name="school" size={32} color={COLORS.white} />
                </View>
                <View style={styles.scholarshipContent}>
                  <Text style={styles.scholarshipTitle}>Obtenir une bourse</Text>
                  <Text style={styles.scholarshipText}>
                    Bénéficiez d'une réduction considérable sur les frais de formations
                  </Text>
                  <TouchableOpacity style={styles.scholarshipButton} onPress={openWhatsAppScholarship}>
                    <Ionicons name="logo-whatsapp" size={20} color="#FF6B35" />
                    <Text style={styles.scholarshipButtonText}>Demander une bourse</Text>
                  </TouchableOpacity>
                </View>
              </LinearGradient>
            </Card>

            {/* Referral Code */}
            <Card style={styles.referralCard}>
              <View style={styles.referralHeader}>
                <Ionicons name="share-social" size={24} color={COLORS.primary} />
                <Text style={styles.referralTitle}>Mon code d'invitation</Text>
              </View>
              <View style={styles.referralCodeContainer}>
                <Text style={styles.referralCode}>{myCode || user?.referral_code}</Text>
                <TouchableOpacity style={styles.shareButton} onPress={handleCopyReferralCode}>
                  <Ionicons name="copy-outline" size={20} color={COLORS.primary} />
                </TouchableOpacity>
              </View>
              <Text style={styles.referralHint}>Partagez ce code et gagnez des points !</Text>
              <TouchableOpacity style={styles.shareFullButton} onPress={handleShareReferralCode}>
                <Ionicons name="share-outline" size={18} color={COLORS.white} />
                <Text style={styles.shareFullButtonText}>Partager</Text>
              </TouchableOpacity>
            </Card>

            {/* Diploma Section */}
            <Card style={styles.diplomaCard}>
              <LinearGradient
                colors={[COLORS.secondary, COLORS.secondaryLight]}
                start={{ x: 0, y: 0 }}
                end={{ x: 1, y: 0 }}
                style={styles.diplomaGradient}
              >
                <View style={styles.diplomaIcon}>
                  <Ionicons name="ribbon" size={32} color={COLORS.white} />
                </View>
                <View style={styles.diplomaContent}>
                  <Text style={styles.diplomaTitle}>Diplôme reconnu par le MINEFOP</Text>
                  <Text style={styles.diplomaText}>
                    Nos formations vous préparent à obtenir des diplômes professionnels reconnus nationalement.
                  </Text>
                  <TouchableOpacity style={styles.diplomaButton} onPress={openWhatsAppDiploma}>
                    <Ionicons name="logo-whatsapp" size={20} color={COLORS.secondary} />
                    <Text style={styles.diplomaButtonText}>Je veux un diplôme</Text>
                  </TouchableOpacity>
                </View>
              </LinearGradient>
            </Card>

            {/* My Current Packs */}
            {myPacks.length > 0 && (
              <View style={styles.section}>
                <View style={styles.sectionHeader}>
                  <Text style={styles.sectionTitle}>Mes formations en cours</Text>
                  <TouchableOpacity onPress={() => navigation.navigate('MyPacks')}>
                    <Text style={styles.seeAll}>Voir tout</Text>
                  </TouchableOpacity>
                </View>
                {myPacks.slice(0, 2).map((userPack) =>
                  userPack.pack ? (
                    <PackCard
                      key={userPack.id}
                      pack={userPack.pack}
                      isPurchased
                      progression={userPack.progression}
                      onPress={() => navigation.navigate('PackModules', { packId: userPack.pack?.id })}
                    />
                  ) : null
                )}
              </View>
            )}

            {/* Recommended Packs */}
            {recommendedPacks.length > 0 && (
              <View style={styles.section}>
                <View style={styles.sectionHeader}>
                  <Text style={styles.sectionTitle}>Formations recommandées</Text>
                  <TouchableOpacity onPress={() => navigation.navigate('Packs')}>
                    <Text style={styles.seeAll}>Voir tout</Text>
                  </TouchableOpacity>
                </View>
                {recommendedPacks.slice(0, 2).map((pack) => (
                  <PackCard
                    key={pack.id}
                    pack={pack}
                    onPress={() => navigation.navigate('PackDetails', { packId: pack.id })}
                  />
                ))}
              </View>
            )}

            {/* Hub Communautaire */}
            <View style={styles.section}>
              <Text style={styles.sectionTitle}>Hub Communautaire</Text>
              <View style={styles.quickActions}>
                <TouchableOpacity
                  style={styles.quickAction}
                  onPress={() => navigation.navigate('Jobs')}
                >
                  <View style={[styles.quickActionIcon, { backgroundColor: `${COLORS.info}15` }]}>
                    <Ionicons name="briefcase" size={24} color={COLORS.info} />
                  </View>
                  <Text style={styles.quickActionText}>Emplois&Concours</Text>
                </TouchableOpacity>

                <TouchableOpacity
  style={styles.quickAction}
  onPress={() => navigation.navigate('Contests')}
>
  <View style={[styles.quickActionIcon, { backgroundColor: `${COLORS.warning}15` }]}>
    <Ionicons name="cash-outline" size={24} color={COLORS.warning} />
  </View>
  <Text style={styles.quickActionText}>Financement</Text>
</TouchableOpacity>
                <TouchableOpacity
                  style={styles.quickAction}
                  onPress={() => navigation.navigate('FAQ')}
                >
                  <View style={[styles.quickActionIcon, { backgroundColor: `${COLORS.secondary}15` }]}>
                    <Ionicons name="help-circle" size={24} color={COLORS.secondary} />
                  </View>
                  <Text style={styles.quickActionText}>FAQ</Text>
                </TouchableOpacity>

                <TouchableOpacity
                  style={styles.quickAction}
                  onPress={() => navigation.navigate('Library')}
                >
                  <View style={[styles.quickActionIcon, { backgroundColor: `${COLORS.primary}15` }]}>
                    <Ionicons name="chatbubbles" size={24} color={COLORS.primary} />
                  </View>
                  <Text style={styles.quickActionText}>Forum & Chat</Text>
                </TouchableOpacity>
              </View>
            </View>

            {/* Powered By */}
            <TouchableOpacity
              style={styles.poweredBy}
              onPress={() => Linking.openURL('https://techforgesolution237.site')}
            >
              <Text style={styles.poweredByText}>Powered by </Text>
              <Text style={styles.poweredByBrand}>TFS237</Text>
            </TouchableOpacity>
          </View>
        </ScrollView>

        {/* Modal d'activation local (fallback si onActivatePress non fourni) */}
        {!onActivatePress && (
          <ActivateAccountModal
            visible={showActivateModal}
            dismissable={!trialExpired}
            onClose={() => setShowActivateModal(false)}
            onActivated={async () => {
              setShowActivateModal(false);
              await loadData();
            }}
            navigation={navigation}
          />
        )}
      </SafeAreaView>
    </SafeAreaProvider>
  );
};

// ─────────────────────────────────────────────────────────────
// STYLES
// ─────────────────────────────────────────────────────────────
const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  scrollView: {
    flex: 1,
  },

  // ── Header ────────────────────────────────────────────────
  header: {
    paddingTop: SPACING.lg,
    paddingBottom: SPACING.xl,
    paddingHorizontal: SPACING.lg,
  },
  headerTop: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.md,
  },
  greetingContainer: {
    flex: 1,
  },
  greeting: {
    fontSize: FONTS.sizes.md,
    color: 'rgba(255,255,255,0.8)',
  },
  userName: {
    fontSize: FONTS.sizes.xxl,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  profileButton: {
    marginLeft: SPACING.md,
    position: 'relative',
  },
  profileGlow: {
    position: 'absolute',
    top: -8,
    left: -8,
    right: -8,
    bottom: -8,
    borderRadius: 30,
    backgroundColor: COLORS.white,
  },
  quoteContainer: {
    flexDirection: 'row',
    backgroundColor: 'rgba(255,255,255,0.1)',
    borderRadius: RADIUS.lg,
    padding: SPACING.md,
    marginTop: SPACING.sm,
    borderLeftWidth: 3,
    borderLeftColor: 'rgba(255,255,255,0.3)',
  },
  quoteIcon: {
    marginRight: SPACING.xs,
    marginTop: 2,
  },
  quoteContent: {
    flex: 1,
  },
  quoteText: {
    fontSize: FONTS.sizes.sm,
    color: COLORS.white,
    fontStyle: 'italic',
    lineHeight: 18,
    marginBottom: SPACING.xs,
  },
  quoteAuthor: {
    fontSize: FONTS.sizes.xs,
    color: 'rgba(255,255,255,0.7)',
    fontWeight: '600',
  },

  // ── Content ───────────────────────────────────────────────
  content: {
    paddingTop: SPACING.md,
  },

  // ── Balance ───────────────────────────────────────────────
  balanceContainer: {
    marginBottom: SPACING.xl,
  },
  activateInlineBtn: {
    marginHorizontal: SPACING.lg,
    marginTop: SPACING.md,
    borderRadius: RADIUS.lg,
    overflow: 'hidden',
  },
  activateInlineBtnGradient: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: SPACING.md,
    paddingHorizontal: SPACING.lg,
    gap: SPACING.sm,
  },
  activateInlineBtnText: {
    flex: 1,
    textAlign: 'center',
    color: COLORS.white,
    fontSize: FONTS.sizes.md,
    fontWeight: 'bold',
  },

  // ── Stats ─────────────────────────────────────────────────
  statsContainer: {
    flexDirection: 'row',
    gap: SPACING.md,
    paddingHorizontal: SPACING.lg,
    marginBottom: SPACING.xl,
  },

  // ── Scholarship ───────────────────────────────────────────
  scholarshipCard: {
    marginHorizontal: SPACING.lg,
    marginBottom: SPACING.xl,
    padding: 0,
    overflow: 'hidden',
  },
  scholarshipGradient: {
    flexDirection: 'row',
    padding: SPACING.lg,
  },
  scholarshipIcon: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: 'rgba(255,255,255,0.2)',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: SPACING.md,
  },
  scholarshipContent: {
    flex: 1,
  },
  scholarshipTitle: {
    fontSize: FONTS.sizes.lg,
    fontWeight: 'bold',
    color: COLORS.white,
    marginBottom: SPACING.xs,
  },
  scholarshipText: {
    fontSize: FONTS.sizes.sm,
    color: 'rgba(255,255,255,0.9)',
    lineHeight: 18,
    marginBottom: SPACING.md,
  },
  scholarshipButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.white,
    paddingVertical: SPACING.sm,
    paddingHorizontal: SPACING.md,
    borderRadius: RADIUS.full,
    alignSelf: 'flex-start',
  },
  scholarshipButtonText: {
    fontSize: FONTS.sizes.sm,
    fontWeight: '600',
    color: '#FF6B35',
    marginLeft: SPACING.xs,
  },

  // ── Referral ──────────────────────────────────────────────
  referralCard: {
    marginHorizontal: SPACING.lg,
    marginBottom: SPACING.xl,
  },
  referralHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: SPACING.md,
  },
  referralTitle: {
    fontSize: FONTS.sizes.lg,
    fontWeight: '600',
    color: COLORS.black,
    marginLeft: SPACING.sm,
  },
  referralCodeContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.gray100,
    borderRadius: RADIUS.lg,
    padding: SPACING.md,
  },
  referralCode: {
    flex: 1,
    fontSize: FONTS.sizes.xxl,
    fontWeight: 'bold',
    color: COLORS.primary,
    letterSpacing: 2,
  },
  shareButton: {
    padding: SPACING.sm,
  },
  referralHint: {
    fontSize: FONTS.sizes.sm,
    color: COLORS.gray500,
    marginTop: SPACING.sm,
    marginBottom: SPACING.md,
  },
  shareFullButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: COLORS.primary,
    paddingVertical: SPACING.sm,
    paddingHorizontal: SPACING.md,
    borderRadius: RADIUS.lg,
    gap: SPACING.xs,
  },
  shareFullButtonText: {
    fontSize: FONTS.sizes.md,
    fontWeight: '600',
    color: COLORS.white,
  },

  // ── Diploma ───────────────────────────────────────────────
  diplomaCard: {
    marginHorizontal: SPACING.lg,
    marginBottom: SPACING.xl,
    padding: 0,
    overflow: 'hidden',
  },
  diplomaGradient: {
    flexDirection: 'row',
    padding: SPACING.lg,
  },
  diplomaIcon: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: 'rgba(255,255,255,0.2)',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: SPACING.md,
  },
  diplomaContent: {
    flex: 1,
  },
  diplomaTitle: {
    fontSize: FONTS.sizes.lg,
    fontWeight: 'bold',
    color: COLORS.white,
    marginBottom: SPACING.xs,
  },
  diplomaText: {
    fontSize: FONTS.sizes.sm,
    color: 'rgba(255,255,255,0.9)',
    lineHeight: 18,
    marginBottom: SPACING.md,
  },
  diplomaButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.white,
    paddingVertical: SPACING.sm,
    paddingHorizontal: SPACING.md,
    borderRadius: RADIUS.full,
    alignSelf: 'flex-start',
  },
  diplomaButtonText: {
    fontSize: FONTS.sizes.sm,
    fontWeight: '600',
    color: COLORS.secondary,
    marginLeft: SPACING.xs,
  },

  // ── Sections & Packs ──────────────────────────────────────
  section: {
    paddingHorizontal: SPACING.lg,
    marginBottom: SPACING.xl,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.md,
  },
  sectionTitle: {
    fontSize: FONTS.sizes.xl,
    fontWeight: 'bold',
    color: COLORS.black,
  },
  seeAll: {
    fontSize: FONTS.sizes.md,
    color: COLORS.primary,
    fontWeight: '600',
  },

  // ── Quick Actions ─────────────────────────────────────────
  quickActions: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: SPACING.md,
    marginTop: SPACING.md,
  },
  quickAction: {
    width: '22%',
    alignItems: 'center',
  },
  quickActionIcon: {
    width: 56,
    height: 56,
    borderRadius: RADIUS.xl,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: SPACING.xs,
  },
  quickActionText: {
    fontSize: FONTS.sizes.sm,
    color: COLORS.gray600,
    textAlign: 'center',
  },

  // ── Footer ────────────────────────────────────────────────
  poweredBy: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: SPACING.xxl,
  },
  poweredByText: {
    fontSize: FONTS.sizes.sm,
    color: COLORS.gray400,
  },
  poweredByBrand: {
    fontSize: FONTS.sizes.sm,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
});

import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Share,
  RefreshControl,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import * as Clipboard from 'expo-clipboard';
import { COLORS, FONTS, SPACING, RADIUS, SHADOWS } from '../../constants/theme';
import { Card, StatCard } from '../../components';
import { useReferralStore } from '../../store/referralStore';
import { SafeAreaProvider, SafeAreaView } from 'react-native-safe-area-context';

interface ReferralScreenProps {
  navigation: any;
}

export const ReferralScreen: React.FC<ReferralScreenProps> = ({ navigation }) => {
  const {
    myCode,
    shareMessage,
    totalFilleuls,
    pointsGagnes,
    filleulsCeMois,
    history,
    parrain,
    fetchMyCode,
    fetchStats,
    fetchHistory,
    fetchMyParrain,
    isLoading,
  } = useReferralStore();
  const [refreshing, setRefreshing] = useState(false);
  const [copied, setCopied] = useState(false);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    await Promise.all([
      fetchMyCode(),
      fetchStats(),
      fetchHistory(),
      fetchMyParrain(),
    ]);
  };

  const onRefresh = async () => {
    setRefreshing(true);
    await loadData();
    setRefreshing(false);
  };

  const handleCopy = async () => {
    await Clipboard.setStringAsync(myCode);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  const handleShare = async () => {
    try {
      await Share.share({
        message: shareMessage,
      });
    } catch (error) {
      console.log('Share error:', error);
    }
  };

  return (
    <SafeAreaProvider>
        <SafeAreaView style={styles.container}>
      <LinearGradient
        colors={[COLORS.secondary, COLORS.secondaryLight]}
        style={styles.header}
      >
        <TouchableOpacity 
          style={styles.backButton}
          onPress={() => navigation.goBack()}
        >
          <Ionicons name="arrow-back" size={24} color={COLORS.white} />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Invitation</Text>
        <View style={{ width: 24 }} />
      </LinearGradient>

      <ScrollView
        style={styles.content}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
      >
        {/* Code Card */}
        <Card style={styles.codeCard}>
          <Text style={styles.codeLabel}>Mon code Invitation</Text>
          <View style={styles.codeContainer}>
            <Text style={styles.code}>{myCode}</Text>
            <TouchableOpacity style={styles.copyButton} onPress={handleCopy}>
              <Ionicons 
                name={copied ? 'checkmark' : 'copy'} 
                size={20} 
                color={copied ? COLORS.success : COLORS.primary} 
              />
            </TouchableOpacity>
          </View>
          <TouchableOpacity style={styles.shareButton} onPress={handleShare}>
            <Ionicons name="share-social" size={20} color={COLORS.white} />
            <Text style={styles.shareText}>Partager mon code</Text>
          </TouchableOpacity>
        </Card>

        {/* Stats */}
        <View style={styles.statsRow}>
          <StatCard
            title="Total filleuls"
            value={totalFilleuls}
            icon="people"
            color={COLORS.primary}
          />
          <StatCard
            title="Points gagnés"
            value={pointsGagnes}
            icon="star"
            color={COLORS.warning}
          />
        </View>
        <View style={styles.statsRow}>
  <StatCard
    title="Ce mois"
    value={filleulsCeMois}
    icon="calendar"
    color={COLORS.secondary}
  />
</View>

        {/* My Parrain */}
        {parrain && (
          <Card style={styles.parrainCard}>
            <Text style={styles.sectionTitle}>Mon Inviteur</Text>
            <View style={styles.parrainInfo}>
              <View style={styles.parrainAvatar}>
                <Text style={styles.parrainInitials}>
                  {parrain.prenom?.charAt(0)}{parrain.nom?.charAt(0)}
                </Text>
              </View>
              <View>
                <Text style={styles.parrainName}>
                  {parrain.prenom} {parrain.nom}
                </Text>
                <Text style={styles.parrainCity}>{parrain.ville}</Text>
              </View>
            </View>
          </Card>
        )}

        {/* History */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Mes Invités</Text>
          {history.length === 0 ? (
            <Card style={styles.emptyCard}>
              <Ionicons name="people-outline" size={48} color={COLORS.gray300} />
              <Text style={styles.emptyText}>Aucun invité pour le moment</Text>
              <Text style={styles.emptyHint}>
                Partagez votre code pour inviter des amis !
              </Text>
            </Card>
          ) : (
            history.map((item, index) => (
              <Card key={index} style={styles.filleulCard}>
                <View style={styles.filleulAvatar}>
                  <Text style={styles.filleulInitials}>
                    {item.filleul.prenom?.charAt(0)}{item.filleul.nom?.charAt(0)}
                  </Text>
                </View>
                <View style={styles.filleulInfo}>
                  <Text style={styles.filleulName}>
                    {item.filleul.prenom} {item.filleul.nom}
                  </Text>
                  <Text style={styles.filleulCity}>{item.filleul.ville}</Text>
                  <Text style={styles.filleulDate}>
                    {new Date(item.date_inscription).toLocaleDateString('fr-FR')}
                  </Text>
                </View>
                <View style={styles.filleulPoints}>
                  <Text style={styles.pointsValue}>+{item.points_gagnes}</Text>
                  <Text style={styles.pointsLabel}>pts</Text>
                </View>
              </Card>
            ))
          )}
        </View>

        {/* Info */}
        <Card style={styles.infoCard}>
          <View style={styles.infoHeader}>
            <Ionicons name="gift" size={24} color={COLORS.secondary} />
            <Text style={styles.infoTitle}>Comment ça marche ?</Text>
          </View>
          <Text style={styles.infoText}>
            1. Partagez votre code d'invitation avec vos amis{'\n'}
            2. Ils s'inscrivent avec votre code{'\n'}
            3. Vous gagnez des points pour chaque invité{'\n'}
            4. Utilisez vos points pour des formations gratuites !
          </Text>
        </Card>
      </ScrollView>
    </SafeAreaView>
    </SafeAreaProvider>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingTop: 50,
    paddingBottom: 20,
    paddingHorizontal: SPACING.lg,
  },
  backButton: {
    padding: SPACING.xs,
  },
  headerTitle: {
    fontSize: FONTS.sizes.xl,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  content: {
    flex: 1,
    padding: SPACING.lg,
  },
  codeCard: {
    alignItems: 'center',
    paddingVertical: SPACING.xl,
    marginBottom: SPACING.lg,
  },
  codeLabel: {
    fontSize: FONTS.sizes.md,
    color: COLORS.gray500,
    marginBottom: SPACING.sm,
  },
  codeContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.gray100,
    borderRadius: RADIUS.xl,
    paddingVertical: SPACING.md,
    paddingHorizontal: SPACING.xl,
    marginBottom: SPACING.lg,
  },
  code: {
    fontSize: FONTS.sizes.xxxl,
    fontWeight: 'bold',
    color: COLORS.primary,
    letterSpacing: 3,
    marginRight: SPACING.md,
  },
  copyButton: {
    padding: SPACING.sm,
  },
  shareButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.secondary,
    paddingVertical: SPACING.md,
    paddingHorizontal: SPACING.xl,
    borderRadius: RADIUS.full,
    gap: SPACING.sm,
  },
  shareText: {
    fontSize: FONTS.sizes.md,
    fontWeight: '600',
    color: COLORS.white,
  },
  statsRow: {
    flexDirection: 'row',
    gap: SPACING.md,
    marginBottom: SPACING.md,
  },
  parrainCard: {
    marginBottom: SPACING.lg,
  },
  sectionTitle: {
    fontSize: FONTS.sizes.lg,
    fontWeight: 'bold',
    color: COLORS.black,
    marginBottom: SPACING.md,
  },
  parrainInfo: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  parrainAvatar: {
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: COLORS.secondary,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: SPACING.md,
  },
  parrainInitials: {
    fontSize: FONTS.sizes.lg,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  parrainName: {
    fontSize: FONTS.sizes.lg,
    fontWeight: '600',
    color: COLORS.black,
  },
  parrainCity: {
    fontSize: FONTS.sizes.sm,
    color: COLORS.gray500,
  },
  section: {
    marginBottom: SPACING.xl,
  },
  emptyCard: {
    alignItems: 'center',
    paddingVertical: SPACING.xxl,
  },
  emptyText: {
    fontSize: FONTS.sizes.lg,
    color: COLORS.gray500,
    marginTop: SPACING.md,
  },
  emptyHint: {
    fontSize: FONTS.sizes.sm,
    color: COLORS.gray400,
    marginTop: SPACING.xs,
  },
  filleulCard: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: SPACING.sm,
  },
  filleulAvatar: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: COLORS.primary,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: SPACING.md,
  },
  filleulInitials: {
    fontSize: FONTS.sizes.md,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  filleulInfo: {
    flex: 1,
  },
  filleulName: {
    fontSize: FONTS.sizes.md,
    fontWeight: '600',
    color: COLORS.black,
  },
  filleulCity: {
    fontSize: FONTS.sizes.sm,
    color: COLORS.gray500,
  },
  filleulDate: {
    fontSize: FONTS.sizes.xs,
    color: COLORS.gray400,
  },
  filleulPoints: {
    alignItems: 'center',
    backgroundColor: `${COLORS.success}15`,
    paddingVertical: SPACING.sm,
    paddingHorizontal: SPACING.md,
    borderRadius: RADIUS.lg,
  },
  pointsValue: {
    fontSize: FONTS.sizes.lg,
    fontWeight: 'bold',
    color: COLORS.success,
  },
  pointsLabel: {
    fontSize: FONTS.sizes.xs,
    color: COLORS.success,
  },
  infoCard: {
    backgroundColor: `${COLORS.secondary}10`,
    borderWidth: 1,
    borderColor: `${COLORS.secondary}30`,
  },
  infoHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: SPACING.md,
    gap: SPACING.sm,
  },
  infoTitle: {
    fontSize: FONTS.sizes.lg,
    fontWeight: 'bold',
    color: COLORS.secondary,
  },
  infoText: {
    fontSize: FONTS.sizes.md,
    color: COLORS.gray600,
    lineHeight: 24,
  },
});


import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { COLORS, FONTS, SPACING, RADIUS, SHADOWS } from '../../constants/theme';
import { Card } from '../../components';
import { useWalletStore } from '../../store/walletStore';
import { SafeAreaProvider, SafeAreaView } from 'react-native-safe-area-context';

interface WalletScreenProps {
  navigation: any;
}

export const WalletScreen: React.FC<WalletScreenProps> = ({ navigation }) => {
  const { balance, equivalentFcfa, transactions, fetchBalance, fetchTransactions, isLoading } = useWalletStore();
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    await Promise.all([fetchBalance(), fetchTransactions()]);
  };

  const onRefresh = async () => {
    setRefreshing(true);
    await loadData();
    setRefreshing(false);
  };

  const getTransactionIcon = (type: string) => {
    switch (type) {
      case 'depot': return 'arrow-down-circle';
      case 'achat_pack': return 'cart';
      case 'transfert_envoye': return 'arrow-up-circle';
      case 'transfert_recu': return 'arrow-down-circle';
      case 'bonus_parrainage': return 'gift';
      case 'code_caisse': return 'ticket';
      default: return 'swap-horizontal';
    }
  };

  const getTransactionColor = (type: string) => {
    if (type.includes('recu') || type === 'depot' || type === 'bonus_parrainage' || type === 'code_caisse') {
      return COLORS.success;
    }
    return COLORS.error;
  };

  return (
    <SafeAreaProvider>
        <SafeAreaView style={styles.container}>
      <LinearGradient
        colors={[COLORS.primary, COLORS.primaryLight]}
        style={styles.header}
      >
        <TouchableOpacity 
          style={styles.backButton}
          onPress={() => navigation.goBack()}
        >
          <Ionicons name="arrow-back" size={24} color={COLORS.white} />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Portefeuille</Text>
        <View style={{ width: 24 }} />
      </LinearGradient>

      <ScrollView
        style={styles.content}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
      >
        {/* Balance Card */}
        <Card style={styles.balanceCard}>
          <Text style={styles.balanceLabel}>Solde disponible</Text>
          <Text style={styles.balanceValue}>{balance.toFixed(2)} pts</Text>
          <Text style={styles.balanceEquivalent}>≈ {equivalentFcfa.toLocaleString()} FCFA</Text>
          
          <View style={styles.actionsRow}>
            <TouchableOpacity
              style={styles.actionButton}
              onPress={() => navigation.navigate('Deposit')}
            >
              <View style={[styles.actionIcon, { backgroundColor: `${COLORS.success}15` }]}>
                <Ionicons name="add-circle" size={24} color={COLORS.success} />
              </View>
              <Text style={styles.actionText}>Recharger</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={styles.actionButton}
              onPress={() => navigation.navigate('CashCode')}
            >
              <View style={[styles.actionIcon, { backgroundColor: `${COLORS.warning}15` }]}>
                <Ionicons name="ticket" size={24} color={COLORS.warning} />
              </View>
              <Text style={styles.actionText}>Code Caisse</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={styles.actionButton}
              onPress={() => navigation.navigate('Transfer')}
            >
              <View style={[styles.actionIcon, { backgroundColor: `${COLORS.primary}15` }]}>
                <Ionicons name="send" size={24} color={COLORS.primary} />
              </View>
              <Text style={styles.actionText}>Transférer</Text>
            </TouchableOpacity>
          </View>
        </Card>

        {/* Recent Transactions */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Text style={styles.sectionTitle}>Transactions récentes</Text>
            <TouchableOpacity onPress={() => navigation.navigate('Transactions')}>
              <Text style={styles.seeAll}>Voir tout</Text>
            </TouchableOpacity>
          </View>
          
          {transactions.length === 0 ? (
            <Card style={styles.emptyCard}>
              <Ionicons name="receipt-outline" size={48} color={COLORS.gray300} />
              <Text style={styles.emptyText}>Aucune transaction</Text>
            </Card>
          ) : (
            transactions.slice(0, 5).map((transaction) => (
              <Card key={transaction.id} style={styles.transactionCard}>
                <View style={[styles.transactionIcon, { backgroundColor: `${getTransactionColor(transaction.type)}15` }]}>
                  <Ionicons
                    name={getTransactionIcon(transaction.type) as any}
                    size={20}
                    color={getTransactionColor(transaction.type)}
                  />
                </View>
                <View style={styles.transactionInfo}>
                  <Text style={styles.transactionDescription}>{transaction.description}</Text>
                  <Text style={styles.transactionDate}>
  {transaction.date || transaction.created_at || 'Date inconnue'}
</Text>
                </View>
                <Text style={[
                  styles.transactionAmount,
                  { color: getTransactionColor(transaction.type) }
                ]}>
{Number(transaction.points) > 0 ? '+' : ''}{transaction.points} pts                </Text>
              </Card>
            ))
          )}
        </View>
      </ScrollView>
    </SafeAreaView>
    </SafeAreaProvider>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingTop: 50,
    paddingBottom: 20,
    paddingHorizontal: SPACING.lg,
  },
  backButton: {
    padding: SPACING.xs,
  },
  headerTitle: {
    fontSize: FONTS.sizes.xl,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  content: {
    flex: 1,
    padding: SPACING.lg,
  },
  balanceCard: {
    alignItems: 'center',
    paddingVertical: SPACING.xl,
  },
  balanceLabel: {
    fontSize: FONTS.sizes.md,
    color: COLORS.gray500,
  },
  balanceValue: {
    fontSize: 42,
    fontWeight: 'bold',
    color: COLORS.primary,
    marginVertical: SPACING.xs,
  },
  balanceEquivalent: {
    fontSize: FONTS.sizes.lg,
    color: COLORS.gray600,
  },
  actionsRow: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    width: '100%',
    marginTop: SPACING.xl,
    paddingTop: SPACING.xl,
    borderTopWidth: 1,
    borderTopColor: COLORS.gray100,
  },
  actionButton: {
    alignItems: 'center',
  },
  actionIcon: {
    width: 56,
    height: 56,
    borderRadius: 28,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: SPACING.xs,
  },
  actionText: {
    fontSize: FONTS.sizes.sm,
    color: COLORS.gray600,
    fontWeight: '500',
  },
  section: {
    marginTop: SPACING.xl,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.md,
  },
  sectionTitle: {
    fontSize: FONTS.sizes.lg,
    fontWeight: 'bold',
    color: COLORS.black,
  },
  seeAll: {
    fontSize: FONTS.sizes.md,
    color: COLORS.primary,
    fontWeight: '600',
  },
  emptyCard: {
    alignItems: 'center',
    paddingVertical: SPACING.xxl,
  },
  emptyText: {
    fontSize: FONTS.sizes.md,
    color: COLORS.gray400,
    marginTop: SPACING.md,
  },
  transactionCard: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: SPACING.sm,
  },
  transactionIcon: {
    width: 44,
    height: 44,
    borderRadius: 22,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: SPACING.md,
  },
  transactionInfo: {
    flex: 1,
  },
  transactionDescription: {
    fontSize: FONTS.sizes.md,
    color: COLORS.black,
    fontWeight: '500',
  },
  transactionDate: {
    fontSize: FONTS.sizes.sm,
    color: COLORS.gray500,
    marginTop: 2,
  },
  transactionAmount: {
    fontSize: FONTS.sizes.md,
    fontWeight: 'bold',
  },
});



tu vas donner les nouveau code complet a copier coller pour les fichiers qui auront changer

et pour les nouveau fichiers que tu vas creer tu vas aussi donner les codes complet a copier coller