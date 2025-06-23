<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'commande_id',
        'methode',
        'statut',
        'transaction_id',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }
    public function getStatutAttribute($value)
    {
        return $value == 'en_attente' ? 'En attente' : 'Confirmé';
    }
    public function getMethodeAttribute($value)
    {
        return $this->attributes['methode'] = $value == 'flooz' ? 'Flooz' : 'TMoney';
    }
    public function getCreatedAtAttribute($value)
    {
        return $this->attributes['created_at'] = \Carbon\Carbon::parse($value)->format('d/m/Y H:i:s');
    }
    public function getUpdatedAtAttribute($value)
    {
        return $this->attributes['updated_at'] = \Carbon\Carbon::parse($value)->format('d/m/Y H:i:s');
    }
    public function getTransactionIdAttribute($value)
    {
        return $this->attributes['transaction_id'] = $value;
    }
    public function getCommandeIdAttribute($value)
    {
        return $this->attributes['commande_id'] = $value;
    }
}
