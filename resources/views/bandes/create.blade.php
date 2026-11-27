@extends('layouts.app')

@section('titre', 'Nouvelle bande - Avicultura')

@section('contenu')
<h1 class="h3 mb-3">Nouvelle bande d'élevage</h1>
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('bandes.store') }}" novalidate>
                    @csrf
                    @include('bandes._form')
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Créer la bande</button>
                        <a href="{{ route('bandes.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection