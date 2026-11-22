@extends('layouts.guest')

@section('titre', 'Connexion - Avicultura')

@section('contenu')
<div class="container-fluid">
    <div class="row min-vh-100">
        <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center av-panneau-marque">
            <div class="px-5 text-white">
                <h1 class="display-5 fw-bold">Avicultura</h1>
                <p class="fs-5 mb-0">Gérez votre exploitation avicole : bandes, stocks, alimentation et finances.</p>
            </div>
        </div>
        <div class="col-lg-6 d-flex align-items-center justify-content-center py-5">
            <div class="w-100 av-formulaire-connexion">
                <h2 class="h3 mb-1">Connexion</h2>
                <p class="text-secondary mb-4">Accédez à votre espace de gestion.</p>
                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse e-mail</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               autocomplete="username" autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection