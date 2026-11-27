@extends('layouts.app')

@section('titre', 'Modifier la bande - Avicultura')

@section('contenu')
<h1 class="h3 mb-3">Modifier la bande {{ $bande->code }} <x-statut-bande :statut="$bande->statut" /></h1>
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('bandes.update', $bande) }}" novalidate>
                    @csrf
                    @method('PUT')
                    @include('bandes._form')
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <a href="{{ route('bandes.show', $bande) }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection