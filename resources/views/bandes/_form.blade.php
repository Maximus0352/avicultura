@php
    $verrouille = isset($bande) && $bande->statut === 'en_cours';
    $dateDebut = isset($bande) ? $bande->date_debut->format('Y-m-d') : null;
    $dateFin = isset($bande) ? $bande->date_fin_prevue->format('Y-m-d') : null;
@endphp

@if ($verrouille)
    <dl class="row">
        <dt class="col-sm-4">Code</dt><dd class="col-sm-8">{{ $bande->code }}</dd>
        <dt class="col-sm-4">Poulailler</dt><dd class="col-sm-8">{{ $bande->poulailler->code }} - {{ $bande->poulailler->nom }}</dd>
        <dt class="col-sm-4">Production</dt><dd class="col-sm-8">{{ $bande->production->nom }}</dd>
        <dt class="col-sm-4">Date de début</dt><dd class="col-sm-8">{{ $bande->date_debut->format('d/m/Y') }}</dd>
        <dt class="col-sm-4">Effectif initial</dt><dd class="col-sm-8">{{ number_format($bande->effectif_initial, 0, ',', ' ') }}</dd>
    </dl>
    <p class="text-secondary">Bande en cours : seule la date de fin prévue peut être modifiée.</p>
    <x-champ nom="date_fin_prevue" label="Date de fin prévue" type="date" :valeur="$dateFin" />
@else
    <x-champ nom="code" label="Code de la bande" maxlength="30" :valeur="$bande->code ?? null" />
    <div class="mb-3">
        <label for="poulailler_id" class="form-label">Poulailler</label>
        <select id="poulailler_id" name="poulailler_id" class="form-select @error('poulailler_id') is-invalid @enderror">
            <option value="">Choisir un poulailler</option>
            @foreach ($poulaillers as $poulailler)
                <option value="{{ $poulailler->id }}" @selected((string) old('poulailler_id', $bande->poulailler_id ?? '') === (string) $poulailler->id)>
                    {{ $poulailler->code }} - {{ $poulailler->nom }} (capacité {{ number_format($poulailler->capacite, 0, ',', ' ') }})
                </option>
            @endforeach
        </select>
        @error('poulailler_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="production_id" class="form-label">Production</label>
        <select id="production_id" name="production_id" class="form-select @error('production_id') is-invalid @enderror">
            <option value="">Choisir une production</option>
            @foreach ($productions as $production)
                <option value="{{ $production->id }}" @selected((string) old('production_id', $bande->production_id ?? '') === (string) $production->id)>
                    {{ $production->nom }}
                </option>
            @endforeach
        </select>
        @error('production_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <x-champ nom="date_debut" label="Date de début" type="date" :valeur="$dateDebut" />
    <x-champ nom="date_fin_prevue" label="Date de fin prévue" type="date" :valeur="$dateFin" />
    <x-champ nom="effectif_initial" label="Effectif initial" type="number" min="1" :valeur="$bande->effectif_initial ?? null" />
@endif