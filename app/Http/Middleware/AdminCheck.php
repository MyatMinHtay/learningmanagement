<?php

namespace App\Http\Middleware;

use App\Models\SystemRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminCheck
{
    /**
     * Handle an incoming request.
     * 
     * Validates if the authenticated user has the required permission.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string $permission The permission to check
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $user = auth()->user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect('/login')->with('warning', 'You must login!');
        }

      

        // Get user permissions from their role
        $userPermissions = explode(',', $user->role->permissions);

        $i = in_array('all',$userPermissions);

        // Check if user has the required permission or has 'all' permissions
        if (in_array($permission, $userPermissions) || in_array('all', $userPermissions)) {
            return $next($request);
        }
        
        // User doesn't have the required permission
        return redirect()->back()->with('warning', 'Unauthorized access: Missing required permission.');
    }
}
