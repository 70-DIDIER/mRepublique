@extends('layouts.app')

@section('content')
<div class="container mt-4">
  <div class="card shadow">
    <div class="card-header" style="background-color: var(--primary-color); color: #fff;">
      <h4 class="mb-0">Détails de la commande #{{ $commande->id }}</h4>
      @if($commande->est_paye)
        <span class="badge badge-success">Payé</span>
      @else
        <span class="badge badge-warning">Non payé</span>
      @endif
    </div>
    <div class="card-body">
      <p><strong>Adresse de livraison :</strong> {{ $commande->adresse_livraison }}</p>
      <p><strong>Date :</strong> {{ $commande->created_at->format('d/m/Y à H:i') }}</p>
      
      <div class="mb-3">
        <h5 class="font-weight-bold">Plats commandés</h5>
        <ul class="list-group list-group-flush">
          @foreach($commande->plats as $plat)
            <li class="list-group-item">
              <i class="fas fa-utensils text-secondary"></i> {{ $plat->nom }} 
              <span class="text-muted">x{{ $plat->pivot->quantite }} ({{ number_format($plat->prix, 0, '', ' ') }} FCFA)</span>
            </li>
          @endforeach
        </ul>
      </div>

      <div class="mb-3">
        <h5 class="font-weight-bold">Boissons commandées</h5>
        <ul class="list-group list-group-flush">
          @foreach($commande->boissons as $boisson)
            <li class="list-group-item">
              <i class="fas fa-glass-martini-alt text-secondary"></i> {{ $boisson->nom }} 
              <span class="text-muted">x{{ $boisson->pivot->quantite }} ({{ number_format($boisson->prix, 0, '', ' ') }} FCFA)</span>
            </li>
          @endforeach
        </ul>
      </div>
      
      @php
          $totalPlats = $commande->plats->sum(function($plat) {
              return $plat->prix * $plat->pivot->quantite;
          });
          $totalBoissons = $commande->boissons->sum(function($boisson) {
              return $boisson->prix * $boisson->pivot->quantite;
          });
          $sousTotal = $totalPlats + $totalBoissons;
      @endphp
      
      <p class="mb-0"><strong>Sous-total :</strong> {{ number_format($sousTotal, 0, '', ' ') }} FCFA</p>
      <p class="mb-0"><strong>Frais de livraison :</strong> {{ number_format($commande->frais_livraison, 0, '', ' ') }} FCFA</p>
      <p class="mb-0"><strong>Total :</strong> {{ number_format($commande->montant_total, 0, '', ' ') }} FCFA</p>
    </div>
    <div class="card-footer bg-white">
      <div class="d-flex justify-content-between">
        <a href="{{ route('commandes.index') }}" class="btn btn-outline-primary">Retour</a>
      </div>
    </div>
  </div>
</div>
@endsection