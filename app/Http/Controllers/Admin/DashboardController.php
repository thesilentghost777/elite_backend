<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EliteUser;
use App\Models\Pack;
use App\Models\UserPack;
use App\Models\Transaction;
use App\Models\CareerProfile;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => EliteUser::count(),
            'new_users_today' => EliteUser::whereDate('created_at', Carbon::today())->count(),
            'new_users_week' => EliteUser::where('created_at', '>=', Carbon::now()->subWeek())->count(),
            'total_packs_sold' => UserPack::count(),
            'active_packs' => UserPack::where('statut', 'actif')->count(),
            'total_revenue' => Transaction::where('type', 'depot')->where('statut', 'complete')->sum('montant_fcfa'),
            'revenue_today' => Transaction::where('type', 'depot')->where('statut', 'complete')->whereDate('created_at', Carbon::today())->sum('montant_fcfa'),
            'total_profiles' => CareerProfile::count(),
            'total_packs' => Pack::count(),
        ];

        $recentUsers = EliteUser::latest()->take(10)->get();
        $recentTransactions = Transaction::with('user')->latest()->take(10)->get();
        $popularPacks = Pack::withCount('userPacks')->orderByDesc('user_packs_count')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentTransactions', 'popularPacks'));
    }
}
