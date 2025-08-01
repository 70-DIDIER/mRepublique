@extends('layouts.app')

@section('content')
</div>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-9">
        <h2>Les plats disponible</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('plats.create') }}" class="btn btn-primary" style="color: white">AJOUTER</a>
            </li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
<div class="row">
    
    @foreach ($plats as $plat )
    <div class="col-lg-4">
    <div class="contact-box">
        <a class="row" href="{{ route('plats.edit', $plat->id) }}">
        <div class="col-4">
            <div class="text-center">
                <img alt="image" class="rounded-circle m-t-xs img-fluid" src="{{ asset('storage/'.$plat->image) }}">
                <div class="m-t-xs font-bold">{{ $plat->categorie }}</div>
            </div>
        </div>
        <div class="col-8">
            <h3><strong>{{ $plat->nom }}</strong></h3>
            <p><span class="btn btn-sm btn-primary">{{ number_format($plat->prix, 0, '.', ',') }} F CFA</span></p>
           <address>
                {{ Str::limit($plat->description, 50) }}
            </address>

            <form method="POST" action="{{ route('plats.toggle', $plat->id) }}">
                @csrf
                @method('PATCH')
                @if ($plat->is_active)
                    <button type="submit" class="btn btn-sm btn-danger">Désactiver</button>
                @else
                    <button type="submit" class="btn btn-sm btn-success">Activer</button>
                @endif
            </form>

            
        </div>
            </a>
    </div>
</div>  
    @endforeach


</div>
</div>
           
@endsection

