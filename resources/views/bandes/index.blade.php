@extends('layouts.app')

@section('titre', 'Bandes d\'élevage - Avicultura')

@php
    $lien = fn ($colonne) => route('bandes.index', array_merge(request()->query(), [
        'tri' => $colonne,
        'sens' => request('tri', 'date_debut') === $colonne && request('sens', 'desc') === 'asc' ? 'desc' : 'asc',
    ]));
    $libelles = ['planifiee' => 'Planifiée', 'en_cours' => 'En cours', 'terminee' => 'Terminée', 'annulee' => 'Annulée'];
@endphp

@section('contenu')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Bandes d'élevage</h1>
    @can('create', \App\Models\BandeElevage::class)
        <a href="{{ route('bandes.create') }}" class="btn btn-primary">Nouvelle bande</a>
    @endcan
</div>

<form method="GET" action="{{ route('bandes.index') }}" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Rechercher par code">
    </div>
    <div class="col-md-3">
        <select name="statut" class="form-select">
            <option value="">Tous les statuts</option>
            @foreach ($libelles as $valeur => $libelle)
                <option value="{{ $valeur }}" @selected(request('statut') === $valeur)>{{ $libelle }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="poulailler" class="form-select">
            <option value="">Tous les poulaillers</option>
            @foreach ($poulaillers as $poulailler)
                <option value="{{ $poulailler->id }}" @selected((string) request('poulailler') === (string) $poulailler->id)>{{ $poulailler->code }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-outline-secondary">Filtrer</button>
        <a href="{{ route('bandes.index') }}" class="btn btn-link">Réinitialiser</a>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th><a href="{{ $lien('code') }}" class="text-decoration-none text-reset">Code</a></th>
                    <th>Poulailler</th>
                    <th>Production</th>
                    <th><a href="{{ $lien('date_debut') }}" class="text-decoration-none text-reset">Début</a></th>
                    <th><a href="{{ $lien('date_fin_prevue') }}" class="text-decoration-none text-reset">Fin prévue</a></th>
                    <th class="text-end"><a href="{{ $lien('effectif_initial') }}" class="text-decoration-none text-reset">Vivants / initial</a></th>
                    <th><a href="{{ $lien('statut') }}" class="text-decoration-none text-reset">Statut</a></th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bandes as $bande)
                    <tr>
                        <td>{{ $bande->code }}</td>
                        <td>{{ $bande->poulailler->code }}</td>
                        <td>{{ $bande->production->nom }}</td>
                        <td>{{ $bande->date_debut->format('d/m/Y') }}</td>
                        <td>{{ $bande->date_fin_prevue->format('d/m/Y') }}</td>
                        <td class="text-end">{{ number_format($bande->effectif_vivant, 0, ',', ' ') }} / {{ number_format($bande->effectif_initial, 0, ',', ' ') }}</td>
                        <td><x-statut-bande :statut="$bande->statut" /></td>
                        <td class="text-end">
                            <a href="{{ route('bandes.show', $bande) }}" class="btn btn-sm btn-outline-secondary">Voir</a>
                            @can('update', $bande)
                                <a href="{{ route('bandes.edit', $bande) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-secondary py-4">Aucune bande trouvée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $bandes->links() }}</div>
@endsection