@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4 text-center text-primary">Modifier le livreur</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('livreur.update', $livreur) }}" method="POST" enctype="multipart/form-data" class="shadow p-4 rounded bg-light mx-auto" style="max-width: 600px;">
        @csrf
        @method('PUT')
        <input type="hidden" name="role" value="livreur">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom complet du livreur</label>
            <input type="text" class="form-control" id="nom" name="name"  value="{{ old('name', $livreur->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email du livreur</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $livreur->email) }}" required>
        </div>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="Nouveau mot de passe (laisser vide pour ne pas changer)" name="password">
        </div>

        <button type="submit" class="btn btn-primary w-100">Mettre à jour</button>
    </form>
</div>
@endsection