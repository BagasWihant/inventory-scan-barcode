<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isMC
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if ($user->Role_ID != '3' && $user->Admin != '1') {

                return response()->view('errors.403', [
                    'message' => 'This menu only for Admin'
                ], 403);
            }
        
        return $next($request);
    }
}
