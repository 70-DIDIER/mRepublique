@extends('layouts.app')

@section('content')
<div class="container mt-4">
  <h2 class="mb-4" style="color: var(--primary-color)">Commandes Disponibles</h2>
  <div class="row">
    @foreach($commandes as $commande)
      <div class="col-md-6 col-lg-4 mb-4 d-flex align-items-stretch">
        <div class="card shadow h-100">
          <div class="card-header" style="background-color: var(--primary-color); color: #fff;">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Commande #{{ $commande->id }}</h5>
              @if($commande->est_paye)
                <span class="badge badge-success">Payé</span>
              @else
                <span class="badge badge-warning">Non payé</span>
              @endif
            </div>
          </div>
          <div class="card-body">
            <p><strong>Adresse :</strong> {{ $commande->adresse_livraison }}</p>
            <p><strong>Date :</strong> {{ $commande->created_at->format('d/m/Y à H:i') }}</p>
            <div class="mb-3">
              <h6 class="font-weight-bold">Plats :</h6>
              <ul class="list-unstyled">
                @foreach($commande->plats as $plat)
                  <li>
                    <i class="fas fa-utensils text-secondary"></i> {{ $plat->nom }}
                    <span class="text-muted">x{{ $plat->pivot->quantite }} ({{ number_format($plat->prix, 0, '', ' ') }} FCFA)</span>
                  </li>
                @endforeach
              </ul>
            </div>
            <div class="mb-3">
              <h6 class="font-weight-bold"><strong>Boissons :</strong></h6>
              <ul class="list-unstyled">
                @foreach($commande->boissons as $boisson)
                  <li>
                    <i class="fas fa-glass-martini-alt text-secondary"></i> {{ $boisson->nom }}
                    <span class="text-muted">x{{ $boisson->pivot->quantite }} ({{ number_format($boisson->prix, 0, '', ' ') }} FCFA)</span>
                  </li>
                @endforeach
              </ul>
            </div> 
            <p class="mb-0"><strong>Total :</strong> {{ number_format($commande->montant_total, 0, '', ' ') }} FCFA</p>
          </div>
          <div class="card-footer bg-white">
            <div class="d-flex justify-content-between">
              <a href="{{ route('commandes.show', $commande->id) }}" class="btn btn-sm btn-outline-primary">Détails</a>
              <a href="{{ route('commandes.edit', $commande->id) }}" class="btn btn-sm btn-outline-warning">Modifier</a>
              <form action="{{ route('commandes.destroy', $commande->id) }}" method="POST" onsubmit="return confirm('Supprimer cette commande ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection