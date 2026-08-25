<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EliteUser;
use App\Models\Pack;
use App\Models\UserPack;
use App\Models\Transaction;
use App\Models\CareerProfile;
use App\Models\Partner;
use App\Models\UserPaymentInstallment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $partnerInstallments = UserPaymentInstallment::whereHas('userPack.user', fn ($q) => $q->whereNotNull('partner_id'))->get();

        $stats = [
            'total_users'                    => EliteUser::count(),
            'new_users_today'                => EliteUser::whereDate('created_at', Carbon::today())->count(),
            'new_users_week'                 => EliteUser::where('created_at', '>=', Carbon::now()->subWeek())->count(),
            'total_packs_sold'               => UserPack::count(),
            'active_packs'                   => UserPack::where('statut', 'actif')->count(),
            'total_revenue'                  => Transaction::where('type', 'depot')->where('statut', 'complete')->sum('montant_fcfa'),
            'revenue_today'                  => Transaction::where('type', 'depot')->where('statut', 'complete')->whereDate('created_at', Carbon::today())->sum('montant_fcfa'),
            'total_profiles'                 => CareerProfile::count(),
            'total_packs'                    => Pack::count(),
            // Multi-centres CFPAM
            'total_partners'                 => Partner::count(),
            'active_partners'                => Partner::where('active', true)->count(),
            'total_partner_learners'         => EliteUser::whereNotNull('partner_id')->count(),
            'partner_active_learners'        => EliteUser::whereNotNull('partner_id')->where('formation_status', 'active')->count(),
            'partner_failed_learners'        => EliteUser::whereNotNull('partner_id')->where('formation_status', 'failed')->count(),
            'partner_installments_collected' => $partnerInstallments->where('statut', 'paye')->sum('montant_fcfa'),
            'partner_installments_late'      => $partnerInstallments->where('statut', 'en_retard')->sum('montant_fcfa'),
            'partner_installments_pending'   => $partnerInstallments->where('statut', 'en_attente')->sum('montant_fcfa'),
        ];

        $recentUsers = EliteUser::latest()->take(10)->get();
        $recentTransactions = Transaction::with('user')->latest()->take(10)->get();
        $popularPacks = Pack::withCount('userPacks')->orderByDesc('user_packs_count')->take(5)->get();

        $partnersSummary = Partner::withCount([
            'learners',
            'learners as active_learners_count' => function ($q) {
                $q->where('formation_status', 'active');
            },
            'learners as failed_learners_count' => function ($q) {
                $q->where('formation_status', 'failed');
            }
        ])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentTransactions', 'popularPacks', 'partnersSummary'));
    }
}

