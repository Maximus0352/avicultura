<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $utilisateur = $request->user();

        if ($utilisateur === null || ! in_array($utilisateur->role, $roles, true)) {
            Log::warning('Acces refuse par role', [
                'user_id' => $utilisateur?->id,
                'role' => $utilisateur?->role,
                'roles_requis' => $roles,
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
            ]);

            abort(403, 'Accès refusé.');
        }

        return $next($request);
    }
}
