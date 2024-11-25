<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       $user = Auth::user();
        if (!$user) {
            return abort(401, 'Unauthorized');
        }

        if (!$user->is_admin) {
            return abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
