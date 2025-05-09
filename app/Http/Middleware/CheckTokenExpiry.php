<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->user()?->currentAccessToken();

        if ($token && $token->last_used_at->diffInDays(now()) >= 7) {
            $token->delete();
            return response()->json([
                'status' => 'error',
                'message' => 'Token expired',
            ], 401);
        }

        return $next($request);
    }
}
