<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSupervisorAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $access = $request->route('access');

        if (!$access instanceof \App\Models\SupervisorAccess) {
            abort(404);
        }

        if ($access->supervisor_id !== auth()->id()) {
            return $this->deny($request, 'Você não tem acesso a este painel.');
        }

        if ($access->isExpired()) {
            return $this->deny($request, 'O seu acesso como supervisor expirou.');
        }

        return $next($request);
    }

    protected function deny(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'supervisor_access_denied', 'message' => $message], 403);
        }

        return redirect()->route('supervisor.index')->with('error', $message);
    }
}
