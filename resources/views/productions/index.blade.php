@extends('layouts.app')

@section('titre', 'Productions - Avicultura')

@section('contenu')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Productions</h1>
    <a href="{{ route('productions.create') }}" class="btn btn-primary">Nouvelle production</a>
</div>

<form method="GET" action="{{ route('productions.index') }}" class="row g-2 mb-3">
    <div class="col-md-6">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Rechercher par nom">
    </div>
    <div class="col-md-6">
        <button type="submit" class="btn btn-outline-secondary">Rechercher</button>
        <a href="{{ route('productions.index') }}" class="btn btn-link">Réinitialiser</a>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Unité</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productions as $production)
                    <tr>
                        <td>{{ $production->nom }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($production->description, 60) ?: '-' }}</td>
                        <td>{{ $production->unite_production }}</td>
                        <td class="text-end">
                            <a href="{{ route('productions.edit', $production) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-secondary py-4">Aucune production trouvée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $productions->links() }}</div>
@endsection