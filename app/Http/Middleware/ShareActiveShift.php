<?php

namespace App\Http\Middleware;

use App\Models\CashierShift;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ShareActiveShift
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $shift = CashierShift::where('user_id', Auth::id())->where('status', 'open')->first();
            view()->share('activeShift', $shift);
        }

        return $next($request);
    }
}
