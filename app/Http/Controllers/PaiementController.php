<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Paiement;
use Illuminate\Http\Request;
use App\Services\PayGateService;
use Illuminate\Support\Facades\Log;

class PaiementController extends Controller
{

public function store(Request $request)
{
    $request->validate([
        'commande_id' => 'required|exists:commandes,id',
        'methode' => 'required|in:flooz,tmoney',
        'telephone' => 'required|string',
    ]);

    $commande = Commande::findOrFail($request->commande_id);

    // Vérifier si paiement déjà enregistré
    if ($commande->paiement) {
        return response()->json(['message' => 'Paiement déjà effectué ou en cours pour cette commande.'], 400);
    }

    $network = strtoupper($request->methode); // FLOOZ ou TMONEY
    $description = "Paiement commande #" . $commande->id;
    $identifier = 'CMD-' . $commande->id . '-' . time(); // identifiant unique

    // Appel API PayGate
    $paygate = app(PayGateService::class);
    $response = $paygate->demanderPaiement(
        $request->telephone,
        $commande->montant_total,
        $description,
        $identifier,
        $network
    );

    // Analyse de la réponse
    if (!isset($response['tx_reference']) || $response['status'] != 0) {
        return response()->json([
            'message' => 'Échec lors de l\'initialisation du paiement.',
            'paygate_response' => $response
        ], 400);
    }

    // Stockage du paiement
    $paiement = Paiement::create([
        'commande_id' => $commande->id,
        'methode' => $request->methode,
        'statut' => 'en_attente',
        'transaction_id' => $response['tx_reference'],
    ]);

    return response()->json([
        'message' => 'Paiement lancé. En attente de confirmation.',
        'paiement' => $paiement,
        'paygate' => $response
    ], 201);
}

public function check($paiementId)
{
    $paiement = Paiement::findOrFail($paiementId);

    if (!$paiement->transaction_id) {
        return response()->json(['message' => 'Aucune transaction associée à ce paiement.'], 400);
    }

    // Appel à PayGate pour vérifier le statut
    $response = app(PayGateService::class)
                ->verifierPaiement($paiement->transaction_id);

    Log::info('Réponse PayGate check', $response);

    // Vérifie si le paiement est réussi (status == 0)
    if (isset($response['status']) && $response['status'] == 0) {
        $paiement->update([
            'statut' => 'effectue',
            'transaction_id' => $response['tx_reference'],
        ]);

        $commande = $paiement->commande;
        Log::info('Avant update check', ['commande_id' => $commande->id ?? null, 'est_paye' => $commande->est_paye ?? null]);
        if ($commande && !$commande->est_paye) {
            $commande->est_paye = true;
            $commande->save();
            $commande->refresh();
            Log::info('Après update check', ['commande_id' => $commande->id ?? null, 'est_paye' => $commande->est_paye ?? null]);
        }

        return response()->json([
            'message' => 'Paiement confirmé avec succès.',
            'paiement' => $paiement,
            'etat' => $response
        ]);
    }

    return response()->json([
        'message' => 'Paiement non confirmé.',
        'etat' => $response
    ]);
}
public function callback(Request $request)
{
    $data = $request->all();
    Log::info('Callback reçu', $data);

    $paiement = Paiement::where('transaction_id', $data['tx_reference'] ?? null)->first();

    if ($paiement && $data['status'] == 0) {
        $paiement->update([
            'statut' => 'effectue',
            'transaction_id' => $data['tx_reference'],
        ]);
        $commande = $paiement->commande;
        Log::info('Avant update callback', ['commande_id' => $commande->id ?? null, 'est_paye' => $commande->est_paye ?? null]);
        $commande->update(['est_paye' => true]);
        $commande->refresh();
        Log::info('Après update callback', ['commande_id' => $commande->id ?? null, 'est_paye' => $commande->est_paye ?? null]);
    }

    return response()->json(['message' => 'Callback reçu.']);
}


}
