<?php
namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Plat;
use App\Models\Boisson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommandeController extends Controller
{
    private function calculerDistance($lat1, $lon1, $lat2, $lon2)
{
    $rayon = 6371; // km
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $rayon * $c;
}



    // 🔹 1. Passer une commande
    public function store(Request $request)
    {
        $request->validate([
            'articles' => 'required|array|min:1',
            'articles.*.type' => 'required|in:plat,boisson',
            'articles.*.id' => 'required|integer',
            'articles.*.quantite' => 'required|integer|min:1',
            // Champs de livraison
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'adresse_livraison' => 'nullable|string',
            'commentaire' => 'nullable|string',
        ]);

        $user = $request->user();
        $total = 0;
        $fraisLivraison = 0;
        // Coordonnées fixes du restaurant (à adapter si besoin)
        $lat_restaurant = 6.202498662229028;
        $lon_restaurant = 1.1944887730143288;
        // Distance client ↔ restaurant
        $distance = $this->calculerDistance(
        $lat_restaurant,
        $lon_restaurant,
        $request->latitude,
        $request->longitude
    );

    // Frais de livraison à 125 FCFA/km
    $fraisLivraison = ceil(($distance * 125)*2);
        DB::beginTransaction();

        try {
            $commande = Commande::create([
                'user_id' => $user->id,
                'statut' => 'en_attente',
                'montant_total' => 0,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'adresse_livraison' => $request->adresse_livraison,
                'frais_livraison' => $fraisLivraison,
                'commentaire' => $request->commentaire,
            ]);

            foreach ($request->articles as $article) {
                $quantite = $article['quantite'];
                
                if ($article['type'] === 'plat') {
                    $plat = Plat::findOrFail($article['id']);
                    $commande->plats()->attach($plat->id, [
                        'quantite' => $quantite,
                        'boisson_id' => null,
                    ]);
                    $total += $plat->prix * $quantite;

                } elseif ($article['type'] === 'boisson') {
                    $boisson = Boisson::findOrFail($article['id']);
                    // on utilise plat_id = null, boisson_id rempli
                    DB::table('commande_plat')->insert([
                        'commande_id' => $commande->id,
                        'plat_id' => null,
                        'boisson_id' => $boisson->id,
                        'quantite' => $quantite,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $total += $boisson->prix * $quantite;
                }
            }

            // Total final = prix des articles + frais de livraison
            $commande->montant_total = $total + $fraisLivraison;
            $commande->save();
            $commande->refresh(); // recharge les données en base
            $distanceKm = round($distance, 2); // distance formatée

            $articlesDetails = [];

            foreach ($request->articles as $article) {
                $quantite = $article['quantite'];

                if ($article['type'] === 'plat') {
                    $plat = Plat::find($article['id']);
                    if ($plat) {
                        $articlesDetails[] = [
                            'nom' => $plat->nom,
                            'type' => 'plat',
                            'quantite' => $quantite,
                            'prix_unitaire' => number_format($plat->prix, 0, '', ' ') . ' FCFA',
                            'total' => number_format($plat->prix * $quantite, 0, '', ' ') . ' FCFA',
                        ];
                    }
                } elseif ($article['type'] === 'boisson') {
                    $boisson = Boisson::find($article['id']);
                    if ($boisson) {
                        $articlesDetails[] = [
                            'nom' => $boisson->nom,
                            'type' => 'boisson',
                            'quantite' => $quantite,
                            'prix_unitaire' => number_format($boisson->prix, 0, '', ' ') . ' FCFA',
                            'total' => number_format($boisson->prix * $quantite, 0, '', ' ') . ' FCFA',
                        ];
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Commande passée avec succès',
                'commande' => [
                    'id' => $commande->id,
                    'statut' => $commande->statut,
                    'date' => $commande->created_at->format('d/m/Y à H:i'),
                    'distance_estimee' => $distanceKm . ' km',
                    'montant_des_articles' => number_format($total, 0, '', ' ') . ' FCFA',
                    'frais_livraison' => number_format($fraisLivraison, 0, '', ' ') . ' FCFA',
                    'montant_total' => number_format($commande->montant_total, 0, '', ' ') . ' FCFA',
                    'adresse_livraison' => $commande->adresse_livraison,
                    'commentaire' => $commande->commentaire,
                    'articles' => $articlesDetails
                ]
            ], 201);
            

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erreur lors de la commande', 'erreur' => $e->getMessage()], 500);
        }
    }

    // 🔹 2. Voir toutes ses commandes
    public function index(Request $request)
    {
        $user = $request->user();
        $commandes = Commande::where('user_id', $user->id)->with('plats')->orderBy('created_at', 'desc')->get();

        return response()->json($commandes);
    }

    // 🔹 3. Détail d’une commande
    public function show($id)
    {
        $commande = Commande::with('plats')->findOrFail($id);
        return response()->json($commande);
    }
}
