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
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    

    public function livraison() {
        return $this->hasOne(Livraison::class);
    }
    public function client()
{
    return $this->belongsTo(User::class, 'user_id');
}

public function plats()
{
    return $this->belongsToMany(Plat::class, 'commande_plat')->withPivot('quantite', 'boisson_id');
}

}
