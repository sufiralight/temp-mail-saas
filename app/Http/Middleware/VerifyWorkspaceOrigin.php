<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWorkspaceOrigin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST')) {
            $expected = $request->getSchemeAndHttpHost();
            $origin = $request->headers->get('Origin');
            $sameOrigin = $origin !== null
                ? $origin === $expected
                : str_starts_with($request->headers->get('Referer', ''), $expected.'/');

            abort_unless($sameOrigin, 403);
        }

        $response = $next($request);
        $response->headers->set('Cache-Control', 'private, no-store');

        return $response;
    }
}
