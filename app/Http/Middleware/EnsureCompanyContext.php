<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Support\CompanyContext;

class EnsureCompanyContext
{
    public function handle(Request $request, Closure $next)
    {
        if (
            $request->routeIs('admin.company.select', 'admin.company.set') ||
            $request->is('admin/company/select') ||
            $request->is('admin/company/select/*') ||
            $request->is('admin/login') || $request->is('login') || $request->is('logout') ||
            $request->is('vendor/voyager/*') || $request->is('voyager-assets*')
        ) {
            return $next($request);
        }

        $user = auth()->user();
        if (!$user) return $next($request);

        // If user is super admin and has no chosen context, force select
        if ($user->hasRole('super_admin') && !CompanyContext::id()) {
            return redirect()->route('admin.company.select');
        }

        // If regular user: ensure session matches user company
        if (!$user->hasRole('super_admin')) {
            if (!$user->company_id) {
                abort(403, 'User has no company assigned.');
            }
            if (CompanyContext::id() !== $user->company_id) {
                CompanyContext::set($user->company_id);
            }
        }

        return $next($request);
    }
}
