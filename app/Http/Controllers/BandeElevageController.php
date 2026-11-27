<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBandeRequest;
use App\Http\Requests\TerminerBandeRequest;
use App\Http\Requests\UpdateBandeRequest;
use App\Models\BandeElevage;
use App\Models\Poulailler;
use App\Models\Production;
use App\Services\BandeElevageService;
use App\Services\EffectifService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BandeElevageController extends Controller
{
    public function __construct(private BandeElevageService $service, private EffectifService $effectifs)
    {
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', BandeElevage::class);

        $tri = in_array($request->query('tri'), ['code', 'date_debut', 'date_fin_prevue', 'effectif_initial', 'statut'], true)
            ? $request->query('tri')
            : 'date_debut';
        $sens = $request->query('sens') === 'asc' ? 'asc' : 'desc';

        $bandes = BandeElevage::with(['poulailler', 'production'])
            ->withSum('mortalites', 'quantite')
            ->withSum('ventes', 'nombre_sujets')
            ->when($request->query('q'), fn ($requete, $terme) => $requete->where('code', 'like', "%{$terme}%"))
            ->when(
                in_array($request->query('statut'), ['planifiee', 'en_cours', 'terminee', 'annulee'], true),
                fn ($requete) => $requete->where('statut', $request->query('statut'))
            )
            ->when(
                ctype_digit((string) $request->query('poulailler')),
                fn ($requete) => $requete->where('poulailler_id', (int) $request->query('poulailler'))
            )
            ->orderBy($tri, $sens)
            ->paginate(10)
            ->withQueryString();

        $bandes->getCollection()->each(
            fn (BandeElevage $bande) => $bande->setAttribute('effectif_vivant', $this->effectifs->depuisCumuls($bande))
        );

        $poulaillers = Poulailler::orderBy('code')->get();

        return view('bandes.index', compact('bandes', 'poulaillers'));
    }

    public function show(BandeElevage $bande): View
    {
        Gate::authorize('view', $bande);

        $bande->load(['poulailler', 'production']);
        $effectifVivant = $this->effectifs->vivant($bande);

        return view('bandes.show', compact('bande', 'effectifVivant'));
    }

    public function create(): View
    {
        Gate::authorize('create', BandeElevage::class);

        return view('bandes.create', $this->listes(null));
    }

    public function store(StoreBandeRequest $request): RedirectResponse
    {
        $this->service->creer($request->user(), $request->validated());

        return redirect()->route('bandes.index')->with('succes', 'Bande créée (planifiée).');
    }

    public function edit(BandeElevage $bande): View
    {
        Gate::authorize('update', $bande);

        return view('bandes.edit', ['bande' => $bande] + $this->listes($bande));
    }

    public function update(UpdateBandeRequest $request, BandeElevage $bande): RedirectResponse
    {
        $this->service->modifier($bande, $request->validated());

        return redirect()->route('bandes.show', $bande)->with('succes', 'Bande modifiée.');
    }

    public function demarrer(BandeElevage $bande): RedirectResponse
    {
        Gate::authorize('changerStatut', $bande);

        return $this->transition($bande, fn () => $this->service->demarrer($bande), 'Bande démarrée.');
    }

    public function terminer(TerminerBandeRequest $request, BandeElevage $bande): RedirectResponse
    {
        $date = $request->validated()['date_fin_reelle'];

        return $this->transition($bande, fn () => $this->service->terminer($bande, $date), 'Bande terminée.');
    }

    public function annuler(BandeElevage $bande): RedirectResponse
    {
        Gate::authorize('changerStatut', $bande);

        return $this->transition($bande, fn () => $this->service->annuler($bande), 'Bande annulée.');
    }

    private function transition(BandeElevage $bande, callable $action, string $succes): RedirectResponse
    {
        try {
            $action();
        } catch (ValidationException $exception) {
            return redirect()->route('bandes.show', $bande)
                ->with('erreur', collect($exception->errors())->flatten()->first());
        }

        return redirect()->route('bandes.show', $bande)->with('succes', $succes);
    }

    private function listes(?BandeElevage $bande): array
    {
        $poulaillers = Poulailler::where(function ($requete) use ($bande) {
            $requete->where('etat', 'actif');

            if ($bande !== null) {
                $requete->orWhere('id', $bande->poulailler_id);
            }
        })->orderBy('code')->get();

        return [
            'poulaillers' => $poulaillers,
            'productions' => Production::orderBy('nom')->get(),
        ];
    }
}