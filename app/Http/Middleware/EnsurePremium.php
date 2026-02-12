<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePremium
{
    public function handle(Request $request, Closure $next, ?string $feature = null): Response
    {
        $user = $request->user();

        if (!$user) {
            return $this->denyAccess($request);
        }

        // Se tem feature específica, verifica
        if ($feature && !$user->canUseFeature($feature)) {
            return $this->denyAccess($request);
        }

        // Se não tem feature, verifica se é premium
        if (!$feature && !$user->isPremium()) {
            return $this->denyAccess($request);
        }

        return $next($request);
    }

    protected function denyAccess(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'premium_required',
                'message' => 'Esta funcionalidade requer assinatura Premium.',
            ], 403);
        }

        return redirect()->route('subscription.plans')
            ->with('error', 'Esta funcionalidade requer assinatura Premium.');
    }
}
