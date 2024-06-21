<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\MenuOptions;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InputStockTakingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $menu = MenuOptions::where('status', '1')
        ->where('user_id', auth()->user()->id)->first();
        if (!$menu) {
            return response()->view('errors.403', [
                'message' => 'Please start prepare your stock taking first',
            ], 403);
        }
        return $next($request);
    }
}
