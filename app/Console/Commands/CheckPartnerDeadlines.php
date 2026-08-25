<?php

namespace App\Console\Commands;

use App\Mail\PartnerPaymentAlertMail;
use App\Models\EliteUser;
use App\Models\UserPaymentInstallment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckPartnerDeadlines extends Command
{
    protected $signature = 'partners:check-deadlines';
    protected $description = 'Marque les échéances et formations partenaires en retard';

    public function handle(): int
    {
        UserPaymentInstallment::where('statut', 'en_attente')
            ->where('due_at', '<', now())
            ->with('userPack.user.partner', 'planInstallment')
            ->each(function (UserPaymentInstallment $installment) {
                $installment->update(['statut' => 'en_retard']);
                $user = $installment->userPack->user;
                $user->update(['formation_status' => 'failed']);
                $this->notifyPartner($user, 'Paiement en retard', "L'apprenant {$user->full_name} a une échéance en retard pour {$installment->planInstallment->libelle}.");
            });

        EliteUser::where('formation_status', 'active')
            ->whereNotNull('formation_deadline')
            ->where('formation_deadline', '<', now())
            ->with('partner')
            ->each(function (EliteUser $user) {
                $hasIncompleteLessons = $user->userPacks()->where('statut', 'actif')->where('progression', '<', 100)->exists();
                if ($hasIncompleteLessons) {
                    $user->update(['formation_status' => 'failed']);
                    $this->notifyPartner($user, 'Formation échouée', "L'apprenant {$user->full_name} n'a pas terminé sa formation à la date prévue.");
                }
            });

        return self::SUCCESS;
    }

    private function notifyPartner(EliteUser $user, string $subject, string $message): void
    {
        if ($user->partner?->email) {
            Mail::to($user->partner->email)->send(new PartnerPaymentAlertMail($subject, $message));
        }
    }
}