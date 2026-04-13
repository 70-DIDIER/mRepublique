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
            'methode'     => 'required|in:flooz,tmoney',
            'telephone'   => 'required|string',
        ]);

        $commande = Commande::findOrFail($request->commande_id);

        if ($commande->paiement) {
            return response()->json(['message' => 'Paiement déjà effectué ou en cours pour cette commande.'], 400);
        }

        // Identifiant unique lié à la commande (pour retrouver via v2/status)
        $identifier = 'CMD-' . $commande->id . '-' . time();

        $response = app(PayGateService::class)->demanderPaiement(
            $request->telephone,
            $commande->montant_total,
            'Paiement commande #' . $commande->id,
            $identifier,
            strtoupper($request->methode)
        );

        if (!isset($response['tx_reference']) || $response['status'] != 0) {
            return response()->json([
                'message'          => 'Échec lors de l\'initialisation du paiement.',
                'paygate_response' => $response,
            ], 400);
        }

        $paiement = Paiement::create([
            'commande_id'    => $commande->id,
            'methode'        => $request->methode,
            'statut'         => 'en_attente',
            'transaction_id' => $response['tx_reference'],
            'identifier'     => $identifier,
        ]);

        return response()->json([
            'message'  => 'Paiement lancé. En attente de confirmation.',
            'paiement' => $paiement,
            'paygate'  => $response,
        ], 201);
    }

    /**
     * Callback reçu de PayGate après paiement client.
     * Champs reçus : tx_reference, identifier, payment_reference, amount, datetime, payment_method, phone_number
     * IMPORTANT : pas de champ "status" dans le callback — on vérifie TOUJOURS via l'API PayGate.
     */
    public function callback(Request $request)
    {
        $data = $request->all();
        Log::info('Callback PayGate reçu', ['tx_reference' => $data['tx_reference'] ?? null, 'identifier' => $data['identifier'] ?? null]);

        // 1. Identifier le paiement (par tx_reference ou identifier)
        $paiement = null;

        if (!empty($data['tx_reference'])) {
            $paiement = Paiement::where('transaction_id', $data['tx_reference'])->first();
        }

        if (!$paiement && !empty($data['identifier'])) {
            $paiement = Paiement::where('identifier', $data['identifier'])->first();
        }

        if (!$paiement) {
            Log::warning('Callback PayGate : paiement introuvable', $data);
            return response()->json(['message' => 'Paiement introuvable.'], 404);
        }

        // 2. Idempotence — ne pas traiter deux fois
        if ($paiement->statut === 'effectue') {
            return response()->json(['message' => 'Déjà traité.'], 200);
        }

        // 3. Vérification indépendante via l'API PayGate (ne jamais faire confiance au callback seul)
        $paygate   = app(PayGateService::class);
        $verification = $paygate->verifierPaiementParIdentifier($paiement->identifier);

        Log::info('Vérification PayGate callback', ['identifier' => $paiement->identifier, 'response' => $verification]);

        if (!isset($verification['status']) || $verification['status'] !== 0) {
            Log::warning('Callback rejeté : paiement non confirmé par PayGate', [
                'identifier' => $paiement->identifier,
                'status'     => $verification['status'] ?? 'N/A',
            ]);
            return response()->json(['message' => 'Paiement non confirmé.'], 200);
        }

        // 4. Tout est bon — on valide
        $paiement->update([
            'statut'         => 'effectue',
            'transaction_id' => $verification['tx_reference'] ?? $paiement->transaction_id,
        ]);

        $commande = $paiement->commande;
        if ($commande && !$commande->est_paye) {
            $commande->update(['est_paye' => true]);
            Log::info('Commande marquée payée via callback', ['commande_id' => $commande->id]);
        }

        return response()->json(['message' => 'Paiement confirmé avec succès.'], 200);
    }

    /**
     * Vérification manuelle depuis le client (polling).
     */
    public function check($paiementId)
    {
        $paiement = Paiement::findOrFail($paiementId);

        if (!$paiement->transaction_id) {
            return response()->json(['message' => 'Aucune transaction associée.'], 400);
        }

        $response = app(PayGateService::class)->verifierPaiement($paiement->transaction_id);

        if (isset($response['status']) && $response['status'] === 0) {
            $paiement->update([
                'statut'         => 'effectue',
                'transaction_id' => $response['tx_reference'] ?? $paiement->transaction_id,
            ]);

            $commande = $paiement->commande;
            if ($commande && !$commande->est_paye) {
                $commande->update(['est_paye' => true]);
            }

            return response()->json([
                'message'  => 'Paiement confirmé.',
                'paiement' => $paiement->fresh(),
            ]);
        }

        return response()->json([
            'message' => 'Paiement non confirmé.',
            'etat'    => $response,
        ]);
    }
}
