<?php

namespace App\Http\Controllers;

use App\Models\Financement;
use App\Models\Bibliotheque;
use App\Models\CommunityGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpportunityController extends Controller
{
    // ─── VUES PUBLIQUES : SOUMISSION ───

    public function publicSubmitForm()
    {
        return view('opportunities.public.submit');
    }

    public function publicSubmitJob(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'entreprise' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'type_contrat' => 'required|string|max:100',
            'salaire_min' => 'nullable|numeric|min:0',
            'salaire_max' => 'nullable|numeric|min:0',
            'date_limite' => 'nullable|date',
            'contact_email' => 'nullable|email',
            'contact_telephone' => 'nullable|string|max:20',
        ]);

        DB::table('job_offers')->insert(array_merge($validated, [
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return back()->with('success', 'Offre d\'emploi soumise avec succès ! Elle sera visible après validation.');
    }

    public function publicSubmitConcours(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'organisateur' => 'required|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'date_limite_inscription' => 'required|date',
            'conditions' => 'nullable|string',
            'lien_inscription' => 'nullable|url',
        ]);

        DB::table('concours')->insert(array_merge($validated, [
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return back()->with('success', 'Concours soumis avec succès !');
    }

    public function publicSubmitFinancement(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'organisme' => 'required|string|max:255',
            'type' => 'required|in:bourse,subvention,pret,investissement,autre',
            'montant_min' => 'nullable|numeric|min:0',
            'montant_max' => 'nullable|numeric|min:0',
            'date_limite' => 'nullable|date',
            'conditions_eligibilite' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_telephone' => 'nullable|string|max:20',
        ]);

        Financement::create($validated);

        return back()->with('success', 'Offre de financement soumise avec succès !');
    }

    public function publicSubmitBook(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'auteur' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'categorie' => 'required|in:entrepreneuriat,informatique,marketing,comptabilite,gestion,droit,sciences,langues,developpement_personnel,commerce,autre',
            'fichier_pdf' => 'required|file|mimes:pdf|max:20480',
            'cover_image' => 'nullable|image|max:2048',
            'nombre_pages' => 'nullable|integer|min:1',
        ]);

        $pdfPath = $request->file('fichier_pdf')->store('bibliotheque/pdfs', 'public');
        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('bibliotheque/covers', 'public');
        }

        Bibliotheque::create(array_merge($validated, [
            'fichier_pdf' => $pdfPath,
            'cover_image' => $coverPath,
        ]));

        return back()->with('success', 'Livre ajouté à la bibliothèque avec succès !');
    }

    public function publicBookForm()
    {
        return view('opportunities.public.submit-book');
    }

    // ─── VUES ELITE USERS : CONSULTATION ───

    public function emploisConcours()
    {
        $emplois = DB::table('job_offers')->where('active', true)->orderByDesc('created_at')->get();
        $concours = DB::table('concours')->where('active', true)->orderByDesc('created_at')->get();

        return view('opportunities.elite.emplois-concours', compact('emplois', 'concours'));
    }

    public function financements()
    {
        $financements = Financement::active()->orderByDesc('created_at')->get();
        return view('opportunities.elite.financements', compact('financements'));
    }

    public function bibliotheque(Request $request)
    {
        $query = Bibliotheque::active();
        if ($request->filled('categorie')) {
            $query->categorie($request->categorie);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('auteur', 'like', "%{$search}%");
            });
        }
        $livres = $query->orderByDesc('created_at')->paginate(12);
        return view('opportunities.elite.bibliotheque', compact('livres'));
    }

    public function downloadBook(Bibliotheque $livre)
    {
        $livre->incrementTelechargements();
        return response()->download(storage_path('app/public/' . $livre->fichier_pdf));
    }

    public function viewBook(Bibliotheque $livre)
    {
        $livre->incrementVues();
        return view('opportunities.elite.view-book', compact('livre'));
    }

    public function communaute()
    {
        $groups = CommunityGroup::active()->orderBy('nom')->get();
        return view('opportunities.elite.communaute', compact('groups'));
    }
}
