<?php

namespace App\Http\Controllers;

use App\Models\Livraison;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivraisonController extends Controller
{
    // 1. Prise en charge d'une livraison par un livreur
    public function prendre(Request $request, $commandeId)
    {
        $user = $request->user();

        if ($user->role !== 'livreur') {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $commande = Commande::findOrFail($commandeId);

        // Vérifie si déjà livrée
        if ($commande->livraison) {
            return response()->json(['message' => 'Cette commande est déjà assignée.'], 400);
        }

        $livraison = Livraison::create([
            'commande_id' => $commande->id,
            'livreur_id' => $user->id,
            'statut' => 'en_chemin',
            'code_validation' => random_int(1000, 9999), // ou null
        ]);

        return response()->json(['message' => 'Livraison prise en charge.', 'livraison' => $livraison]);
    }

    // 2. Marquer une livraison comme livrée
    public function livrer(Request $request, $id)
    {
        $livraison = Livraison::findOrFail($id);

        if ($request->user()->id !== $livraison->livreur_id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $request->validate([
            'code_validation' => 'required'
        ]);

        if ($livraison->code_validation != $request->code_validation) {
            return response()->json(['message' => 'Code de validation incorrect.'], 400);
        }

        $livraison->update([
            'statut' => 'livree',
        ]);

        return response()->json(['message' => 'Commande livrée avec succès.', 'livraison' => $livraison]);
    }

    // 3. Liste des livraisons du livreur connecté
    public function mesLivraisons(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'livreur') {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $livraisons = Livraison::where('livreur_id', $user->id)->with('commande')->get();

        return response()->json($livraisons);
    }

    // 4. Admin : voir toutes les livraisons
    public function toutes()
    {
        $livraisons = Livraison::with(['commande', 'livreur'])->get();
        return response()->json($livraisons);
    }
}
