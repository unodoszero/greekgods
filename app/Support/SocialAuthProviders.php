<?php

namespace App\Support;

use Illuminate\Support\Str;

class SocialAuthProviders
{
    /**
     * @var array<string, array{label: string, id_column: string, required_config: array<string, string>}>
     */
    private const PROVIDERS = [
        'google' => [
            'label' => 'Google',
            'id_column' => 'google_id',
            'required_config' => [
                'client_id' => 'GOOGLE_CLIENT_ID',
                'client_secret' => 'GOOGLE_CLIENT_SECRET',
                'redirect' => 'GOOGLE_REDIRECT_URI',
            ],
        ],
        'microsoft' => [
            'label' => 'Microsoft',
            'id_column' => 'microsoft_id',
            'required_config' => [
                'client_id' => 'MICROSOFT_CLIENT_ID',
                'client_secret' => 'MICROSOFT_CLIENT_SECRET',
                'redirect' => 'MICROSOFT_REDIRECT_URI',
            ],
        ],
    ];

    public static function supported(string $provider): bool
    {
        return array_key_exists($provider, self::PROVIDERS);
    }

    public static function label(string $provider): string
    {
        return self::PROVIDERS[$provider]['label'] ?? Str::headline($provider);
    }

    public static function idColumn(string $provider): string
    {
        abort_unless(self::supported($provider), 404);

        return self::PROVIDERS[$provider]['id_column'];
    }

    public static function configured(string $provider): bool
    {
        return self::missingConfigKeys($provider) === [];
    }

    /**
     * @return list<string>
     */
    public static function missingConfigKeys(string $provider): array
    {
        abort_unless(self::supported($provider), 404);

        $missingKeys = [];

        foreach (self::PROVIDERS[$provider]['required_config'] as $key => $envKey) {
            $value = config("services.{$provider}.{$key}");

            if (! is_string($value) || trim($value) === '') {
                $missingKeys[] = $envKey;
            }
        }

        return $missingKeys;
    }

    /**
     * @return array<string, array{label: string, configured: bool}>
     */
    public static function viewModels(): array
    {
        $providers = [];

        foreach (self::PROVIDERS as $provider => $config) {
            $providers[$provider] = [
                'label' => $config['label'],
                'configured' => self::configured($provider),
            ];
        }

        return $providers;
    }
}
