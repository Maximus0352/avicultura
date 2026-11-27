<x-champ nom="code" label="Code" maxlength="30" :valeur="$poulailler->code ?? null" />
<x-champ nom="nom" label="Nom" :valeur="$poulailler->nom ?? null" />
<x-champ nom="capacite" label="Capacité (sujets)" type="number" min="1" :valeur="$poulailler->capacite ?? null" />
<x-champ nom="localisation" label="Localisation (facultatif)" :valeur="$poulailler->localisation ?? null" />
<div class="mb-3">
    <label for="etat" class="form-label">État</label>
    <select id="etat" name="etat" class="form-select @error('etat') is-invalid @enderror">
        @foreach (['actif' => 'Actif', 'inactif' => 'Inactif', 'maintenance' => 'En maintenance'] as $valeur => $libelle)
            <option value="{{ $valeur }}" @selected(old('etat', $poulailler->etat ?? 'actif') === $valeur)>{{ $libelle }}</option>
        @endforeach
    </select>
    @error('etat')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>