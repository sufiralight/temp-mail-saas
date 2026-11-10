<?php

namespace App\Http\Middleware;

use App\Support\WorkspaceInstallation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireWorkspace
{
    public function __construct(private WorkspaceInstallation $installation) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->installation->ready()) {
            return to_route('install');
        }

        return $next($request);
    }
}
