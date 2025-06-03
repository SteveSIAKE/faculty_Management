<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return response()->json([
                'message' => 'Non authentifié'
            ], 401);
        }

        $userRole = $request->user()->role->name;

        if (!in_array($userRole, $roles)) {
            return response()->json([
                'message' => 'Accès non autorisé'
            ], 403);
        }

        return $next($request);
    }
} 