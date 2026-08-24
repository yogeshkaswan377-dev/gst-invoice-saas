<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanySelected
{
    public function handle(Request $request, Closure $next): Response
    {
        // Super Admin bypass
        if (auth()->check() && auth()->user()->hasRole('super_admin')) {
            return $next($request);
        }

        $user = auth()->user();

        // If current_company_id is null but company_id is set, sync it
        if ($user && !$user->current_company_id && $user->company_id) {
            $user->current_company_id = $user->company_id;
            $user->save();
        }

        // Set session if missing
        if (!session()->has('current_company_id')) {
            if ($user && $user->current_company_id) {
                session(['current_company_id' => $user->current_company_id]);
            } elseif ($user && $user->company_id) {
                session(['current_company_id' => $user->company_id]);
                $user->current_company_id = $user->company_id;
                $user->save();
            } else {
                return redirect()->route('company.create');
            }
        }

        // Final check
        if (!session('current_company_id')) {
            return redirect()->route('company.create');
        }

        return $next($request);
    }
}
