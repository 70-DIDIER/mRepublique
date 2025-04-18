<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Livraison extends Model
{
    use HasFactory;

    protected $fillable = [
        'commande_id',
        'livreur_id',
        'statut',
        'code_validation',
    ];

    public function commande() {
        return $this->belongsTo(Commande::class);
    }

    public function livreur() {
        return $this->belongsTo(User::class, 'livreur_id');
    }
}
