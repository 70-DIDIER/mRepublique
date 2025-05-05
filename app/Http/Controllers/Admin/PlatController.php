<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer tous les plats
        $plats = Plat::all();
        return view('plats.index', compact('plats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Afficher le formulaire de création d'un plat
        return view('plats.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valider les données
        $request->validate([
            'nom' => 'required|string',
            'categorie' => 'required|string|nullable',
            'description' => 'nullable|string',
            'prix' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
        ]);

        // Gérer l'image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        // Créer le plat
        $plat = Plat::create([
            'nom' => $request->nom,
            'categorie' => $request->categorie,
            'description' => $request->description,
            'prix' => $request->prix,
            'image' => $imagePath,
        ]);

        return redirect()->route('plats.index')->with('success', 'Plat ajouté avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Afficher un plat spécifique
        $plat = Plat::findOrFail($id);
        return view('plats.show', compact('plat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Afficher le formulaire d'édition d'un plat
        $plat = Plat::findOrFail($id);
        return view('plats.update', compact('plat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Valider les données
        $request->validate([
            'nom' => 'sometimes|string',
            'categorie' => 'sometimes|string|nullable',
            'description' => 'sometimes|nullable|string',
            'prix' => 'sometimes|numeric',
            'image' => 'nullable|image|max:2048',
        ]);

        // Trouver le plat à mettre à jour
        $plat = Plat::findOrFail($id);

        // Gérer l'image
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $plat->image = $imagePath;
        }

        // Mettre à jour le plat
        $plat->update($request->only(['nom', 'categorie', 'description', 'prix']));

        return redirect()->route('plats.index')->with('success', 'Plat mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Supprimer un plat
        $plat = Plat::findOrFail($id);
        $plat->delete();

        return redirect()->route('plats.index')->with('success', 'Plat supprimé avec succès');
    }
}
