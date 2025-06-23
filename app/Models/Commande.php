<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'statut',
        'total',
        'type_livraison',
        'latitude',
        'longitude',
        'adresse_livraison',
        'frais_livraison',
        'commentaire',
        'est_paye',
    ];

    protected $casts = [
        'est_paye' => 'boolean',
    ];

    public function plats()
    {
        // Relation pour les plats commandés (où la colonne boisson_id est null)
        return $this->belongsToMany(Plat::class, 'commande_plat', 'commande_id', 'plat_id')
                    ->withPivot('quantite')
                    ->whereNull('commande_plat.boisson_id');
    }

    public function boissons()
    {
        // Relation pour les boissons commandées (où la colonne boisson_id n'est pas null)
        return $this->belongsToMany(Boisson::class, 'commande_plat', 'commande_id', 'boisson_id')
                    ->withPivot('quantite')
                    ->whereNotNull('commande_plat.boisson_id');
    }
    public function user()
    {
    return $this->belongsTo(\App\Models\User::class);
    }

    public function paiement()
    {
        return $this->hasOne(\App\Models\Paiement::class);
    }
}
