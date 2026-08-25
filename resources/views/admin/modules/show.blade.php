@extends('admin.layouts.app')

@section('title', 'Détails du Module : ' . $module->nom)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    <span>/</span>
    <a href="{{ route('admin.packs.show', $module->pack) }}">{{ $module->pack->nom }}</a>
    <span>/</span>
    <span>{{ $module->nom }}</span>
@endsection

@section('content')
@php
    $activeQuiz = $module->activeQuiz();
    $lessons = $module->lessons;
    $totalMinutes = $lessons->sum('duree_minutes');
@endphp

<style>
    .module-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(30, 58, 138, 0.25);
    }

    .hero-flex {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .stat-box {
        background: white;
        border-radius: 14px;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    }

    .stat-val {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1e40af;
        margin-bottom: 0.25rem;
    }

    .stat-lbl {
        font-size: 0.8rem;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.05em;
    }

    .lessons-table {
        width: 100%;
        border-collapse: collapse;
    }

    .lessons-table th {
        text-align: left;
        padding: 0.85rem 1rem;
        background: #f8fafc;
        color: #475569;
        font-size: 0.8rem;
        text-transform: uppercase;
        font-weight: 700;
        border-bottom: 2px solid #e2e8f0;
    }

    .lessons-table td {
        padding: 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .part-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .part-tag.blue { background: #dbeafe; color: #1d4ed8; }
    .part-tag.purple { background: #ede9fe; color: #6d28d9; }
    .part-tag.green { background: #d1fae5; color: #047857; }
</style>

<!-- Hero Module -->
<div class="module-hero">
    <div class="hero-flex">
        <div>
            <div style="font-size: 0.875rem; opacity: 0.85; margin-bottom: 0.35rem;">
                <i class="fas fa-box" style="margin-right: 0.35rem;"></i> Pack : {{ $module->pack->nom }}
            </div>
            <h1 style="font-size: 1.85rem; font-weight: 800; margin: 0 0 0.5rem 0;">{{ $module->nom }}</h1>
            @if($module->description)
                <p style="opacity: 0.9; margin: 0 0 1rem 0; max-width: 650px; font-size: 0.95rem;">{{ $module->description }}</p>
            @endif

            <div style="display: flex; gap: 1rem; flex-wrap: wrap; font-size: 0.875rem;">
                <span class="badge" style="background: rgba(255,255,255,0.25); color: white; text-transform: uppercase;">
                    Type : {{ $module->type }}
                </span>
                <span class="badge" style="background: rgba(255,255,255,0.25); color: white;">
                    Ordre #{{ $module->ordre }}
                </span>
                @if($module->active)
                    <span class="badge" style="background: #10b981; color: white;">Actif</span>
                @else
                    <span class="badge" style="background: #ef4444; color: white;">Inactif</span>
                @endif
            </div>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.packs.show', $module->pack) }}" class="btn btn-secondary" style="background: rgba(255,255,255,0.15); border: none; color: white;">
                <i class="fas fa-arrow-left" style="margin-right: 0.4rem;"></i> Retour Pack
            </a>
            <a href="{{ route('admin.modules.edit', $module) }}" class="btn btn-secondary" style="background: rgba(255,255,255,0.25); border: none; color: white;">
                <i class="fas fa-edit" style="margin-right: 0.4rem;"></i> Modifier Module
            </a>
        </div>
    </div>
</div>

<!-- Stats Quick View -->
<div class="stats-grid">
    <div class="stat-box">
        <div class="stat-val">{{ $lessons->count() }}</div>
        <div class="stat-lbl">Leçons du module</div>
    </div>
    <div class="stat-box">
        <div class="stat-val" style="color: #7c3aed;">{{ $totalMinutes }} min</div>
        <div class="stat-lbl">Durée totale estimée</div>
    </div>
    <div class="stat-box">
        <div class="stat-val" style="color: {{ $activeQuiz ? '#059669' : '#d97706' }};">
            {{ $activeQuiz ? '10 Q' : 'Sans quiz' }}
        </div>
        <div class="stat-lbl">{{ $activeQuiz ? 'Quiz de fin configuré' : 'Validation automatique' }}</div>
    </div>
    <div class="stat-box">
        <div class="stat-val" style="color: #2563eb;">{{ $module->note_passage }}/20</div>
        <div class="stat-lbl">Note de passage (Palier 7)</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <!-- Liste des Leçons -->
    <div>
        <div class="form-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #1e293b;">
                    <i class="fas fa-layer-group" style="color: #2563eb; margin-right: 0.4rem;"></i>
                    Leçons E-Learning ({{ $lessons->count() }})
                </h3>
                <a href="{{ route('admin.lessons.create', ['module' => $module->id]) }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                    <i class="fas fa-plus" style="margin-right: 0.35rem;"></i> Nouvelle Leçon
                </a>
            </div>

            @if($lessons->count() > 0)
                <div style="overflow-x: auto;">
                    <table class="lessons-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Titre de la leçon</th>
                                <th>Composantes (3 Parties)</th>
                                <th>Durée</th>
                                <th>Statut</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lessons->sortBy('ordre') as $lesson)
                            <tr>
                                <td>
                                    <span style="font-weight: 800; color: #1e40af; background: #eff6ff; width: 26px; height: 26px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center;">
                                        {{ $lesson->ordre }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">
                                        {{ $lesson->titre }}
                                    </div>
                                    @if($lesson->url_web)
                                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">
                                            <i class="fas fa-link"></i> {{ Str::limit($lesson->url_web, 40) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                                        @if($lesson->contenu_texte || $lesson->url_web)
                                            <span class="part-tag blue" title="Théorie & Support">
                                                <i class="fas fa-book"></i> Théorie
                                            </span>
                                        @endif
                                        @if($lesson->url_video_explication || $lesson->url_video)
                                            <span class="part-tag purple" title="Vidéo explicative">
                                                <i class="fas fa-play"></i> Explication
                                            </span>
                                        @endif
                                        @if($lesson->url_video_pratique)
                                            <span class="part-tag green" title="Vidéo pratique">
                                                <i class="fas fa-laptop-code"></i> Pratique
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size: 0.85rem; color: #64748b; font-weight: 600;">
                                        {{ $lesson->duree_minutes }} min
                                    </span>
                                </td>
                                <td>
                                    @if($lesson->active)
                                        <span class="badge" style="background: #d1fae5; color: #065f46; font-size: 0.75rem;">Actif</span>
                                    @else
                                        <span class="badge" style="background: #fee2e2; color: #991b1b; font-size: 0.75rem;">Inactif</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 0.4rem;">
                                        <a href="{{ route('admin.lessons.edit', $lesson) }}" class="btn btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Supprimer cette leçon ?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 2.5rem; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                    <i class="fas fa-folder-open" style="font-size: 2rem; color: #94a3b8; margin-bottom: 0.75rem;"></i>
                    <p style="color: #64748b; margin: 0 0 1rem 0; font-size: 0.95rem;">Aucune leçon n'est encore rattachée à ce module.</p>
                    <a href="{{ route('admin.lessons.create', ['module' => $module->id]) }}" class="btn btn-primary">
                        <i class="fas fa-plus" style="margin-right: 0.4rem;"></i> Créer la première leçon
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Section Quiz du Module -->
    <div>
        <div class="form-card">
            <h3 style="margin: 0 0 1.25rem 0; font-size: 1.15rem; font-weight: 700; color: #1e293b;">
                <i class="fas fa-trophy" style="color: #f59e0b; margin-right: 0.4rem;"></i>
                Quiz de Fin de Module
            </h3>

            @if($activeQuiz)
                <div style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                        <h4 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #1e293b;">{{ $activeQuiz->titre }}</h4>
                        @if($activeQuiz->questions->count() === 10)
                            <span class="badge" style="background: #d1fae5; color: #065f46; font-size: 0.75rem;">10/10 Prêt</span>
                        @else
                            <span class="badge" style="background: #fef3c7; color: #92400e; font-size: 0.75rem;">{{ $activeQuiz->questions->count() }}/10 Incomplet</span>
                        @endif
                    </div>

                    <div style="font-size: 0.85rem; color: #64748b; line-height: 1.6; margin-bottom: 1rem;">
                        <div><i class="fas fa-clock" style="margin-right: 0.35rem; color: #3b82f6;"></i> Durée : <strong>{{ $activeQuiz->duree_minutes }} min</strong></div>
                        <div><i class="fas fa-coins" style="margin-right: 0.35rem; color: #f59e0b;"></i> Cagnotte : <strong>1 000 000 FCFA</strong></div>
                        <div><i class="fas fa-star" style="margin-right: 0.35rem; color: #10b981;"></i> Palier de passage : <strong>7/10 ({{ $module->note_passage }}/20)</strong></div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="{{ route('admin.quizzes.show', $activeQuiz) }}" class="btn btn-primary" style="text-align: center; justify-content: center;">
                            <i class="fas fa-list-ol" style="margin-right: 0.4rem;"></i> Ouvrir l'Assistant 10 Questions
                        </a>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('admin.quizzes.edit', $activeQuiz) }}" class="btn btn-secondary" style="flex: 1; text-align: center; justify-content: center; font-size: 0.85rem;">
                                <i class="fas fa-cog"></i> Paramètres
                            </a>
                            <form action="{{ route('admin.quizzes.destroy', $activeQuiz) }}" method="POST" onsubmit="return confirm('Supprimer ce quiz ?');" style="flex: 1;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="width: 100%; font-size: 0.85rem;">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; gap: 0.75rem; align-items: flex-start; margin-bottom: 1rem;">
                        <i class="fas fa-info-circle" style="color: #2563eb; font-size: 1.25rem; margin-top: 0.1rem;"></i>
                        <div style="font-size: 0.875rem; color: #1e3a8a; line-height: 1.5;">
                            <strong>Validation automatique sans quiz :</strong><br>
                            Aucun quiz n'est actuellement configuré pour ce module. Il sera validé automatiquement dès que l'apprenant termine toutes ses leçons.
                        </div>
                    </div>

                    <a href="{{ route('admin.quizzes.create', ['module' => $module->id]) }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        <i class="fas fa-plus" style="margin-right: 0.4rem;"></i> Ajouter un Quiz de Fin
                    </a>
                </div>
            @endif

            <!-- Paramètres de déblocage -->
            <div style="border-top: 1px solid #e2e8f0; padding-top: 1rem; font-size: 0.85rem; color: #64748b;">
                <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Critères de passage & Parrainage</div>
                <div>&bull; Note de passage directe : <strong>{{ $module->note_passage }}/20</strong></div>
                <div>&bull; Seuil parrainage : <strong>{{ $module->note_parrainage }}/20</strong></div>
                <div>&bull; Parrainages requis : <strong>{{ $module->parrainages_requis }} filleuls</strong></div>
            </div>
        </div>
    </div>
</div>
@endsection
