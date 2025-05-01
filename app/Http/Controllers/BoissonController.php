<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Boisson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BoissonController extends Controller
{
    // Liste de toutes les boissons
    public function index(){
        return Boisson::all();
    }
    // enregistrer une boisson
    
    public function store(Request $request){
        try{ 
                $request->validate([
                    'nom' => 'required|string',
                    'categorie' => 'required|string|nullable',
                    'description' => 'nullable|string',
                    'prix' => 'required|numeric',
                    'image' => 'nullable|image|max:2048',
                ]);
                $imagePath = null;
                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('images', 'public');
                }
                $boisson = Boisson::create([
                    'nom' => $request->nom,
                    'categorie' => $request->categorie,
                    'description' => $request->description,
                    'prix' => $request->prix,
                    'image' => $imagePath,
                ]);
                return response()->json($boisson, 201);
            } catch (Exception $e) {
                return response()->json($e);
            }

    }
    // afficher une boisson
    public function show($id){
        return Boisson::findOrFail($id);
    }
    // modifier une boisson
    public function update(Request $request, $id){
        $boisson = Boisson::findOrFail($id);
        $request->validate([
            'nom' => 'sometimes|string',
            'categorie' => 'sometimes|string|nullable',
            'description' => 'sometimes|nullable|string',
            'prix' => 'sometimes|numeric',
            'image' => 'nullable|image|max:2048',
        ]);
        // Initialiser les données à mettre à jour
        $data = [];
        if ($request->has('nom')) {
            $data['nom'] = $request->nom;
        }
        if ($request->has('categorie')) {
            $data['categorie'] = $request->categorie;
        }
        if ($request->has('description')) {
            $data['description'] = $request->description;
        }
        if ($request->has('prix')) {
            $data['prix'] = $request->prix;
        }
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($boisson->image) {
                Storage::disk('public')->delete($boisson->image);
            }
            // Enregistrer la nouvelle image
            $data['image'] = $request->file('image')->store('images', 'public');
        }
        // Mettre à jour la boisson
        $boisson->update($data);
        return response()->json($boisson, 200);
    }
    // supprimer une boisson
    public function destroy($id){
        $boisson = Boisson::findOrFail($id);
        // Supprimer l'image si elle existe
        if ($boisson->image) {
            Storage::disk('public')->delete($boisson->image);
        }
        $boisson->delete();
        return response()->json(['message' => 'Boisson supprimé avec succès'], 200);
    }
}
