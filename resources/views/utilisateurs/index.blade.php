@extends('layouts.app')

@section('titre', 'Utilisateurs - Avicultura')

@section('contenu')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Utilisateurs</h1>
    <a href="{{ route('utilisateurs.create') }}" class="btn btn-primary">
        {{ auth()->user()->estAdmin() ? 'Nouveau responsable' : 'Nouvel agent' }}
    </a>
</div>

<form method="GET" action="{{ route('utilisateurs.index') }}" class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Rechercher par nom ou e-mail">
    </div>
    <div class="col-md-3">
        <select name="actif" class="form-select">
            <option value="">Tous les comptes</option>
            <option value="1" @selected(request('actif') === '1')>Actifs</option>
            <option value="0" @selected(request('actif') === '0')>Désactivés</option>
        </select>
    </div>
    <div class="col-md-4">
        <button type="submit" class="btn btn-outline-secondary">Filtrer</button>
        <a href="{{ route('utilisateurs.index') }}" class="btn btn-link">Réinitialiser</a>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>E-mail</th>
                    @if (auth()->user()->estAdmin())
                        <th>Exploitation</th>
                    @endif
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($utilisateurs as $utilisateur)
                    <tr>
                        <td>{{ $utilisateur->nom }}</td>
                        <td>{{ $utilisateur->email }}</td>
                        @if (auth()->user()->estAdmin())
                            <td>{{ $utilisateur->exploitation?->nom ?? '-' }}</td>
                        @endif
                        <td>{{ $utilisateur->libelleRole() }}</td>
                        <td>
                            <span class="badge {{ $utilisateur->actif ? 'bg-success' : 'bg-secondary' }}">
                                {{ $utilisateur->actif ? 'Actif' : 'Désactivé' }}
                            </span>
                        </td>
                        <td class="text-end">
                            @can('update', $utilisateur)
                                <a href="{{ route('utilisateurs.edit', $utilisateur) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                            @endcan
                            @can('changerStatut', $utilisateur)
                                <form method="POST" action="{{ route('utilisateurs.statut', $utilisateur) }}" class="d-inline"
                                      onsubmit="return confirm('Confirmer le changement de statut ?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                        {{ $utilisateur->actif ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-4">Aucun utilisateur trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $utilisateurs->links() }}</div>
@endsection