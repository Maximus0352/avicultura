@extends('layouts.app')

@section('titre', 'Poulaillers - Avicultura')

@php
    $lien = fn ($colonne) => route('poulaillers.index', array_merge(request()->query(), [
        'tri' => $colonne,
        'sens' => request('tri', 'code') === $colonne && request('sens') !== 'desc' ? 'desc' : 'asc',
    ]));
    $pourcentage = $capaciteTotale > 0 ? min(100, (int) round($capaciteInstallee / $capaciteTotale * 100)) : 0;
    $badges = ['actif' => 'bg-success', 'inactif' => 'bg-secondary', 'maintenance' => 'bg-warning text-dark'];
    $libelles = ['actif' => 'Actif', 'inactif' => 'Inactif', 'maintenance' => 'En maintenance'];
@endphp

@section('contenu')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Poulaillers</h1>
    <a href="{{ route('poulaillers.create') }}" class="btn btn-primary">Nouveau poulailler</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-1">
            <span>Capacité installée</span>
            <strong>{{ number_format($capaciteInstallee, 0, ',', ' ') }} / {{ number_format($capaciteTotale, 0, ',', ' ') }} sujets</strong>
        </div>
        <div class="progress" role="progressbar" aria-valuenow="{{ $pourcentage }}" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar" style="width: {{ $pourcentage }}%"></div>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('poulaillers.index') }}" class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Rechercher par code ou nom">
    </div>
    <div class="col-md-3">
        <select name="etat" class="form-select">
            <option value="">Tous les états</option>
            @foreach ($libelles as $valeur => $libelle)
                <option value="{{ $valeur }}" @selected(request('etat') === $valeur)>{{ $libelle }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <button type="submit" class="btn btn-outline-secondary">Filtrer</button>
        <a href="{{ route('poulaillers.index') }}" class="btn btn-link">Réinitialiser</a>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th><a href="{{ $lien('code') }}" class="text-decoration-none text-reset">Code</a></th>
                    <th><a href="{{ $lien('nom') }}" class="text-decoration-none text-reset">Nom</a></th>
                    <th class="text-end"><a href="{{ $lien('capacite') }}" class="text-decoration-none text-reset">Capacité</a></th>
                    <th>Localisation</th>
                    <th><a href="{{ $lien('etat') }}" class="text-decoration-none text-reset">État</a></th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($poulaillers as $poulailler)
                    <tr>
                        <td>{{ $poulailler->code }}</td>
                        <td>{{ $poulailler->nom }}</td>
                        <td class="text-end">{{ number_format($poulailler->capacite, 0, ',', ' ') }}</td>
                        <td>{{ $poulailler->localisation ?? '-' }}</td>
                        <td><span class="badge {{ $badges[$poulailler->etat] }}">{{ $libelles[$poulailler->etat] }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('poulaillers.edit', $poulailler) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-4">Aucun poulailler trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $poulaillers->links() }}</div>
@endsection