<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Exploitation;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private UserService $service)
    {
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $acteur = $request->user();

        $utilisateurs = User::with('exploitation')
            ->where('role', '<>', 'admin')
            ->when($acteur->estResponsable(), fn ($requete) => $requete->where('exploitation_id', $acteur->exploitation_id))
            ->when($request->query('q'), function ($requete, $terme) {
                $requete->where(function ($sousRequete) use ($terme) {
                    $sousRequete->where('nom', 'like', "%{$terme}%")
                        ->orWhere('email', 'like', "%{$terme}%");
                });
            })
            ->when(
                in_array($request->query('actif'), ['0', '1'], true),
                fn ($requete) => $requete->where('actif', $request->query('actif') === '1')
            )
            ->orderBy('nom')
            ->paginate(10)
            ->withQueryString();

        return view('utilisateurs.index', compact('utilisateurs'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', User::class);

        $exploitations = $request->user()->estAdmin()
            ? Exploitation::orderBy('nom')->get()
            : collect();

        return view('utilisateurs.create', compact('exploitations'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->service->creer($request->user(), $request->validated());

        return redirect()->route('utilisateurs.index')->with('succes', 'Utilisateur créé.');
    }

    public function edit(User $utilisateur): View
    {
        Gate::authorize('update', $utilisateur);

        return view('utilisateurs.edit', compact('utilisateur'));
    }

    public function update(UpdateUserRequest $request, User $utilisateur): RedirectResponse
    {
        $this->service->modifier($utilisateur, $request->validated());

        return redirect()->route('utilisateurs.index')->with('succes', 'Utilisateur modifié.');
    }

    public function basculer(User $utilisateur): RedirectResponse
    {
        Gate::authorize('changerStatut', $utilisateur);

        $this->service->basculerActif($utilisateur);

        return redirect()->route('utilisateurs.index')->with('succes', 'Statut du compte mis à jour.');
    }
}