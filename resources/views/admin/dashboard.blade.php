@extends('layouts.app')

@section('content')
<style>
    .dashboard-header {
        background: #f8f9fa;
        padding: 2rem 0 1rem 0;
        margin-bottom: 2rem;
        border-bottom: 3px solid #e9ecef;
    }
    
    .dashboard-title {
        color: #2c3e50;
        font-weight: 700;
        font-size: 1.8rem;
        margin: 0;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        height: 100%;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin: 0 auto 1rem auto;
        background: #f8f9fa;
        color: #495057;
    }
    
    .stat-title {
        color: #6c757d;
        font-size: 0.95rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
    }
    
    .stat-value {
        color: #2c3e50;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        line-height: 1;
    }
    
    .stat-badge {
        display: inline-block;
        background: #f8f9fa;
        color: #495057;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 0.75rem;
    }
    
    .plat-name {
        color: #2c3e50;
        font-size: 1.3rem;
        font-weight: 600;
        margin: 0.5rem 0;
    }
    
    .section-header {
        background: white;
        padding: 1.5rem;
        border-radius: 12px 12px 0 0;
        border: 1px solid #e9ecef;
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 0;
    }
    
    .section-title {
        color: #2c3e50;
        font-weight: 700;
        font-size: 1.3rem;
        margin: 0;
    }
    
    .commandes-container {
        background: white;
        border-radius: 0 0 12px 12px;
        border: 1px solid #e9ecef;
        border-top: none;
        padding: 1.5rem;
    }
    
    .accordion-item {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 1rem;
        background: white;
    }
    
    .accordion-button {
        background: #f8f9fa;
        color: #2c3e50;
        font-weight: 600;
        padding: 1.25rem 1.5rem;
        border: none;
    }
    
    .accordion-button:not(.collapsed) {
        background: #e9ecef;
        color: #2c3e50;
        box-shadow: none;
    }
    
    .accordion-button:focus {
        box-shadow: none;
        border: none;
    }
    
    .accordion-button::after {
        background-size: 1.2rem;
    }
    
    .accordion-body {
        padding: 1.5rem;
        background: white;
        color: #495057;
        line-height: 1.8;
    }
    
    .commande-info {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .info-item {
        flex: 1;
        min-width: 200px;
    }
    
    .info-label {
        color: #6c757d;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        color: #2c3e50;
        font-size: 1rem;
        font-weight: 500;
    }
    
    .plats-list {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        margin-top: 1rem;
    }
    
    .plats-list ul {
        margin: 0.5rem 0 0 0;
        padding-left: 1.5rem;
    }
    
    .plats-list li {
        padding: 0.3rem 0;
        color: #495057;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        background: #f8f9fa;
        border-radius: 10px;
        color: #6c757d;
    }
    
    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #adb5bd;
    }
    
    .commande-badge {
        display: inline-block;
        padding: 0.35rem 0.8rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        background: #e9ecef;
        color: #495057;
        text-transform: capitalize;
    }
</style>

<div class="dashboard-header">
    <div class="container">
        <h1 class="dashboard-title">📊 Tableau de bord</h1>
    </div>
</div>

<div class="container">
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    👥
                </div>
                <h5 class="stat-title text-center">Nombre de clients</h5>
                <p class="stat-value text-center">{{ $nbClients }}</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    🏆
                </div>
                <h5 class="stat-title text-center">Plat le plus commandé</h5>
                <p class="plat-name text-center">{{ $platPopulaire ? $platPopulaire->nom : 'Aucun' }}</p>
                <div class="text-center">
                    <span class="stat-badge">
                        Commandé {{ $platPopulaire ? $platPopulaire->total : 0 }} fois
                    </span>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    🍽️
                </div>
                <h5 class="stat-title text-center">Plats commandés</h5>
                <p class="stat-value text-center">{{ $nbPlatsCommandes }}</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="section-header">
                <h4 class="section-title">📦 Dernières commandes</h4>
            </div>
            <div class="commandes-container">
                @forelse($dernieresCommandes as $commande)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $commande->id }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $commande->id }}" aria-expanded="false" aria-controls="collapse{{ $commande->id }}">
                            <span style="margin-right: 1rem;">📋</span>
                            Commande #{{ $commande->id }} 
                            <span style="margin: 0 1rem; color: #6c757d;">•</span>
                            {{ $commande->created_at->format('d/m/Y H:i') }}
                        </button>
                    </h2>
                    <div id="collapse{{ $commande->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $commande->id }}" data-bs-parent="#commandesAccordion">
                        <div class="accordion-body">
                            <div class="commande-info">
                                <div class="info-item">
                                    <div class="info-label">👤 Client</div>
                                    <div class="info-value">{{ $commande->user->name ?? '-' }}</div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">📍 Adresse</div>
                                    <div class="info-value">{{ $commande->adresse_livraison ?? '-' }}</div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">📊 Statut</div>
                                    <div class="info-value">
                                        <span class="commande-badge">{{ ucfirst($commande->statut) }}</span>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">💰 Total</div>
                                    <div class="info-value" style="font-weight: 700; font-size: 1.1rem;">
                                        {{ number_format($commande->total, 0, '.', ' ') }} F CFA
                                    </div>
                                </div>
                            </div>
                            
                            <div class="plats-list">
                                <div class="info-label">🍴 Plats commandés</div>
                                <ul>
                                    @foreach($commande->plats as $plat)
                                        <li><strong>{{ $plat->nom }}</strong> × {{ $plat->pivot->quantite }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <h5>Aucune commande récente</h5>
                    <p style="margin: 0; color: #adb5bd;">Les nouvelles commandes apparaîtront ici</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection