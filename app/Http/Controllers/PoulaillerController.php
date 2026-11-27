<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePoulaillerRequest;
use App\Http\Requests\UpdatePoulaillerRequest;
use App\Models\Poulailler;
use App\Services\PoulaillerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PoulaillerController extends Controller
{
    public function __construct(private PoulaillerService $service)
    {
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Poulailler::class);

        $tri = in_array($request->query('tri'), ['code', 'nom', 'capacite', 'etat'], true)
            ? $request->query('tri')
            : 'code';
        $sens = $request->query('sens') === 'desc' ? 'desc' : 'asc';

        $poulaillers = Poulailler::query()
            ->when($request->query('q'), function ($requete, $terme) {
                $requete->where(function ($sousRequete) use ($terme) {
                    $sousRequete->where('code', 'like', "%{$terme}%")
                        ->orWhere('nom', 'like', "%{$terme}%");
                });
            })
            ->when(
                in_array($request->query('etat'), ['actif', 'inactif', 'maintenance'], true),
                fn ($requete) => $requete->where('etat', $request->query('etat'))
            )
            ->orderBy($tri, $sens)
            ->paginate(10)
            ->withQueryString();

        $capaciteInstallee = (int) Poulailler::sum('capacite');
        $capaciteTotale = (int) $request->user()->exploitation->capacite_totale;

        return view('poulaillers.index', compact('poulaillers', 'capaciteInstallee', 'capaciteTotale'));
    }

    public function create(): View
    {
        Gate::authorize('create', Poulailler::class);

        return view('poulaillers.create');
    }

    public function store(StorePoulaillerRequest $request): RedirectResponse
    {
        $this->service->creer($request->user(), $request->validated());

        return redirect()->route('poulaillers.index')->with('succes', 'Poulailler créé.');
    }

    public function edit(Poulailler $poulailler): View
    {
        Gate::authorize('update', $poulailler);

        return view('poulaillers.edit', compact('poulailler'));
    }

    public function update(UpdatePoulaillerRequest $request, Poulailler $poulailler): RedirectResponse
    {
        $this->service->modifier($poulailler, $request->validated());

        return redirect()->route('poulaillers.index')->with('succes', 'Poulailler modifié.');
    }
}