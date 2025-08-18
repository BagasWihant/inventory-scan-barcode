<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class allowedNIK
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$nik): Response
    {
        $user = auth()->user();
        if (in_array($user->nik, $nik) || $user->Admin == '1') {

            return $next($request);
        }
        return response()->view('errors.403', [
            'message' => 'This menu only for Admin '
        ], 403);
    }
}
