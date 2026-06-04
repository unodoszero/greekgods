<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteSocialProfileRequest;
use App\Models\User;
use App\Support\AuthSessionIdentity;
use App\Support\SocialAuthProviders;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Masmerise\Toaster\Toaster;
use Throwable;

class SocialAuthController extends Controller
{
    private const PENDING_SESSION_KEY = 'social_auth.pending_user';

    public function redirect(string $provider): RedirectResponse
    {
        $this->ensureSupportedProvider($provider);

        if (! SocialAuthProviders::configured($provider)) {
            return $this->loginRedirectWithError($this->missingConfigMessage($provider));
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        $this->ensureSupportedProvider($provider);

        if (! SocialAuthProviders::configured($provider)) {
            return $this->loginRedirectWithError($this->missingConfigMessage($provider));
        }

        if ($request->filled('error')) {
            return $this->loginRedirectWithError(
                $this->providerLabel($provider).' sign-in was cancelled. Please try again if you want to continue.'
            );
        }

        try {
            $providerUser = Socialite::driver($provider)->user();
        } catch (InvalidStateException) {
            return $this->loginRedirectWithError(
                $this->providerLabel($provider).' sign-in expired. Please try again.'
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->loginRedirectWithError(
                'We could not complete '.$this->providerLabel($provider).' sign-in. Please try again.'
            );
        }

        $providerId = trim((string) $providerUser->getId());
        if ($providerId === '') {
            return $this->loginRedirectWithError(
                'We could not verify your '.$this->providerLabel($provider).' account. Please try again.'
            );
        }

        $email = $this->providerEmail($provider, $providerUser);
        if ($email === null) {
            return $this->loginRedirectWithError(
                $this->providerLabel($provider).' did not share an email address. Please use another sign-in method.'
            );
        }

        if (! $this->providerEmailIsUsable($provider, $providerUser)) {
            return $this->loginRedirectWithError(
                $this->providerLabel($provider).' did not confirm your email address. Please use another sign-in method.'
            );
        }

        return DB::transaction(function () use ($request, $provider, $providerUser, $providerId, $email): RedirectResponse {
            $providerColumn = $this->providerColumn($provider);
            $linkedUser = User::where($providerColumn, $providerId)->first();

            if ($linkedUser !== null) {
                $this->refreshSocialFields($linkedUser, $provider, $providerUser);
                $this->login($request, $linkedUser);
                Toaster::success($this->providerLabel($provider).' sign-in completed.');

                return redirect()->intended('/profile');
            }

            $existingUser = User::where('email', $email)->first();

            if ($existingUser !== null) {
                $existingProviderId = $existingUser->{$providerColumn};

                if ($existingProviderId !== null && $existingProviderId !== $providerId) {
                    return $this->loginRedirectWithError(
                        'That '.$this->providerLabel($provider).' account is already linked differently. Please use another sign-in method.'
                    );
                }

                $this->linkSocialAccount($existingUser, $provider, $providerId, $providerUser);
                $this->login($request, $existingUser);
                Toaster::success($this->providerLabel($provider).' account linked.');

                return redirect()->intended('/profile');
            }

            Toaster::info('Finish your profile to complete '.$this->providerLabel($provider).' sign-up.');

            $request->session()->put(self::PENDING_SESSION_KEY, [
                'provider' => $provider,
                'provider_id' => $providerId,
                'name' => $this->providerName($providerUser, $email),
                'email' => $email,
                'avatar' => $this->providerAvatar($providerUser),
            ]);

            return redirect()
                ->route('social.complete')
                ->with('status', 'Finish your profile to complete '.$this->providerLabel($provider).' sign-up.');
        });
    }

    public function showCompletion(Request $request): View
    {
        return view('auth.social-complete', [
            'pendingUser' => $request->session()->get(self::PENDING_SESSION_KEY),
        ]);
    }

    public function completeRegistration(CompleteSocialProfileRequest $request): RedirectResponse
    {
        $pendingUser = $request->session()->get(self::PENDING_SESSION_KEY);
        $provider = (string) $pendingUser['provider'];
        $providerColumn = $this->providerColumn($provider);
        $providerId = (string) $pendingUser['provider_id'];
        $email = strtolower(trim((string) $pendingUser['email']));

        return DB::transaction(function () use ($request, $pendingUser, $provider, $providerColumn, $providerId, $email): RedirectResponse {
            $linkedUser = User::where($providerColumn, $providerId)->first();
            if ($linkedUser !== null) {
                $request->session()->forget(self::PENDING_SESSION_KEY);
                $this->login($request, $linkedUser);
                Toaster::success($this->providerLabel($provider).' sign-in completed.');

                return redirect('/profile');
            }

            $existingUser = User::where('email', $email)->first();
            if ($existingUser !== null) {
                $existingProviderId = $existingUser->{$providerColumn};

                if ($existingProviderId !== null && $existingProviderId !== $providerId) {
                    $request->session()->forget(self::PENDING_SESSION_KEY);

                    return $this->loginRedirectWithError(
                        'An account already exists for this email. Please use another sign-in method.'
                    );
                }

                $existingUser->forceFill([
                    $providerColumn => $providerId,
                    'avatar' => $pendingUser['avatar'] ?: $existingUser->avatar,
                    'provider' => $provider,
                ])->save();

                $request->session()->forget(self::PENDING_SESSION_KEY);
                $this->login($request, $existingUser);
                Toaster::success($this->providerLabel($provider).' account linked.');

                return redirect('/profile');
            }

            $nameParts = $this->nameParts((string) $pendingUser['name'], $email);

            $user = User::create(array_merge($request->userAttributes(), [
                'email' => $email,
                'password' => Str::random(64),
                'first_name' => $nameParts['first_name'],
                'last_name' => $nameParts['last_name'],
                $providerColumn => $providerId,
                'avatar' => $pendingUser['avatar'],
                'provider' => $provider,
            ]));

            $request->session()->forget(self::PENDING_SESSION_KEY);
            $this->login($request, $user);
            Toaster::success('Account created successfully.');

            return redirect('/profile');
        });
    }

    private function ensureSupportedProvider(string $provider): void
    {
        abort_unless(SocialAuthProviders::supported($provider), 404);
    }

    private function providerColumn(string $provider): string
    {
        return SocialAuthProviders::idColumn($provider);
    }

    private function providerLabel(string $provider): string
    {
        return SocialAuthProviders::label($provider);
    }

    private function missingConfigMessage(string $provider): string
    {
        $missingKeys = implode(', ', SocialAuthProviders::missingConfigKeys($provider));

        return $this->providerLabel($provider)
            .' sign-in is not configured yet. Add '
            .$missingKeys
            .' to your .env file, then run php artisan config:clear.';
    }

    private function providerEmail(string $provider, SocialiteUser $providerUser): ?string
    {
        $email = $providerUser->getEmail();

        if ($provider === 'microsoft' && ($email === null || trim($email) === '')) {
            $rawUser = method_exists($providerUser, 'getRaw') ? $providerUser->getRaw() : [];
            $email = Arr::get($rawUser, 'mail') ?: Arr::get($rawUser, 'userPrincipalName');
        }

        $email = strtolower(trim((string) $email));

        return filter_var($email, FILTER_VALIDATE_EMAIL) === false ? null : $email;
    }

    private function providerEmailIsUsable(string $provider, SocialiteUser $providerUser): bool
    {
        if ($provider !== 'google' || ! method_exists($providerUser, 'getRaw')) {
            return true;
        }

        $rawUser = $providerUser->getRaw();
        $verified = Arr::get($rawUser, 'email_verified', Arr::get($rawUser, 'verified_email'));

        if ($verified === null) {
            return true;
        }

        return filter_var($verified, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === true;
    }

    private function providerName(SocialiteUser $providerUser, string $email): string
    {
        $name = trim((string) $providerUser->getName());

        if ($name === '') {
            $name = trim((string) $providerUser->getNickname());
        }

        return $name === '' ? Str::before($email, '@') : $name;
    }

    private function providerAvatar(SocialiteUser $providerUser): ?string
    {
        $avatar = trim((string) $providerUser->getAvatar());

        return $avatar === '' ? null : $avatar;
    }

    private function linkSocialAccount(User $user, string $provider, string $providerId, SocialiteUser $providerUser): void
    {
        $user->forceFill([
            $this->providerColumn($provider) => $providerId,
            'avatar' => $this->providerAvatar($providerUser) ?: $user->avatar,
            'provider' => $provider,
        ])->save();
    }

    private function refreshSocialFields(User $user, string $provider, SocialiteUser $providerUser): void
    {
        $user->forceFill([
            'avatar' => $this->providerAvatar($providerUser) ?: $user->avatar,
            'provider' => $provider,
        ])->save();
    }

    private function login(Request $request, User $user): void
    {
        Auth::login($user);
        $request->session()->regenerate();
        AuthSessionIdentity::store($request, $user);
    }

    /**
     * @return array{first_name: string, last_name: string}
     */
    private function nameParts(string $name, string $email): array
    {
        $name = trim(preg_replace('/\s+/', ' ', $name) ?? '');

        if ($name === '') {
            $name = Str::before($email, '@');
        }

        [$firstName, $lastName] = array_pad(explode(' ', $name, 2), 2, '');

        return [
            'first_name' => Str::limit($firstName === '' ? 'Social' : $firstName, 255, ''),
            'last_name' => Str::limit($lastName, 255, ''),
        ];
    }

    private function loginRedirectWithError(string $message): RedirectResponse
    {
        Toaster::error($message);

        return redirect()
            ->route('login')
            ->withErrors(['social' => $message]);
    }
}
