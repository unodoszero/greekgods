<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Masmerise\Toaster\Toaster;
use Symfony\Component\HttpFoundation\Response;

class EnsurePendingSocialRegistration
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $pendingUser = $request->session()->get('social_auth.pending_user');

        if (! is_array($pendingUser) || ! $this->hasRequiredFields($pendingUser)) {
            $request->session()->forget('social_auth.pending_user');
            Toaster::error('Your social sign-up session expired. Please try again.');

            return redirect()
                ->route('login')
                ->withErrors(['social' => 'Your social sign-up session expired. Please try again.']);
        }

        return $next($request);
    }

    /**
     * @param  array<string, mixed>  $pendingUser
     */
    private function hasRequiredFields(array $pendingUser): bool
    {
        if (! in_array($pendingUser['provider'] ?? null, ['google', 'microsoft'], true)) {
            return false;
        }

        foreach (['provider_id', 'email', 'name'] as $field) {
            if (! is_string($pendingUser[$field] ?? null) || trim($pendingUser[$field]) === '') {
                return false;
            }
        }

        return true;
    }
}
