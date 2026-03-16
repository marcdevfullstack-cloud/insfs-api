<?php

namespace App\Http\Middleware;

use App\Models\PortalUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() instanceof PortalUser) {
            return response()->json(['message' => 'Accès réservé aux étudiants du portail.'], 403);
        }

        return $next($request);
    }
}
