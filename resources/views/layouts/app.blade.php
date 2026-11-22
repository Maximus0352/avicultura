<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titre', 'Avicultura')</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/avicultura.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="navbar av-barre">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-white" href="{{ route('dashboard') }}">Avicultura</a>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end text-white lh-sm">
                    <div class="fw-semibold">{{ auth()->user()->nom }}</div>
                    <small>{{ auth()->user()->libelleRole() }}</small>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Déconnexion</button>
                </form>
            </div>
        </div>
    </nav>
    <main class="container-fluid py-4">
        <x-flash />
        @yield('contenu')
    </main>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>