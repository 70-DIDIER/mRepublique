@extends('layouts.app')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-9">
        <h2>Les Livreurs disponibles dans l'Application</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('livreurs.create') }}" class="btn btn-primary" style="color: white">AJOUTER</a>
            </li>

        </ol>
    </div>
</div>

<div class="container mt-5">
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped align-middle text-center">
            <thead class="table-primary">
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Téléphone</th>
                    <th>Actions</th>
                    <!-- <th>Adresse</th>
                    <th>Photo</th>-->
                </tr>
            </thead>
            <tbody>
                @foreach ($livreurs as $livreur)
                    <tr>
                        <td>{{ $livreur->name }}</td>
                        <td>{{ $livreur->email }}</td>
                        <td><span class="">{{ $livreur->role }}</span></td>
                        <td>{{ $livreur->telephone }}</td>
                        <!-- <td>{{ $livreur->adresse }}</td> -->
                        <!-- <td>
                            @if($livreur->photo)
                                <img src="{{ asset('storage/'.$livreur->photo) }}" alt="Photo" class="rounded-circle border border-2" style="width: 50px; height: 50px;">
                            @else
                                <span class="text-muted">Aucune</span>
                            @endif
                        </td> -->
                        <td>
                            <a href="{{ route('livreurs.edit', $livreur->id) }}" class="btn btn-sm btn-warning mb-1">Modifier</a>
                            <form action="{{ route('livreurs.destroy', $livreur->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Téléphone</th>
                    <th>Actions</th>
                    <!-- <th>Adresse</th>
                    <th>Photo</th>-->
                </tr>
            </tfoot>
        </table>
    </div>
</div>





@endsection

