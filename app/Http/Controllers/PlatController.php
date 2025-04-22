<?php
namespace App\Http\Controllers;

use App\Models\Plat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlatController extends Controller
{
    // Liste de tous les plats
    public function index()
    {
        return Plat::all();
    }

    // Afficher un plat spécifique
    public function show($id)
    {
        return Plat::findOrFail($id);
    }

    // Ajouter un nouveau plat
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'description' => 'nullable|string',
            'prix' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        $plat = Plat::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'prix' => $request->prix,
            'image' => $imagePath,
        ]);

        return response()->json($plat, 201);
    }

    // Modifier un plat
    public function update(Request $request, $id)
    {
        $plat = Plat::findOrFail($id);
    
        $request->validate([
            'nom' => 'sometimes|string',
            'description' => 'sometimes|nullable|string',
            'prix' => 'sometimes|numeric',
            'image' => 'nullable|image|max:2048',
        ]);
    
        // Initialiser les données à mettre à jour
        $data = [];
    
        if ($request->has('nom')) {
            $data['nom'] = $request->input('nom');
        }
    
        if ($request->has('description')) {
            $data['description'] = $request->input('description');
        }
    
        if ($request->has('prix')) {
            $data['prix'] = $request->input('prix');
        }
    
        if ($request->hasFile('image')) {
            // Supprimer l’ancienne image si elle existe
            if ($plat->image) {
                Storage::disk('public')->delete($plat->image);
            }
    
            // Enregistrer la nouvelle image
            $data['image'] = $request->file('image')->store('images', 'public');
        }
    
        // Appliquer la mise à jour
        if (!empty($data)) {
            $plat->update($data);
        }
    
        return response()->json($plat->fresh(), 200);
    }
    


    // Supprimer un plat
    public function destroy($id)
    {
        $plat = Plat::findOrFail($id);

        // Supprimer l’image si existe
        if ($plat->image) {
            Storage::disk('public')->delete($plat->image);
        }

        $plat->delete();

        return response()->json(['message' => 'Plat supprimé']);
    }
}
