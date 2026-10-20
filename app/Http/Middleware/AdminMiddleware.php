<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        /*
        |--------------------------------------------------------------------------
        | 1. Authentication Check
        |--------------------------------------------------------------------------
        |
        | User must be logged in before accessing any admin page.
        |
        */

        if (!Auth::check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Please login first.');
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Get Authenticated User
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | 3. Safety Check
        |--------------------------------------------------------------------------
        |
        | Although Auth::check() is true, keep this additional protection
        | so the middleware never tries to access email from a null user.
        |
        */

        if (!$user) {
            Auth::logout();

            return redirect()
                ->route('login')
                ->with('error', 'Your session has expired. Please login again.');
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Admin Authorization Check
        |--------------------------------------------------------------------------
        |
        | Admin status now lives on the users table (`is_admin` column)
        | instead of a hardcoded email list. Promote a user with:
        |
        |   php artisan admin:promote user@example.com
        |
        */

        if (!$user->isAdmin()) {
            abort(
                403,
                'Unauthorized access. You do not have permission to access the admin panel.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Authorized Admin
        |--------------------------------------------------------------------------
        */

        return $next($request);
    }
}