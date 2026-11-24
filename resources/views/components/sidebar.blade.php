@php
    $role = auth()->user()->role;
    $rubriques = match ($role) {
        'admin' => [
            ['Tableau de bord', 'dashboard'],
            ['Exploitations', 'exploitations.index'],
            ['Utilisateurs', null],
        ],
        'responsable' => [
            ['Tableau de bord', 'dashboard'],
            ['Utilisateurs', null],
            ['Poulaillers', null],
            ['Productions', null],
            ['Bandes d\'élevage', null],
            ['Intrants et stocks', null],
            ['Alimentation', null],
            ['Activités', null],
            ['Mortalités', null],
            ['Pesées', null],
            ['Ventes', null],
            ['Finances', null],
            ['Alertes', null],
        ],
        'agent' => [
            ['Tableau de bord', 'dashboard'],
            ['Bandes d\'élevage', null],
            ['Alimentation', null],
            ['Activités', null],
            ['Mortalités', null],
            ['Pesées', null],
            ['Stocks', null],
            ['Alertes', null],
        ],
    };
@endphp
<nav class="av-sidebar">
    <ul class="nav flex-column">
        @foreach ($rubriques as [$libelle, $route])
            <li class="nav-item">
                @if ($route)
                    <a class="nav-link {{ request()->routeIs($route, \Illuminate\Support\Str::before($route, '.') . '.*') ? 'active' : '' }}" href="{{ route($route) }}">{{ $libelle }}</a>
                @else
                    <span class="nav-link disabled">{{ $libelle }}</span>
                @endif
            </li>
        @endforeach
    </ul>
</nav>