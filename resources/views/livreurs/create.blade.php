@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4 text-center text-primary">Créer un livreur</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('livreurs.register')}}" method="POST" enctype="multipart/form-data" class="shadow p-4 rounded bg-light mx-auto" style="max-width: 600px;">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nom de Livreur</label>
            <input type="text" placeholder="Votre nom complet" class="form-control" id="nom" name="name" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" placeholder="Entrez votre email" class="form-control" id="email" name="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" placeholder="Entrez votre mot de passe" class="form-control" id="password" name="password" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Rôle</label>
            <input type="hidden" name="role" class="form-control" id="role" required>
        </div>

       <!--  <div class="mb-3">
            <label for="adress" class="form-label">Adresse</label>
            <input type="adress" class="form-control" id="adress" name="adresse" required>
        </div>

        <div class="mb-3">
            <label for="telephone" class="form-label">Téléphone</label>
            <input type="tel" class="form-control" id="telephone" name="telephone" required>
        </div>

        <div class="mb-3">
            <label for="photo" class="form-label">Photo de profil</label>
            <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
        </div> -->

        <button type="submit" class="btn btn-primary w-100">Enregistrer</button>
    </form>
</div>
@endsection
