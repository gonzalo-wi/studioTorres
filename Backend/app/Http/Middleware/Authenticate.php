<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     * Compatible with Laravel 8 signature.
     */
    protected function redirectTo($request)
    {
        // API returns JSON; for non-JSON just return null (no web login route).
        return $request->expectsJson() ? null : null;
    }
}
