<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Boisson;
use App\Services\ImageService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class BoissonController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    // Liste de toutes les boissons
    public function index(){
        $boissons = Boisson::all();
        return view('boissons.index', compact('boissons'));
    }
    public function create(){
        return view('boissons.create');
    }    
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
                    $image = $this->imageService->optimize($request->file('image'));
                    $imagePath = 'images/' . time() . '.jpg';
                    $this->imageService->save($image, storage_path('app/public/' . $imagePath));
                }
                $boisson = Boisson::create([
                    'nom' => $request->nom,
                    'categorie' => $request->categorie,
                    'description' => $request->description,
                    'prix' => $request->prix,
                    'image' => $imagePath,
                ]);
                return redirect()->route('boissons.index')->with('success', 'Boisson ajoutée avec succès');
            } catch (Exception $e) {
                return redirect()->route('boissons.index')->with('error', 'Erreur lors de l\'ajout de la boisson');
            }

    }
    // afficher une boisson
    public function show($id){
        $boisson = Boisson::findOrFail($id);
        return view('boissons.show', compact('boisson'));
    }


    public function edit(string $id)
    {
        // Afficher le formulaire d'édition d'un plat
        $boissons = Boisson::findOrFail($id);
        return view('boissons.update', compact('boissons'));
    }
    // modifier une boisson
    public function update(Request $request, string $id){
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

            $image = $this->imageService->optimize($request->file('image'));
            $imagePath = 'images/' . time() . '.jpg';
            $this->imageService->save($image, storage_path('app/public/' . $imagePath));
            $data['image'] = $imagePath;
        }

        // Mettre à jour la boisson
        $boisson->update($data);
        return redirect()->route('boissons.index')->with('success', 'Boisson mise à jour avec succès');
    }

    // supprimer une boisson
    public function destroy($id){
        $boisson = Boisson::findOrFail($id);
        // Supprimer l'image si elle existe
        if ($boisson->image) {
            Storage::disk('public')->delete($boisson->image);
        }
        $boisson->delete();
        return redirect()->route('boissons.index')->with('success', 'Boisson supprimée avec succès');
    }
}
