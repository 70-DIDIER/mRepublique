<?php

namespace App\Http\Controllers\Admin;

use App\Models\Commande;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
    
    public function index()
    {
        // Coordonnées fixes du restaurant 
    $lat_restaurant = 6.202498662229028;
    $lon_restaurant = 1.1944887730143288;

    $commandes = Commande::with(['plats', 'boissons', 'user'])->orderBy('created_at', 'desc')->get()->map(function ($commande) use ($lat_restaurant, $lon_restaurant) {
        $distance = $this->calculerDistance(
            $lat_restaurant,
            $lon_restaurant,
            $commande->latitude,
            $commande->longitude
        );
        $commande->distance_estimee = round($distance, 2) . ' km';
        return $commande;
    });

    return view('commandes.index', compact('commandes'));
    }
}
