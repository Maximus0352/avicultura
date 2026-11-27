@extends('layouts.app')

@section('titre', 'Nouveau poulailler - Avicultura')

@section('contenu')
<h1 class="h3 mb-3">Nouveau poulailler</h1>
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('poulaillers.store') }}" novalidate>
                    @csrf
                    @include('poulaillers._form')
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Créer</button>
                        <a href="{{ route('poulaillers.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection