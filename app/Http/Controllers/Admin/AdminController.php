<?php

namespace App\Http\Controllers\Admin;
use App\Models\Plat;
use App\Models\User;
use App\Models\Commande;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index(){
        return view('admin.dashboard');
    }
    public function dashboard()
{
    $nbClients = User::where('role', 'client')->count();
    $nbPlatsCommandes = Commande::with('plats')->get()->pluck('plats')->flatten()->count();
    $dernieresCommandes = Commande::with(['user', 'plats'])
        ->latest()
        ->take(3)
        ->get();
    $platPopulaire = Plat::select('plats.*')
        ->join('commande_plat', 'plats.id', '=', 'commande_plat.plat_id')
        ->selectRaw('plats.*, SUM(commande_plat.quantite) as total')
        ->groupBy('plats.id')
        ->orderByDesc('total')
        ->first();

    return view('admin.dashboard', compact('nbClients', 'nbPlatsCommandes', 'platPopulaire', 'dernieresCommandes'));
}

}
