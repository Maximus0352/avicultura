@extends('layouts.app')

@section('titre', 'Modifier l\'utilisateur - Avicultura')

@section('contenu')
<h1 class="h3 mb-3">Modifier l'utilisateur</h1>
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <p class="text-secondary">{{ $utilisateur->libelleRole() }}</p>
                <form method="POST" action="{{ route('utilisateurs.update', $utilisateur) }}" novalidate>
                    @csrf
                    @method('PUT')
                    @include('utilisateurs._form')
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <a href="{{ route('utilisateurs.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection