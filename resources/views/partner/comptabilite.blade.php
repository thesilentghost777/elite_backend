@extends('partner.layouts.app')

@section('title', 'Comptabilité des 5 Tranches & Apprenants')

@section('content')
<div class="space-y-8">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Comptabilité des 5 Tranches</h1>
            <p class="text-sm text-slate-500 mt-1">Suivi financier par apprenant, encaissement au guichet, relevés individuels et rapports imprimables.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('partner.comptabilite.report', request()->query()) }}" target="_blank" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition">
                <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Imprimer le Rapport Global (PDF)
            </a>
            <a href="{{ route('partner.plans') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Gérer les Échéanciers
            </a>
        </div>
    </div>

    <!-- Stats Comptables Globaux -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Attendu</span>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ number_format($totalAttendu, 0, ',', ' ') }} <span class="text-xs font-bold text-slate-500">FCFA</span></div>
            <span class="text-[11px] text-slate-400 mt-1 block">Toutes les 5 tranches cumulées</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-emerald-200/80 bg-emerald-50/20 shadow-sm">
            <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block">Total Encaissé</span>
            <div class="text-2xl font-black text-emerald-600 mt-2">{{ number_format($totalEncaisse, 0, ',', ' ') }} <span class="text-xs font-bold text-emerald-700">FCFA</span></div>
            <span class="text-[11px] text-emerald-600 mt-1 block">Taux de recouvrement : <strong>{{ $tauxRecouvrement }}%</strong></span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-amber-200/80 bg-amber-50/20 shadow-sm">
            <span class="text-xs font-bold text-amber-700 uppercase tracking-wider block">En attente</span>
            <div class="text-2xl font-black text-amber-600 mt-2">{{ number_format($totalEnAttente, 0, ',', ' ') }} <span class="text-xs font-bold text-amber-700">FCFA</span></div>
            <span class="text-[11px] text-amber-600 mt-1 block">Échéances futures non échues</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-rose-200/80 bg-rose-50/20 shadow-sm">
            <span class="text-xs font-bold text-rose-700 uppercase tracking-wider block">Impayés / En Retard</span>
            <div class="text-2xl font-black text-rose-600 mt-2">{{ number_format($totalEnRetard, 0, ',', ' ') }} <span class="text-xs font-bold text-rose-700">FCFA</span></div>
            <span class="text-[11px] text-rose-600 mt-1 block">Échéances dépassées</span>
        </div>
    </div>

    <!-- Filtres & Recherche -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('partner.comptabilite') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Rechercher</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, matricule, tél..."
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Formation</label>
                <select name="pack_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes les formations</option>
                    @foreach($packs as $pack)
                        <option value="{{ $pack->id }}" {{ (string)request('pack_id') === (string)$pack->id ? 'selected' : '' }}>
                            {{ $pack->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Statut Formation</label>
                <select name="statut" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('statut') === 'active' ? 'selected' : '' }}>En cours (Active)</option>
                    <option value="complete" {{ request('statut') === 'complete' ? 'selected' : '' }}>Terminée avec succès</option>
                    <option value="failed" {{ request('statut') === 'failed' ? 'selected' : '' }}>Échouée (Retard)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Statut Paiement</label>
                <select name="payment_status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les états financiers</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>100% Soldé</option>
                    <option value="late" {{ request('payment_status') === 'late' ? 'selected' : '' }}>Avec Impayé / Retard</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Tranches en attente</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                    Filtrer
                </button>
                <a href="{{ route('partner.comptabilite') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition" title="Réinitialiser">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </a>
            </div>
        </form>
    </div>

    <!-- Table des Apprenants & Détail des 5 Tranches -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between flex-wrap gap-2">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                Liste des apprenants rattachés ({{ $learners->total() }})
            </h2>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500 hidden sm:inline">
                    Paiements fixes : 10k (Inscr.) · 200k (Scol.) · 135k (Mat.) · 55k (Exam) · 55k (Stage)
                </span>
                <a href="{{ route('partner.comptabilite.report', request()->query()) }}" target="_blank" class="text-xs font-bold text-blue-600 hover:underline inline-flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Imprimer
                </a>
            </div>
        </div>

        @if($learners->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Aucun apprenant trouvé</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Vos apprenants inscrits avec votre code partenaire ou répondant à vos filtres apparaîtront ici.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/75 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-4">Apprenant</th>
                            <th class="py-3.5 px-4">Formation & Statut</th>
                            <th class="py-3.5 px-4">Suivi des 5 Tranches</th>
                            <th class="py-3.5 px-4 text-right">Rapports & Guichet</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($learners as $learner)
                            @php
                                $userPack = $learner->userPacks->first();
                                $installments = $userPack ? $userPack->installments->sortBy('planInstallment.ordre') : collect();
                                $paidCount = $installments->where('statut', 'paye')->count();
                                $hasLate = $installments->where('statut', 'en_retard')->count() > 0;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition items-top">
                                <!-- Col 1: Apprenant -->
                                <td class="py-4 px-4 align-top">
                                    <div class="font-bold text-slate-900 text-sm">{{ $learner->full_name }}</div>
                                    <div class="text-slate-500 text-[11px] mt-0.5">{{ $learner->telephone ?: $learner->email }}</div>
                                    <div class="text-slate-400 text-[10px] mt-0.5">Inscrit le {{ $learner->created_at ? $learner->created_at->format('d/m/Y') : '-' }} &bull; Code: <strong>{{ $learner->referral_code }}</strong></div>
                                </td>

                                <!-- Col 2: Formation & Statut -->
                                <td class="py-4 px-4 align-top">
                                    @if($userPack && $userPack->pack)
                                        <div class="font-bold text-slate-800">{{ $userPack->pack->nom }}</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">
                                            Progression : <span class="font-semibold text-slate-700">{{ round($userPack->progression) }}%</span>
                                        </div>
                                    @else
                                        <div class="text-slate-400 italic">Pack non sélectionné</div>
                                    @endif

                                    <div class="mt-2">
                                        @if($learner->formation_status === 'active')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                En formation
                                            </span>
                                        @elseif($learner->formation_status === 'complete')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                                Validé
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                                Échec / Retard
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Col 3: Suivi des 5 Tranches -->
                                <td class="py-4 px-4 align-top">
                                    @if($installments->isEmpty())
                                        <span class="text-slate-400 italic text-[11px]">Aucun échéancier rattaché</span>
                                    @else
                                        <div class="grid grid-cols-1 sm:grid-cols-5 gap-2 max-w-xl">
                                            @foreach($installments as $inst)
                                                @php
                                                    $libelle = $inst->planInstallment->libelle ?? ('T' . $loop->iteration);
                                                    $montant = (float) $inst->montant_fcfa;
                                                @endphp
                                                <div class="p-2 rounded-xl border text-[11px] {{ $inst->statut === 'paye' ? 'border-emerald-200 bg-emerald-50/60' : ($inst->statut === 'en_retard' ? 'border-rose-200 bg-rose-50/60' : 'border-slate-200 bg-slate-50') }}">
                                                    <div class="font-bold text-slate-800 truncate" title="{{ $libelle }}">
                                                        {{ $libelle }}
                                                    </div>
                                                    <div class="font-mono font-bold text-[10px] mt-0.5 {{ $inst->statut === 'paye' ? 'text-emerald-700' : ($inst->statut === 'en_retard' ? 'text-rose-700' : 'text-slate-600') }}">
                                                        {{ number_format($montant, 0, ',', ' ') }} F
                                                    </div>
                                                    <div class="mt-1">
                                                        @if($inst->statut === 'paye')
                                                            <span class="inline-block text-[9px] font-extrabold text-emerald-700 uppercase">
                                                                ✓ Payé
                                                            </span>
                                                        @elseif($inst->statut === 'en_retard')
                                                            <span class="inline-block text-[9px] font-extrabold text-rose-700 uppercase">
                                                                ! Retard
                                                            </span>
                                                        @else
                                                            <span class="inline-block text-[9px] font-bold text-slate-500">
                                                                Dû le {{ $inst->due_at ? \Carbon\Carbon::parse($inst->due_at)->format('d/m') : '-' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>

                                <!-- Col 4: Action Encaisser Comptoir & Fiche PDF -->
                                <td class="py-4 px-4 align-top text-right">
                                    <div class="flex items-center justify-end gap-2 flex-wrap">
                                        <a href="{{ route('partner.comptabilite.learner', $learner) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition" title="Imprimer la fiche individuelle">
                                            <svg class="w-3.5 h-3.5 mr-1 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            Fiche PDF
                                        </a>

                                        @if($installments->isNotEmpty())
                                            @php
                                                $nextUnpaid = $installments->firstWhere('statut', '!=', 'paye');
                                            @endphp
                                            @if($nextUnpaid)
                                                <form method="POST" action="{{ route('partner.installments.pay-counter', $nextUnpaid) }}" onsubmit="return confirm('Confirmer l\'encaissement au guichet de {{ number_format($nextUnpaid->montant_fcfa, 0, ',', ' ') }} FCFA pour {{ $learner->full_name }} ?');">
                                                    @csrf
                                                    <input type="hidden" name="notes" value="Encaissement physique au guichet du centre {{ $partner->nom }}">
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold shadow-sm transition">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        Encaisser {{ $nextUnpaid->planInstallment->libelle ?? 'Tranche' }}
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                    100% Soldé
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($learners->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $learners->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
