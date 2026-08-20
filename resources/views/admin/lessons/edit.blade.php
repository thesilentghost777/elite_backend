@extends('layouts.admin')

@section('title', 'Modifier la Leçon')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-green-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="mb-6">
            <a href="{{ route('admin.chapters.show', $lesson->chapter) }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-200 mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour au chapitre
            </a>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-2">
                    <div class="bg-gradient-to-r from-blue-600 to-green-600 text-white rounded-lg p-3 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Modifier la Leçon</h1>
                        <p class="mt-1 text-gray-600">{{ $lesson->titre }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium">Il y a des erreurs dans le formulaire :</h3>
                    <ul class="mt-2 text-sm list-disc list-inside">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Formulaire -->
        <form action="{{ route('admin.lessons.update', $lesson) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Informations de base -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-gradient-to-r from-blue-600 to-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                    Informations de Base
                </h2>

                <div class="space-y-6">
                    <div>
                        <label for="titre" class="block text-sm font-semibold text-gray-700 mb-2">
                            Titre de la Leçon <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="titre" 
                               id="titre" 
                               value="{{ old('titre', $lesson->titre) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Ex: Les outils de bureautique essentiels"
                               required>
                        <p class="mt-1 text-sm text-gray-500">Donnez un titre clair et descriptif</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="duree_minutes" class="block text-sm font-semibold text-gray-700 mb-2">
                                Durée (Minutes) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       name="duree_minutes" 
                                       id="duree_minutes" 
                                       value="{{ old('duree_minutes', $lesson->duree_minutes) }}"
                                       min="1"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Temps estimé pour cette leçon</p>
                        </div>

                        <div>
                            <label for="ordre" class="block text-sm font-semibold text-gray-700 mb-2">
                                Ordre d'Affichage <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="ordre" 
                                   id="ordre" 
                                   value="{{ old('ordre', $lesson->ordre) }}"
                                   min="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   required>
                            <p class="mt-1 text-sm text-gray-500">Position dans le chapitre</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenu de la leçon -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-gradient-to-r from-blue-600 to-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                    Contenu de la Leçon
                </h2>

                <div class="space-y-6">
                    <div>
                        <label for="contenu_texte" class="block text-sm font-semibold text-gray-700 mb-2">
                            Contenu Texte
                        </label>
                        <textarea name="contenu_texte" 
                                  id="contenu_texte" 
                                  rows="8"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 font-mono text-sm"
                                  placeholder="Saisissez le contenu de la leçon...">{{ old('contenu_texte', $lesson->contenu_texte) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Le contenu principal de votre leçon (supporte le Markdown)</p>
                    </div>
                </div>
            </div>

            <!-- Ressources multimédia -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-gradient-to-r from-blue-600 to-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                    Ressources Multimédia
                </h2>

                <div class="space-y-6">
                    <div>
                        <label for="url_video" class="block text-sm font-semibold text-gray-700 mb-2">
                            URL de la Vidéo
                        </label>
                        <div class="relative">
                            <input type="url" 
                                   name="url_video" 
                                   id="url_video" 
                                   value="{{ old('url_video', $lesson->url_video) }}"
                                   class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="https://youtube.com/watch?v=...">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Lien vers une vidéo YouTube, Vimeo, etc.</p>
                        @if($lesson->url_video)
                        <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded flex items-center text-sm text-green-700">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Vidéo actuellement configurée
                        </div>
                        @endif
                    </div>

                    <div>
                        <label for="url_externe" class="block text-sm font-semibold text-gray-700 mb-2">
                            URL Externe
                        </label>
                        <div class="relative">
                            <input type="url" 
                                   name="url_externe" 
                                   id="url_externe" 
                                   value="{{ old('url_externe', $lesson->url_externe) }}"
                                   class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="https://example.com/resource">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Lien vers une ressource externe ou un document</p>
                        @if($lesson->url_externe)
                        <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded flex items-center text-sm text-green-700">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            URL externe actuellement configurée
                        </div>
                        @endif
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-blue-900">À propos des ressources</h3>
                                <p class="mt-1 text-sm text-blue-700">
                                    Les ressources multimédia sont optionnelles. Vous pouvez ajouter une vidéo, un lien externe, ou les deux pour enrichir votre leçon.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paramètres -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-gradient-to-r from-blue-600 to-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
                    Paramètres
                </h2>

                <div class="border-t border-gray-200 pt-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="active" 
                               id="active" 
                               value="1"
                               {{ old('active', $lesson->active) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3">
                            <span class="text-sm font-semibold text-gray-700">Leçon active</span>
                            <p class="text-sm text-gray-500">Les leçons actives sont visibles par les apprenants</p>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg shadow-lg p-6 border border-purple-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Statistiques</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="bg-purple-100 rounded-full p-2 mr-3">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Vues</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ $lesson->progress->count() }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="bg-green-100 rounded-full p-2 mr-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Complétée</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ $lesson->progress->where('completed', true)->count() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-between">
                <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette leçon ? Cette action est irréversible.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors duration-200">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer
                        </span>
                    </button>
                </form>

                <div class="flex space-x-4">
                    <a href="{{ route('admin.chapters.show', $lesson->chapter) }}" 
                       class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors duration-200">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-green-600 text-white font-semibold rounded-lg shadow-md hover:from-blue-700 hover:to-green-700 transform hover:scale-105 transition-all duration-200">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Mettre à Jour
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection