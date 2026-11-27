<x-champ nom="nom" label="Nom de la production" :valeur="$production->nom ?? null" />
<div class="mb-3">
    <label for="description" class="form-label">Description (facultatif)</label>
    <textarea id="description" name="description" rows="3"
              class="form-control @error('description') is-invalid @enderror">{{ old('description', $production->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="unite_production" class="form-label">Unité de production</label>
    <select id="unite_production" name="unite_production" class="form-select @error('unite_production') is-invalid @enderror">
        @foreach (['sujet' => 'Sujet', 'kg' => 'Kilogramme', 'oeuf' => 'Œuf'] as $valeur => $libelle)
            <option value="{{ $valeur }}" @selected(old('unite_production', $production->unite_production ?? 'sujet') === $valeur)>{{ $libelle }}</option>
        @endforeach
    </select>
    @error('unite_production')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>