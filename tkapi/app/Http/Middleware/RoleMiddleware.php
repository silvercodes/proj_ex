<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $role
     * @param null $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $role, $permission = null)
    {
        if (!$request->user()->hasRole($role))
            return response('', 403);

        if($permission !== null && !$request->user()->can($permission))
            return response('', 403);


        return $next($request);
    }
}
