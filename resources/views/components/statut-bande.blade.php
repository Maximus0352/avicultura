@props(['statut'])
@php
    $styles = [
        'planifiee' => ['bg-secondary', 'Planifiée'],
        'en_cours' => ['bg-success', 'En cours'],
        'terminee' => ['bg-dark', 'Terminée'],
        'annulee' => ['bg-danger', 'Annulée'],
    ];
    [$classe, $libelle] = $styles[$statut];
@endphp
<span class="badge {{ $classe }}">{{ $libelle }}</span>