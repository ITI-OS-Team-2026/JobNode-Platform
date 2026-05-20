<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! in_array($request->user()->role, $roles)) {
            // If they don't have access, boot them back to their respective dashboard
            $role = $request->user()->role;
            
            return match($role) {
                'employer' => redirect()->route('employer.dashboard')->with('error', 'Unauthorized access.'),
                'candidate' => redirect()->route('candidate.dashboard')->with('error', 'Unauthorized access.'),
                'admin' => redirect()->route('admin.dashboard')->with('error', 'Unauthorized access.'),
                default => redirect('/'),
            };
        }

        return $next($request);
    }
}