<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            abort(403, 'Your account is inactive or unauthorized.');
        }

        $allowed = collect($roles)
            ->map(fn (string $role): UserRole => UserRole::from($role))
            ->all();

        if (! $user->hasAnyRole(...$allowed)) {
            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
