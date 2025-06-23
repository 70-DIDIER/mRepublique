@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <h1 class="mb-5 text-center text-decoration-underline">Liste des utilisateurs</h1>
    <div class="card shadow">
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: var(--secondary-color); color: #fff;">
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Date d’inscription</th>
                            <th>role</th>
                            <th>code_sms</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->telephone ?? '-' }}</td>
                            <td>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ $user->code_sms }}</td>
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