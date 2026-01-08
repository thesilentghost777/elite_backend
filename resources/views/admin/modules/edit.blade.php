@extends('layouts.admin')

@section('title', 'Modifier le Module')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-green-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- En-tête -->
        <div class="mb-6">
            <a href="{{ route('admin.packs.show', $module->pack) }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-200 mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour au pack
            </a>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-2">
                    <div class="bg-gradient-to-r from-blue-600 to-green-600 text-white rounded-lg p-3 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Modifier le Module</h1>
                        <p class="mt-1 text-gray-600">{{ $module->nom }}</p>
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
        <form action="{{ route('admin.modules.update', $module) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Informations du Module</h2>

                <div class="space-y-6">
                    <div>
                        <label for="nom" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nom du Module <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nom" 
                               id="nom" 
                               value="{{ old('nom', $module->nom) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Ex: Introduction au Secrétariat"
                               required>
                        <p class="mt-1 text-sm text-gray-500">Donnez un nom descriptif au module</p>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                  placeholder="Décrivez le contenu du module...">{{ old('description', $module->description) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Expliquez ce que les apprenants vont découvrir dans ce module</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                                Type de Module <span class="text-red-500">*</span>
                            </label>
                            <select name="type" 
                                    id="type" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    required>
                                <option value="">Sélectionnez un type</option>
                                <option value="theorique" {{ old('type', $module->type) == 'theorique' ? 'selected' : '' }}>
                                    📚 Théorique
                                </option>
                                <option value="pratique" {{ old('type', $module->type) == 'pratique' ? 'selected' : '' }}>
                                    🛠️ Pratique
                                </option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Choisissez le type d'apprentissage</p>
                        </div>

                        <div>
                            <label for="ordre" class="block text-sm font-semibold text-gray-700 mb-2">
                                Ordre d'Affichage <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="ordre" 
                                   id="ordre" 
                                   value="{{ old('ordre', $module->ordre) }}"
                                   min="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   required>
                            <p class="mt-1 text-sm text-gray-500">Position du module dans le pack</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="active" 
                                   id="active" 
                                   value="1"
                                   {{ old('active', $module->active) ? 'checked' : '' }}
                                   class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-3">
                                <span class="text-sm font-semibold text-gray-700">Module actif</span>
                                <p class="text-sm text-gray-500">Les modules actifs sont visibles par les apprenants</p>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Statistiques du module -->
            <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-lg shadow-lg p-6 border border-blue-200">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-2 mr-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Chapitres</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $module->chapters->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="bg-green-100 rounded-full p-2 mr-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Leçons</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $module->chapters->sum(fn($c) => $c->lessons->count()) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-between">
                <form action="{{ route('admin.modules.destroy', $module) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce module ? Cette action est irréversible.');">
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
                    <a href="{{ route('admin.packs.show', $module->pack) }}" 
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