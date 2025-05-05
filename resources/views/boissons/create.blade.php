@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4 text-center text-primary">Créer une nouvelle boisson</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('boissons.store') }}" method="POST" enctype="multipart/form-data" class="shadow p-4 rounded bg-light mx-auto" style="max-width: 600px;">
        @csrf

        <div class="mb-3">
            <label for="nom" class="form-label">Nom de la boisson</label>
            <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom') }}" required>
        </div>

        <div class="mb-3">
            <label for="categorie" class="form-label">Catégorie</label>
            <input type="text" class="form-control" id="categorie" name="categorie" value="{{ old('categorie') }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="prix" class="form-label">Prix</label>
            <input type="number" step="0.01" name="prix" class="form-control" id="prix" value="{{ old('prix') }}" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image de la boisson</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary w-100">Enregistrer</button>
    </form>
</div>
@endsection