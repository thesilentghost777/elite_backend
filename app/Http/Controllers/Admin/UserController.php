<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EliteUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = EliteUser::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('ville')) {
            $query->where('ville', $request->ville);
        }

        if ($request->filled('diplome')) {
            $query->where('dernier_diplome', $request->diplome);
        }

        $users = $query->withCount(['packs', 'filleuls'])->latest()->paginate(20);
        $villes = EliteUser::distinct()->pluck('ville')->filter();
        $diplomes = ['CEP', 'BEPC', 'Probatoire', 'BAC', 'Licence', 'Master', 'Doctorat'];

        return view('admin.users.index', compact('users', 'villes', 'diplomes'));
    }

    public function show(EliteUser $user)
    {
        $user->load(['packs.pack', 'transactions', 'profileChoice.profile', 'filleuls', 'parrain']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(EliteUser $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, EliteUser $user)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'nullable|email|unique:elite_users,email,' . $user->id,
            'telephone' => 'required|string|unique:elite_users,telephone,' . $user->id,
            'ville' => 'required|string',
            'dernier_diplome' => 'required|string',
            'solde_points' => 'required|numeric|min:0',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)->with('success', 'Utilisateur mis à jour avec succès');
    }

    public function addPoints(Request $request, EliteUser $user)
    {
        $validated = $request->validate([
            'points' => 'required|numeric|min:0.01',
            'motif' => 'required|string|max:255',
        ]);

        $user->addPoints($validated['points']);

        $user->transactions()->create([
            'type' => 'bonus_admin',
            'points' => $validated['points'],
            'reference' => 'ADMIN-' . strtoupper(uniqid()),
            'description' => $validated['motif'],
            'statut' => 'complete',
        ]);

        return back()->with('success', $validated['points'] . ' points ajoutés avec succès');
    }

    public function destroy(EliteUser $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé');
    }
}
