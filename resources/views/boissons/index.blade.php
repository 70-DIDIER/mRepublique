@extends('layouts.app')

@section('content')
</div>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-9">
        <h2>Les boissons disponible</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('boissons.create') }}" class="btn btn-primary">AJOUTER</a>
            </li>
            
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
<div class="row">
    @foreach ($boissons as $boisson )
    <div class="col-lg-4">
    <div class="contact-box">
        <a class="row" href="profile.html">
        <div class="col-4">
            <div class="text-center">
                <img alt="image" class="rounded-circle m-t-xs img-fluid" src="{{ asset('storage/'.$boisson->image) }}">
                <div class="m-t-xs font-bold">{{ $boisson->categorie }}</div>
            </div>
        </div>
        <div class="col-8">
            <h3><strong>{{ $boisson->nom }}</strong></h3>
            <p><span class="btn btn-sm btn-primary">{{ number_format($boisson->prix, 2) }} F CFA</span></p>
            <address>
                {{ Str::limit($boisson->description, 50) }}
            </address>
        </div>
            </a>
    </div>
</div>  
    @endforeach


</div>
</div>
           
@endsection

