<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles  Allowed roles for this route
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! in_array($user->role, $roles, true)) {
            // Redirect user to their own dashboard based on role
            $redirectRoute = match ($user->role) {
                'cashier' => 'pos.index',
                'barista' => 'kitchen.index',
                default => 'dashboard',
            };

            return redirect()->route($redirectRoute)
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
