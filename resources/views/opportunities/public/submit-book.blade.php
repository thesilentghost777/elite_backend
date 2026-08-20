<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un livre — Elite 2.0</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: {
            elite: {
                blue: { 500:'#3b82f6',600:'#2563eb',800:'#1e3a5f',900:'#0f2440' },
                green: { 400:'#34d399',500:'#10b981',600:'#059669' },
            }
        }}}}
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Figtree', system-ui, sans-serif; }
        .gradient-elite { background: linear-gradient(135deg, #1e3a5f 0%, #064e3b 100%); }
        .upload-zone { border: 2px dashed #cbd5e1; transition: all 0.3s; }
        .upload-zone:hover, .upload-zone.dragover { border-color: #10b981; background: #ecfdf5; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="gradient-elite text-white py-8 px-4 text-center">
        <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-book-open text-2xl text-emerald-300"></i>
        </div>
        <h1 class="text-2xl font-black">Bibliothèque Elite 2.0</h1>
        <p class="text-white/70 text-sm mt-1">Partagez un livre PDF avec la communauté</p>
    </div>

    @if(session('success'))
    <div class="max-w-lg mx-auto px-4 mt-4">
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm"><i class="fas fa-check-circle mr-1"></i>{{ session('success') }}</div>
    </div>
    @endif

    @if($errors->any())
    <div class="max-w-lg mx-auto px-4 mt-4">
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    </div>
    @endif

    <div class="max-w-lg mx-auto px-4 py-6">
        <form action="{{ route('public.submit.book') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border p-5 space-y-4">
            @csrf
            <input name="titre" placeholder="Titre du livre *" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
            <input name="auteur" placeholder="Auteur" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
            <textarea name="description" placeholder="Description courte" rows="3" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none resize-none"></textarea>
            <select name="categorie" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none text-gray-600">
                <option value="">Catégorie *</option>
                <option value="entrepreneuriat">Entrepreneuriat</option>
                <option value="informatique">Informatique</option>
                <option value="marketing">Marketing</option>
                <option value="comptabilite">Comptabilité</option>
                <option value="gestion">Gestion</option>
                <option value="droit">Droit</option>
                <option value="sciences">Sciences</option>
                <option value="langues">Langues</option>
                <option value="developpement_personnel">Développement personnel</option>
                <option value="commerce">Commerce</option>
                <option value="autre">Autre</option>
            </select>
            <input name="nombre_pages" type="number" placeholder="Nombre de pages" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">

            <div class="upload-zone rounded-xl p-6 text-center cursor-pointer" id="pdf-zone" onclick="document.getElementById('pdf-input').click()">
                <i class="fas fa-file-pdf text-3xl text-gray-400 mb-2"></i>
                <p class="text-sm text-gray-500" id="pdf-label">Cliquer pour sélectionner le PDF * (max 20MB)</p>
                <input type="file" name="fichier_pdf" id="pdf-input" accept=".pdf" required class="hidden" onchange="document.getElementById('pdf-label').textContent=this.files[0]?.name || 'Cliquer pour sélectionner'">
            </div>

            <div class="upload-zone rounded-xl p-6 text-center cursor-pointer" onclick="document.getElementById('cover-input').click()">
                <i class="fas fa-image text-3xl text-gray-400 mb-2"></i>
                <p class="text-sm text-gray-500" id="cover-label">Image de couverture (optionnel)</p>
                <input type="file" name="cover_image" id="cover-input" accept="image/*" class="hidden" onchange="document.getElementById('cover-label').textContent=this.files[0]?.name || 'Image de couverture'">
            </div>

            <button type="submit" class="w-full gradient-elite text-white font-bold py-3 rounded-xl hover:opacity-90 transition text-sm">
                <i class="fas fa-upload mr-2"></i>Ajouter à la bibliothèque
            </button>
        </form>
    </div>
</body>
</html>
