<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boisson extends Model
{
    protected $fillable = [
        'nom',
        'categorie',
        'description',
        'prix',
        'image',
    ];
    // Pour exposer l’URL de l’image
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
    public function commandes()
    {
        return $this->belongsToMany(Commande::class, 'commande_plat')
                    ->withPivot('quantite', 'plat_id');
    }

}
