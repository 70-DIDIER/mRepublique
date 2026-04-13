@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4 text-center text-primary">Créer une nouvelle carousel image</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('carousels.store') }}" method="POST" enctype="multipart/form-data" class="shadow p-4 rounded bg-light mx-auto" style="max-width: 600px;">
        @csrf

        <div class="mb-3">
            <label for="nom" class="form-label">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre" value="{{ old('titre') }}" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">ImageCarousel</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary w-100">Enregistrer</button>
    </form>
</div>
@endsection