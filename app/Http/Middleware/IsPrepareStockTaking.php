<?php

namespace App\Http\Middleware;

use App\Models\MenuOptions;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsPrepareStockTaking
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $menu = MenuOptions::where('status', '1')->first();
        if ($menu && $menu->status == 1) {
            return response()->view('errors.403', [
                'message' => 'Sorry, this menu is disable for now. Please try again later. ',
            ], 403);
        }
        return $next($request);
    }
}
