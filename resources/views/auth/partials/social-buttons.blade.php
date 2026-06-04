@php
    $socialProviders = \App\Support\SocialAuthProviders::viewModels();
@endphp

<div class="social-auth" aria-label="Social authentication options">
    @foreach ($socialProviders as $provider => $socialProvider)
        @if ($socialProvider['configured'])
            <a class="social-auth-button social-auth-button-{{ $provider }}" href="{{ route('social.redirect', ['provider' => $provider]) }}" aria-label="Continue with {{ $socialProvider['label'] }}">
                @include('auth.partials.social-mark', ['provider' => $provider])
                <span>Continue with {{ $socialProvider['label'] }}</span>
            </a>
        @else
            <span class="social-auth-button social-auth-button-{{ $provider }} social-auth-button-disabled" aria-disabled="true" title="{{ $socialProvider['label'] }} sign-in is not configured yet.">
                @include('auth.partials.social-mark', ['provider' => $provider])
                <span>Continue with {{ $socialProvider['label'] }}</span>
            </span>
        @endif
    @endforeach
</div>
