<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionRequest;
use App\Http\Requests\UpdateProductionRequest;
use App\Models\Production;
use App\Services\ProductionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProductionController extends Controller
{
    public function __construct(private ProductionService $service)
    {
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Production::class);

        $productions = Production::query()
            ->when($request->query('q'), fn ($requete, $terme) => $requete->where('nom', 'like', "%{$terme}%"))
            ->orderBy('nom')
            ->paginate(10)
            ->withQueryString();

        return view('productions.index', compact('productions'));
    }

    public function create(): View
    {
        Gate::authorize('create', Production::class);

        return view('productions.create');
    }

    public function store(StoreProductionRequest $request): RedirectResponse
    {
        $this->service->creer($request->user(), $request->validated());

        return redirect()->route('productions.index')->with('succes', 'Production créée.');
    }

    public function edit(Production $production): View
    {
        Gate::authorize('update', $production);

        return view('productions.edit', compact('production'));
    }

    public function update(UpdateProductionRequest $request, Production $production): RedirectResponse
    {
        $this->service->modifier($production, $request->validated());

        return redirect()->route('productions.index')->with('succes', 'Production modifiée.');
    }
}