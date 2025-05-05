<div class="container mt-5">
    <h1 class="mb-4">Liste des Plats</h1>
    
    <a href="{{ route('plats.create') }}" class="btn btn-primary mb-3">Ajouter un nouveau plat</a>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($plats->isEmpty())
        <div class="alert alert-info">Aucun plat trouvé.</div>
    @else
        <div class="row">
            @foreach($plats as $plat)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        @if($plat->image)
                            <img src="{{ asset('storage/'.$plat->image) }}" class="card-img-top" alt="{{ $plat->nom }}">
                        @else
                            <img src="https://via.placeholder.com/300x200?text=Pas+d'image" class="card-img-top" alt="{{ $plat->nom }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $plat->nom }}</h5>
                            <p class="card-text">
                                <strong>Catégorie :</strong> {{ $plat->categorie }}<br>
                                {{ Str::limit($plat->description, 100) }}
                            </p>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">Prix : {{ number_format($plat->prix, 2) }} F CFA</small>
                            <div class="mt-2">
                                <a href="{{ route('plats.edit', $plat->id) }}" class="btn btn-sm btn-warning">Modifier</a>
                                <form action="{{ route('plats.destroy', $plat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce plat ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>


