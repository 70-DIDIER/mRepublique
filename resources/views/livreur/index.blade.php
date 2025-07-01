@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <h1 class="mb-5 text-center text-decoration-underline">Liste des Livreurs</h1>
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="d-flex justify-content-between align-items-center p-3">
                <h5 class="mb-0">Livreurs</h5>
                <a href="{{ route('livreur.create') }}" class="btn btn-primary">Ajouter un Livreur</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: var(--secondary-color); color: #fff;">
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Date d’inscription</th>
                            <th>role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($livreurs as $livreur)
                        <tr>
                            <td>{{ $livreur->name }}</td>
                            <td>{{ $livreur->email }}</td>
                            <td>{{ $livreur->created_at ? $livreur->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $livreur->role }}</td>
                            <td>
                                {{-- <a href="{{ route('livreur.show', $livreur->id) }}" class="btn btn-info btn-sm">Voir</a> --}}
                                <a href="{{ route('livreur.edit', $livreur) }}" class="btn btn-warning btn-sm">Modifier</a>
                                <form action="{{ route('livreur.destroy', $livreur->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucun utilisateur trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection