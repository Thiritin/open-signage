<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSharedSecretIsSetMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if(\App::isLocal() || empty(config('app.shared_secret'))) return $next($request);

        $sharedSecret = $request->get('shared_secret');
        if (empty($sharedSecret) || $sharedSecret !== config('app.shared_secret')) {
            abort(403, 'Shared secret is invalid');
        }

        return $next($request);
    }
}
