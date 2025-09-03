@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h5 class="card-title">Nombre de clients</h5>
                    <p class="display-4 text-primary">{{ $nbClients }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h5 class="card-title">Nombre de plats commandés</h5>
                    <p class="display-4 text-success">{{ $nbPlatsCommandes }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h5 class="card-title">Plat le plus commandé</h5>
                    <p class="h4 text-success">{{ $platPopulaire ? $platPopulaire->nom : 'Aucun' }}</p>
                    <span class="badge  text-success">Commandé {{ $platPopulaire ? $platPopulaire->total : 0 }} fois</span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Accordéon des 3 dernières commandes -->
    <div class="row mt-5">
        <div class="col-12">
            <h4 class="mb-3">Dernières commandes</h4>
            <div class="accordion" id="commandesAccordion">
                @foreach($dernieresCommandes as $commande)
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header" id="heading{{ $commande->id }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $commande->id }}" aria-expanded="false" aria-controls="collapse{{ $commande->id }}">
                            Commande #{{ $commande->id }} - {{ $commande->created_at->format('d/m/Y H:i') }}
                        </button>
                    </h2>
                    <div id="collapse{{ $commande->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $commande->id }}" data-bs-parent="#commandesAccordion">
                        <div class="accordion-body">
                            <strong>Client :</strong> {{ $commande->user->name ?? '-' }}<br>
                            <strong>Adresse :</strong> {{ $commande->adresse_livraison ?? '-' }}<br>
                            <strong>Statut :</strong> {{ ucfirst($commande->statut) }}<br>
                            <strong>Total :</strong> {{ number_format($commande->total, 2) }} F<br>
                            <strong>Plats :</strong>
                            <ul>
                                @foreach($commande->plats as $plat)
                                    <li>{{ $plat->nom }} x {{ $plat->pivot->quantite }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endforeach
                @if($dernieresCommandes->isEmpty())
                    <div class="alert alert-info">Aucune commande récente.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection