<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(401);
        }

        if ($user->is_blocked) {
            abort(403, 'Your account has been blocked.');
        }

        $role = $user->isAdmin() ? User::ROLE_ADMIN : ($user->isStaff() ? User::ROLE_STAFF : User::ROLE_USER);

        if (! empty($roles) && ! in_array($role, $roles, true)) {
            abort(403, 'Unauthorized role access.');
        }

        return $next($request);
    }
}
