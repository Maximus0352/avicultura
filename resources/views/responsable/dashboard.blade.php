@extends('layouts.app')

@section('titre', 'Tableau de bord - Avicultura')

@section('contenu')
<h1 class="h3 mb-3">Tableau de bord</h1>
<div class="card">
    <div class="card-body">
        <p class="mb-1">Bienvenue, {{ auth()->user()->nom }}.</p>
        <p class="text-secondary mb-0">{{ auth()->user()->exploitation->nom }}. Les indicateurs arrivent au Jour 11.</p>
    </div>
</div>
@endsection