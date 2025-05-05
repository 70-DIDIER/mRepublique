@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Modifier un plat</h1>
    
    @if ($errors->any())
       <div class="alert alert-danger">
          <ul class="mb-0">
             @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
             @endforeach
          </ul>
       </div>
    @endif

    <form action="{{ route('plats.update', $plat->id) }}" method="POST" enctype="multipart/form-data">
         @csrf
         @method('PUT')
         
         <div class="mb-3">
             <label for="nom" class="form-label">Nom du plat</label>
             <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom', $plat->nom) }}" required>
         </div>

         <div class="mb-3">
             <label for="categorie" class="form-label">Catégorie</label>
             <input type="text" class="form-control" id="categorie" name="categorie" value="{{ old('categorie', $plat->categorie) }}" required>
         </div>

         <div class="mb-3">
             <label for="description" class="form-label">Description</label>
             <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $plat->description) }}</textarea>
         </div>

         <div class="mb-3">
             <label for="prix" class="form-label">Prix</label>
             <input type="number" step="0.01" class="form-control" id="prix" name="prix" value="{{ old('prix', $plat->prix) }}" required>
         </div>

         <div class="mb-3">
             <label for="image" class="form-label">Image du plat</label>
             <input type="file" class="form-control" id="image" name="image" accept="image/*">
             @if($plat->image)
                 <div class="mt-2">
                     <img src="{{ asset('storage/'.$plat->image) }}" alt="{{ $plat->nom }}" style="max-width:150px;">
                 </div>
             @endif
         </div>

         <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
    </form>
</div>
@endsection
