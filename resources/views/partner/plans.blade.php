@extends('partner.layouts.app')

@section('title', 'Paramétrage des Échéanciers (5 Tranches)')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Paramétrage des Échéanciers</h1>
            <p class="text-sm text-slate-500 mt-1">Définissez vos propres délais de paiement pour chaque pack de formation (5 tranches obligatoires).</p>
        </div>
    </div>

    <!-- Grille : Formulaire de création / modification + Liste des plans existants -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Formulaire de Configuration -->
        <div class="lg:col-span-6 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    ⚙️
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Configurer un Échéancier</h2>
                    <p class="text-xs text-slate-500">Montants réglementaires CFPAM fixes · Délais personnalisés</p>
                </div>
            </div>

            <form method="POST" action="{{ route('partner.plans.save') }}" class="space-y-5">
                @csrf

                <!-- Sélection du Pack -->
                <div>
                    <label for="pack_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Formation / Pack associé <span class="text-rose-500">*</span>
                    </label>
                    <select name="pack_id" id="pack_id" required
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">Sélectionnez un pack de cours...</option>
                        @foreach($packs as $pack)
                            <option value="{{ $pack->id }}" {{ old('pack_id') == $pack->id ? 'selected' : '' }}>
                                {{ $pack->nom }} ({{ $pack->categorie ? $pack->categorie->nom : 'Général' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nom du Plan -->
                <div>
                    <label for="nom" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Intitulé de l'échéancier <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom', 'Promotion ' . date('Y') . ' - Standard') }}" required
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Ex: Session Octobre 2026 - Temps Plein">
                </div>

                <!-- Date de fin de formation -->
                <div>
                    <label for="date_fin_formation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Date de fin de formation (Deadline Finale) <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="date_fin_formation" id="date_fin_formation" value="{{ old('date_fin_formation', now()->addMonths(9)->format('Y-m-d')) }}" required
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-[11px] text-slate-400 mt-1">À cette date, si l'étudiant n'a pas fini ses cours, son statut passe en échec automatique.</p>
                </div>

                <!-- Les 5 Tranches Définies -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Les 5 Tranches de Paiement</h3>
                        <span class="text-xs font-black text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                            Total : 455 000 FCFA
                        </span>
                    </div>

                    @php
                        $defaultTranches = [
                            ['libelle' => '1. Inscription', 'montant' => 10000, 'delai' => 0],
                            ['libelle' => '2. Scolarité', 'montant' => 200000, 'delai' => 30],
                            ['libelle' => '3. Matière d\'œuvre', 'montant' => 135000, 'delai' => 60],
                            ['libelle' => '4. Inscription / Frais exam', 'montant' => 55000, 'delai' => 90],
                            ['libelle' => '5. Stage et soutenance', 'montant' => 55000, 'delai' => 120],
                        ];
                    @endphp

                    <div class="space-y-3">
                        @foreach($defaultTranches as $index => $t)
                            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex-1">
                                    <input type="hidden" name="installments[{{ $index }}][libelle]" value="{{ $t['libelle'] }}">
                                    <input type="hidden" name="installments[{{ $index }}][montant_fcfa]" value="{{ $t['montant'] }}">
                                    <div class="font-bold text-xs text-slate-900">{{ $t['libelle'] }}</div>
                                    <div class="text-[11px] font-mono font-bold text-emerald-700 mt-0.5">
                                        {{ number_format($t['montant'], 0, ',', ' ') }} FCFA
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <label class="text-[11px] text-slate-600 font-semibold whitespace-nowrap">Délai :</label>
                                    <div class="relative w-24">
                                        <input type="number" min="0" max="365" name="installments[{{ $index }}][delai_jours]"
                                            value="{{ old("installments.{$index}.delai_jours", $t['delai']) }}" required
                                            class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs font-bold text-center focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
                                    </div>
                                    <span class="text-[11px] text-slate-500 font-semibold">jours</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Submit -->
                <div class="pt-4">
                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md transition flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Enregistrer cet Échéancier
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste des Échéanciers Configurés -->
        <div class="lg:col-span-6 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Échéanciers Actifs ({{ $partner->paymentPlans->count() }})
                </h2>

                @if($partner->paymentPlans->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">📋</div>
                        <p class="text-xs text-slate-500">Aucun échéancier configuré pour l'instant.<br>Remplissez le formulaire ci-contre pour configurer votre premier pack.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($partner->paymentPlans as $plan)
                            <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:shadow-md transition">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 uppercase mb-1">
                                            {{ $plan->pack ? $plan->pack->nom : 'Pack Inconnu' }}
                                        </span>
                                        <h3 class="font-bold text-sm text-slate-900">{{ $plan->nom }}</h3>
                                        <p class="text-[11px] text-slate-500 mt-0.5">
                                            Fin de formation : <span class="font-semibold text-slate-700">{{ $plan->date_fin_formation ? $plan->date_fin_formation->format('d/m/Y') : 'Non définie' }}</span>
                                        </p>
                                    </div>
                                    <form method="POST" action="{{ route('partner.plans.delete', $plan) }}" onsubmit="return confirm('Supprimer cet échéancier ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg text-rose-500 hover:bg-rose-50 transition" title="Supprimer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>

                                <!-- Les tranches du plan -->
                                <div class="mt-4 pt-4 border-t border-slate-200/80">
                                    <span class="text-[11px] font-bold text-slate-600 block mb-2 uppercase tracking-wider">Tranches & Délais :</span>
                                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-2">
                                        @foreach($plan->installments as $inst)
                                            <div class="p-2 rounded-lg bg-white border border-slate-200 text-center">
                                                <div class="text-[10px] font-bold text-slate-800 truncate" title="{{ $inst->libelle }}">{{ $inst->libelle }}</div>
                                                <div class="text-[10px] font-mono text-emerald-700 font-bold mt-0.5">{{ number_format($inst->montant_fcfa, 0, ',', ' ') }} F</div>
                                                <div class="text-[9px] text-slate-500 font-semibold mt-0.5">+{{ $inst->delai_jours }}j</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
