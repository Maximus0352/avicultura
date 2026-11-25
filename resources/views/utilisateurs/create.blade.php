@extends('layouts.app')

@section('titre', 'Nouvel utilisateur - Avicultura')

@section('contenu')
<h1 class="h3 mb-3">{{ auth()->user()->estAdmin() ? 'Nouveau responsable' : 'Nouvel agent' }}</h1>
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('utilisateurs.store') }}" novalidate>
                    @csrf
                    @if (auth()->user()->estAdmin())
                        <div class="mb-3">
                            <label for="exploitation_id" class="form-label">Exploitation</label>
                            <select id="exploitation_id" name="exploitation_id" class="form-select @error('exploitation_id') is-invalid @enderror">
                                <option value="">Choisir une exploitation</option>
                                @foreach ($exploitations as $exploitation)
                                    <option value="{{ $exploitation->id }}" @selected((string) old('exploitation_id') === (string) $exploitation->id)>{{ $exploitation->nom }}</option>
                                @endforeach
                            </select>
                            @error('exploitation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                    @include('utilisateurs._form')
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Créer</button>
                        <a href="{{ route('utilisateurs.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection