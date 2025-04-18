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
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function plats() {
        return $this->belongsToMany(Plat::class)->withPivot('quantite');
    }

    public function livraison() {
        return $this->hasOne(Livraison::class);
    }
}
