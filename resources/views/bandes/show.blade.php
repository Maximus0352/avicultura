@extends('layouts.app')

@section('titre', 'Bande ' . $bande->code . ' - Avicultura')

@section('contenu')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Bande {{ $bande->code }} <x-statut-bande :statut="$bande->statut" /></h1>
    <a href="{{ route('bandes.index') }}" class="btn btn-outline-secondary">Retour à la liste</a>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Poulailler</dt>
                    <dd class="col-sm-7">{{ $bande->poulailler->code }} - {{ $bande->poulailler->nom }}</dd>
                    <dt class="col-sm-5">Production</dt>
                    <dd class="col-sm-7">{{ $bande->production->nom }}</dd>
                    <dt class="col-sm-5">Date de début</dt>
                    <dd class="col-sm-7">{{ $bande->date_debut->format('d/m/Y') }}</dd>
                    <dt class="col-sm-5">Fin prévue</dt>
                    <dd class="col-sm-7">{{ $bande->date_fin_prevue->format('d/m/Y') }}</dd>
                    @if ($bande->date_fin_reelle)
                        <dt class="col-sm-5">Fin réelle</dt>
                        <dd class="col-sm-7">{{ $bande->date_fin_reelle->format('d/m/Y') }}</dd>
                    @endif
                    <dt class="col-sm-5">Effectif initial</dt>
                    <dd class="col-sm-7">{{ number_format($bande->effectif_initial, 0, ',', ' ') }}</dd>
                    <dt class="col-sm-5">Effectif vivant</dt>
                    <dd class="col-sm-7"><strong>{{ number_format($effectifVivant, 0, ',', ' ') }}</strong></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        @can('changerStatut', $bande)
            @if (in_array($bande->statut, ['planifiee', 'en_cours'], true))
                <div class="card">
                    <div class="card-body d-grid gap-2">
                        @can('update', $bande)
                            <a href="{{ route('bandes.edit', $bande) }}" class="btn btn-outline-primary">Modifier</a>
                        @endcan

                        @if ($bande->statut === 'planifiee')
                            <form method="POST" action="{{ route('bandes.demarrer', $bande) }}"
                                  onsubmit="return confirm('Démarrer cette bande ?')">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">Démarrer la bande</button>
                            </form>
                        @endif

                        @if ($bande->statut === 'en_cours')
                            <form method="POST" action="{{ route('bandes.terminer', $bande) }}" novalidate>
                                @csrf
                                <label for="date_fin_reelle" class="form-label">Date de fin réelle</label>
                                <input type="date" id="date_fin_reelle" name="date_fin_reelle"
                                       value="{{ old('date_fin_reelle', now()->toDateString()) }}"
                                       class="form-control mb-2 @error('date_fin_reelle') is-invalid @enderror">
                                @error('date_fin_reelle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <button type="submit" class="btn btn-primary w-100">Terminer la bande</button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('bandes.annuler', $bande) }}"
                              onsubmit="return confirm('Annuler cette bande ? Cette action est définitive.')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">Annuler la bande</button>
                        </form>
                    </div>
                </div>
            @endif
        @endcan
    </div>
</div>
@endsection