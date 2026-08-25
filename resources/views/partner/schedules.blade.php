@extends('partner.layouts.app')

@section('title', 'Gestion des Plages Horaires des Cours')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Horaires d'Ouverture des Cours</h1>
            <p class="text-sm text-slate-500 mt-1">Programmez l'accès aux cours en ligne pour les apprenants de votre centre selon vos plages d'heures.</p>
        </div>
    </div>

    <!-- Grille : Formulaire d'ajout + Liste des créneaux -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Formulaire Création Créneau -->
        <div class="lg:col-span-5 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm h-fit">
            <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    ⏰
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Programmer un Créneau</h2>
                    <p class="text-xs text-slate-500">Ouverture / fermeture automatique en temps réel</p>
                </div>
            </div>

            <form method="POST" action="{{ route('partner.schedules.save') }}" class="space-y-5">
                @csrf

                <!-- Sélection Pack -->
                <div>
                    <label for="pack_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Pack / Formation <span class="text-rose-500">*</span>
                    </label>
                    <select name="pack_id" id="pack_id" required
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white">
                        <option value="">Sélectionnez la formation concernée...</option>
                        @foreach($packs as $pack)
                            <option value="{{ $pack->id }}" {{ old('pack_id') == $pack->id ? 'selected' : '' }}>
                                {{ $pack->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sélection Leçon spécifique (Optionnel) -->
                <div>
                    <label for="lesson_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Leçon Spécifique <span class="text-slate-400 font-normal">(Optionnel)</span>
                    </label>
                    <select name="lesson_id" id="lesson_id"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white">
                        <option value="">Toutes les leçons du pack (Accès Global)</option>
                        @foreach($lessons as $lesson)
                            <option value="{{ $lesson->id }}" {{ old('lesson_id') == $lesson->id ? 'selected' : '' }}>
                                {{ $lesson->chapter->module->pack->nom ?? '' }} ➔ {{ $lesson->titre }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Laissez vide pour autoriser l'accès à l'ensemble du pack pendant cette plage.</p>
                </div>

                <!-- Date & Heure Début -->
                <div>
                    <label for="starts_at" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Heure d'ouverture du cours <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" name="starts_at" id="starts_at" value="{{ old('starts_at', now()->format('Y-m-d\TH:i')) }}" required
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                </div>

                <!-- Date & Heure Fin -->
                <div>
                    <label for="ends_at" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Heure de fermeture automatique <span class="text-slate-400 font-normal">(Optionnel)</span>
                    </label>
                    <input type="datetime-local" name="ends_at" id="ends_at" value="{{ old('ends_at', now()->addHours(2)->format('Y-m-d\TH:i')) }}"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <p class="text-[11px] text-slate-400 mt-1">Passé cette heure, l'accès au cours sera reverrouillé pour vos apprenants.</p>
                </div>

                <div class="pt-3">
                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md transition flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Programmer cette Plage Horaire
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste des Plages Horaires Programmées -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-slate-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Plages Horaires Enregistrées ({{ $schedules->total() }})
                    </h2>
                </div>

                @if($schedules->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">⏰</div>
                        <p class="text-xs text-slate-500">Aucune plage horaire programmée.<br>Utilisez le formulaire pour ouvrir des créneaux d'étude.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($schedules as $sched)
                            @php
                                $isOpen = $sched->isOpen();
                                $starts = \Carbon\Carbon::parse($sched->starts_at);
                                $ends = $sched->ends_at ? \Carbon\Carbon::parse($sched->ends_at) : null;
                                $isPast = $ends ? $ends->isPast() : false;
                            @endphp
                            <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-sm text-slate-900">{{ $sched->pack->nom }}</span>
                                        @if($isOpen)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500 text-white animate-pulse">
                                                ● OUVERT EN CE MOMENT
                                            </span>
                                        @elseif($isPast)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">
                                                Terminé
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                Programmé
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1">
                                        {{ $sched->lesson ? ('Leçon : ' . $sched->lesson->titre) : 'Accès à toutes les leçons du pack' }}
                                    </div>
                                    <div class="text-[11px] font-semibold text-emerald-700 mt-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $starts->translatedFormat('d M Y à H:i') }}
                                        @if($ends)
                                            ➔ {{ $ends->translatedFormat('H:i') }} (durée: {{ $starts->diffInMinutes($ends) }} min)
                                        @else
                                            (Accès illimité après ouverture)
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <form method="POST" action="{{ route('partner.schedules.delete', $sched) }}" onsubmit="return confirm('Supprimer ce créneau horaire ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Supprimer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($schedules->hasPages())
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            {{ $schedules->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
