@props(['nom', 'label', 'type' => 'text', 'valeur' => null])
<div class="mb-3">
    <label for="{{ $nom }}" class="form-label">{{ $label }}</label>
    <input type="{{ $type }}" id="{{ $nom }}" name="{{ $nom }}"
           value="{{ $type === 'password' ? '' : old($nom, $valeur) }}"
           class="form-control @error($nom) is-invalid @enderror" {{ $attributes }}>
    @error($nom)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>