<?php

namespace App\Http\Middleware;

use App\Models\ClientToken;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ValidateClientToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = JWTAuth::getToken();

        if (!$token || !ClientToken::where('token', $token)->exists()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized. Token is revoked.'], 401);
        }

        return $next($request);
    }
}
