<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AuthSessionIdentity;
use App\Support\WorkoutSplitCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as SocialiteUser;
use Masmerise\Toaster\Toaster;
use Tests\TestCase;

class LaravelMigrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->configureSocialProviders();
    }

    public function test_public_pages_and_legacy_redirects_work(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('/files//', false)
            ->assertSee('href="/about"', false);
        $this->get('/blog')
            ->assertOk()
            ->assertDontSee('/files//', false)
            ->assertSee('href="/about"', false);
        $this->get('/articles/bmi')
            ->assertOk()
            ->assertDontSee('/files//', false)
            ->assertSee('href="/about"', false);
        $this->get('/calculator')
            ->assertOk()
            ->assertSee('id="toaster"', false)
            ->assertSee('/files/calculator.js', false);
        $this->get('/laws')
            ->assertOk()
            ->assertSee('id="toaster"', false)
            ->assertSee('/files/laws.js', false);
        $this->get('/files/about')->assertRedirect('/about');
        $this->get('/files//about')->assertRedirect('/about');
        $this->get('/articles/bmi.php')->assertRedirect('/articles/bmi');
    }

    public function test_layout_includes_toaster_livewire_and_vite_assets(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('id="toaster"', false)
            ->assertSee('livewire', false);

        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertIsString($layout);
        $this->assertStringContainsString('@livewireStyles', $layout);
        $this->assertStringContainsString('<x-toaster-hub />', $layout);
        $this->assertStringContainsString("@vite('resources/js/app.js')", $layout);
        $this->assertStringContainsString('@livewireScripts', $layout);
        $this->assertLessThan(
            strpos($layout, '@livewireScripts'),
            strpos($layout, "@vite('resources/js/app.js')")
        );

        $appJs = file_get_contents(resource_path('js/app.js'));

        $this->assertIsString($appJs);
        $this->assertStringContainsString('window.GreekGodsToast', $appJs);
        $this->assertStringContainsString('window.GreekGodsConfirm', $appJs);
    }

    public function test_active_public_scripts_do_not_use_native_browser_dialogs(): void
    {
        $scriptPaths = glob(public_path('files/*.js')) ?: [];

        $this->assertNotEmpty($scriptPaths);

        foreach ($scriptPaths as $scriptPath) {
            $script = file_get_contents($scriptPath);

            $this->assertIsString($script);
            $this->assertDoesNotMatchRegularExpression(
                '/\b(?:alert|confirm|prompt)\s*\(/',
                $script,
                basename($scriptPath).' should use app UI and toaster feedback instead of native browser dialogs.'
            );
        }
    }

    public function test_program_script_uses_optimistic_destructive_updates(): void
    {
        $script = file_get_contents(public_path('files/program.js'));

        $this->assertIsString($script);
        $this->assertStringContainsString('previousState', $script);
        $this->assertStringContainsString('previousWorkouts', $script);
        $this->assertStringContainsString('setPending(true)', $script);
        $this->assertStringContainsString('Saving changes...', $script);
    }

    public function test_profile_routes_require_authentication(): void
    {
        $this->get('/profile')
            ->assertRedirect('/login')
            ->assertDontSee('Welcome,');
        $this->get('/files/profile.php')
            ->assertRedirect('/login')
            ->assertDontSee('Welcome,');

        $this->getJson('/profile/data')->assertUnauthorized();
        $this->getJson('/includes/users/fetch_user_data.php')->assertUnauthorized();
        $this->patchJson('/profile/account')->assertUnauthorized();
        $this->patchJson('/profile/body-metrics')->assertUnauthorized();
        $this->patchJson('/profile/password')->assertUnauthorized();

        $this->assertGuest();
    }

    public function test_guest_nav_routes_get_started_to_registration(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertSee('const userId = null;', false)
            ->assertSee('const userFullName = "";', false)
            ->assertSee('<button id="register-button" onclick="window.location.href=\'/register\'">GET STARTED</button>', false)
            ->assertSee('<button id="profile-button" onclick="window.location.href=\'/profile\'" hidden style="display: none;">', false)
            ->assertSee('<span id="profile-name" hidden style="display: none;"></span>', false);
    }

    public function test_authenticated_nav_shows_clickable_user_name_on_public_pages(): void
    {
        $user = $this->createUser([
            'email' => 'navuser@gmail.com',
            'first_name' => 'Nav',
            'last_name' => 'User',
        ]);

        foreach (['/', '/about', '/blog', '/calculator'] as $path) {
            $this->withSession([
                AuthSessionIdentity::USER_ID => $user->id,
                AuthSessionIdentity::FULL_NAME => 'Nav User',
                AuthSessionIdentity::FIRST_NAME => 'Nav',
            ])
                ->get($path)
                ->assertOk()
                ->assertSee('const userId = '.$user->id.';', false)
                ->assertSee('const userFullName = "Nav User";', false)
                ->assertSee('<button id="register-button"', false)
                ->assertSee('hidden style="display: none;">GET STARTED</button>', false)
                ->assertSee('<button id="profile-button" onclick="window.location.href=\'/profile\'" style="display: inline-flex;" aria-label="Open profile">', false)
                ->assertSee('<span id="profile-name" style="display: inline;">Nav User</span>', false);
        }

        $this->withSession([
            AuthSessionIdentity::USER_ID => $user->id,
            AuthSessionIdentity::FULL_NAME => 'Nav User',
            AuthSessionIdentity::FIRST_NAME => 'Nav',
        ])
            ->get('/laws')
            ->assertOk()
            ->assertDontSee('GET STARTED');

        $this->actingAs($user)
            ->get('/program')
            ->assertOk()
            ->assertSee('const userFullName = "Nav User";', false)
            ->assertSee('<button id="profile-button" style="display: inline-flex;" aria-label="Open profile" onclick="window.location.href=\'/profile\'">', false)
            ->assertSee('<span id="profile-name" style="display: inline;">Nav User</span>', false);

        $navScript = file_get_contents(base_path('index.js'));
        $publicNavScript = file_get_contents(public_path('index.js'));
        $navStyles = file_get_contents(base_path('index.css'));
        $publicNavStyles = file_get_contents(public_path('index.css'));

        $this->assertIsString($navScript);
        $this->assertIsString($publicNavScript);
        $this->assertSame($navScript, $publicNavScript);
        $this->assertIsString($navStyles);
        $this->assertIsString($publicNavStyles);
        $this->assertSame($navStyles, $publicNavStyles);
        $this->assertStringNotContainsString('navButton.textContent = fullName', $navScript);
        $this->assertStringNotContainsString('navButton.style.display', $navScript);
        $this->assertStringNotContainsString('navProfile.style.display', $navScript);
        $this->assertStringNotContainsString('#profile-name {'.PHP_EOL.'    display: none;', $navStyles);
    }

    public function test_authenticated_profile_nav_uses_logout_without_get_started(): void
    {
        $user = $this->createUser([
            'email' => 'profilelogout@gmail.com',
            'first_name' => 'Profile',
            'last_name' => 'User',
        ]);

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee('<button id="logout" type="button">LOGOUT</button>', false)
            ->assertDontSee('GET STARTED');
    }

    public function test_user_can_register_and_reach_profile(): void
    {
        Toaster::fake();

        $response = $this->post('/register', [
            'email' => 'ada@gmail.com',
            'password' => 'V3ry!UniquePassphrase2026#Greekgods',
            'password_confirmation' => 'V3ry!UniquePassphrase2026#Greekgods',
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'birthdate' => '1990-01-01',
            'height_value' => 1.7,
            'height_unit' => 'm',
            'weight_value' => 65,
            'weight_unit' => 'kg',
            'activity' => 'moderate',
            'sex' => 'female',
            'check' => '1',
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas(AuthSessionIdentity::USER_ID);
        $response->assertSessionHas(AuthSessionIdentity::FULL_NAME, 'Ada Lovelace');
        $response->assertSessionHas(AuthSessionIdentity::FIRST_NAME, 'Ada');
        Toaster::assertDispatched('Account created successfully.');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'ada@gmail.com',
            'height_value' => '1.7',
            'height_unit' => 'm',
            'weight_value' => '65',
            'weight_unit' => 'kg',
            'sex' => 'female',
        ]);
    }

    public function test_login_failure_success_and_logout_dispatch_toasts(): void
    {
        $user = $this->createUser([
            'email' => 'loginuser@gmail.com',
            'password' => 'Current!123Password',
        ]);

        Toaster::fake();

        $this->from('/login')
            ->post('/login', [
                'email' => 'loginuser@gmail.com',
                'password' => 'wrong-password',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email']);

        Toaster::assertDispatched('Invalid email or password.');

        Toaster::fake();

        $this->post('/login', [
            'email' => 'loginuser@gmail.com',
            'password' => 'Current!123Password',
        ])
            ->assertRedirect('/profile')
            ->assertSessionHas(AuthSessionIdentity::USER_ID, $user->id)
            ->assertSessionHas(AuthSessionIdentity::FULL_NAME, 'Default User')
            ->assertSessionHas(AuthSessionIdentity::FIRST_NAME, 'Default');

        Toaster::assertDispatched('Logged in successfully.');
        $this->assertAuthenticatedAs($user);

        Toaster::fake();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login')
            ->assertSessionMissing(AuthSessionIdentity::USER_ID)
            ->assertSessionMissing(AuthSessionIdentity::FULL_NAME)
            ->assertSessionMissing(AuthSessionIdentity::FIRST_NAME);

        Toaster::assertDispatched('Logged out successfully.');
        $this->assertGuest();
    }

    public function test_login_and_register_render_social_auth_buttons(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Continue with Google')
            ->assertSee('Continue with Microsoft')
            ->assertSee('/auth/google/redirect', false)
            ->assertSee('/auth/microsoft/redirect', false);

        $this->get('/register')
            ->assertOk()
            ->assertSee('Continue with Google')
            ->assertSee('Continue with Microsoft')
            ->assertSee('/auth/google/redirect', false)
            ->assertSee('/auth/microsoft/redirect', false);
    }

    public function test_social_redirect_routes_use_configured_providers(): void
    {
        Socialite::fake('google');
        Socialite::fake('microsoft');

        $this->get('/auth/google/redirect')
            ->assertRedirect('https://socialite.fake/google/authorize');

        $this->get('/auth/microsoft/redirect')
            ->assertRedirect('https://socialite.fake/microsoft/authorize');

        $this->get('/auth/not-real/redirect')->assertNotFound();
    }

    public function test_social_redirect_requires_provider_credentials_before_leaving_the_app(): void
    {
        Config::set('services.google.client_id', null);
        Config::set('services.google.client_secret', null);

        Toaster::fake();

        $this->get('/auth/google/redirect')
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['social']);

        Toaster::assertDispatched('Google sign-in is not configured yet. Add GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET to your .env file, then run php artisan config:clear.');

        $this->get('/login')
            ->assertOk()
            ->assertSee('Continue with Google')
            ->assertSee('social-auth-button-disabled', false)
            ->assertDontSee('/auth/google/redirect', false);
    }

    public function test_social_callback_requires_provider_credentials_before_calling_provider(): void
    {
        Config::set('services.microsoft.client_id', null);

        Toaster::fake();

        Socialite::fake('microsoft', $this->socialiteUser(
            id: 'should-not-be-used',
            email: 'unused@gmail.com',
            name: 'Unused User'
        ));

        $this->get('/auth/microsoft/callback')
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['social']);

        Toaster::assertDispatched('Microsoft sign-in is not configured yet. Add MICROSOFT_CLIENT_ID to your .env file, then run php artisan config:clear.');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'unused@gmail.com']);
    }

    public function test_google_callback_links_existing_email_account_and_logs_in(): void
    {
        $user = $this->createUser([
            'email' => 'socialuser@gmail.com',
            'first_name' => 'Social',
            'last_name' => 'User',
        ]);

        Socialite::fake('google', $this->socialiteUser(
            id: 'google-123',
            email: 'SOCIALUSER@gmail.com',
            name: 'Social User',
            avatar: 'https://example.com/avatar/google.jpg'
        ));

        Toaster::fake();

        $this->get('/auth/google/callback')
            ->assertRedirect('/profile');

        Toaster::assertDispatched('Google account linked.');
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'google_id' => 'google-123',
            'avatar' => 'https://example.com/avatar/google.jpg',
            'provider' => 'google',
        ]);
    }

    public function test_microsoft_callback_links_existing_email_account_and_logs_in(): void
    {
        $user = $this->createUser([
            'email' => 'outlookuser@gmail.com',
            'first_name' => 'Outlook',
            'last_name' => 'User',
        ]);

        Socialite::fake('microsoft', $this->socialiteUser(
            id: 'microsoft-123',
            email: 'outlookuser@gmail.com',
            name: 'Outlook User',
            avatar: null,
            raw: ['mail' => 'outlookuser@gmail.com', 'userPrincipalName' => 'outlookuser@gmail.com']
        ));

        Toaster::fake();

        $this->get('/auth/microsoft/callback')
            ->assertRedirect('/profile');

        Toaster::assertDispatched('Microsoft account linked.');
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'microsoft_id' => 'microsoft-123',
            'provider' => 'microsoft',
        ]);
    }

    public function test_new_social_user_is_sent_to_profile_completion_before_account_creation(): void
    {
        Socialite::fake('google', $this->socialiteUser(
            id: 'google-new-123',
            email: 'newgoogle@gmail.com',
            name: 'New Google',
            avatar: 'https://example.com/avatar/new-google.jpg'
        ));

        Toaster::fake();

        $this->get('/auth/google/callback')
            ->assertRedirect('/auth/social/complete')
            ->assertSessionHas('social_auth.pending_user', function (array $pendingUser): bool {
                return $pendingUser['provider'] === 'google'
                    && $pendingUser['provider_id'] === 'google-new-123'
                    && $pendingUser['email'] === 'newgoogle@gmail.com';
            });

        Toaster::assertDispatched('Finish your profile to complete Google sign-up.');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'newgoogle@gmail.com']);
    }

    public function test_new_microsoft_user_is_sent_to_profile_completion_before_account_creation(): void
    {
        Socialite::fake('microsoft', $this->socialiteUser(
            id: 'microsoft-new-callback-123',
            email: 'newmicrosoft@gmail.com',
            name: 'New Microsoft',
            raw: ['mail' => 'newmicrosoft@gmail.com', 'userPrincipalName' => 'newmicrosoft@gmail.com']
        ));

        Toaster::fake();

        $this->get('/auth/microsoft/callback')
            ->assertRedirect('/auth/social/complete')
            ->assertSessionHas('social_auth.pending_user', function (array $pendingUser): bool {
                return $pendingUser['provider'] === 'microsoft'
                    && $pendingUser['provider_id'] === 'microsoft-new-callback-123'
                    && $pendingUser['email'] === 'newmicrosoft@gmail.com';
            });

        Toaster::assertDispatched('Finish your profile to complete Microsoft sign-up.');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'newmicrosoft@gmail.com']);
    }

    public function test_social_profile_completion_creates_user_and_logs_in(): void
    {
        $pendingUser = [
            'provider' => 'microsoft',
            'provider_id' => 'microsoft-new-123',
            'name' => 'Microsoft Athlete',
            'email' => 'microsoftathlete@gmail.com',
            'avatar' => 'https://example.com/avatar/microsoft.jpg',
        ];

        Toaster::fake();

        $this->withSession(['social_auth.pending_user' => $pendingUser])
            ->post('/auth/social/complete', [
                'birthdate' => '1994-05-20',
                'height_value' => '5.10',
                'height_unit' => 'ft',
                'weight_value' => '180',
                'weight_unit' => 'lb',
                'activity' => 'active',
                'sex' => 'male',
                'check' => '1',
            ])
            ->assertRedirect('/profile')
            ->assertSessionMissing('social_auth.pending_user');

        Toaster::assertDispatched('Account created successfully.');
        $user = User::where('email', 'microsoftathlete@gmail.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertNotEmpty($user->password);
        $this->assertFalse(Hash::check('password', $user->password));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Microsoft',
            'last_name' => 'Athlete',
            'microsoft_id' => 'microsoft-new-123',
            'provider' => 'microsoft',
            'height_value' => '5.10',
            'height_unit' => 'ft',
            'weight_value' => '180',
            'weight_unit' => 'lb',
            'sex' => 'male',
        ]);
    }

    public function test_social_callbacks_handle_cancelled_missing_email_and_conflicts(): void
    {
        Toaster::fake();

        $this->get('/auth/google/callback?error=access_denied')
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['social']);

        Socialite::fake('microsoft', fn () => throw new InvalidStateException);

        $this->get('/auth/microsoft/callback')
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['social']);

        Socialite::fake('google', $this->socialiteUser(
            id: 'google-no-email',
            email: null,
            name: 'No Email User'
        ));

        $this->get('/auth/google/callback')
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['social']);

        Socialite::fake('google', $this->socialiteUser(
            id: 'google-unverified-email',
            email: 'unverified@gmail.com',
            name: 'Unverified User',
            raw: ['email_verified' => false]
        ));

        $this->get('/auth/google/callback')
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['social']);

        $this->createUser([
            'email' => 'conflict@gmail.com',
            'google_id' => 'already-linked-google-id',
        ]);

        Socialite::fake('google', $this->socialiteUser(
            id: 'different-google-id',
            email: 'conflict@gmail.com',
            name: 'Conflict User'
        ));

        $this->get('/auth/google/callback')
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['social']);

        Toaster::assertDispatched('Google sign-in was cancelled. Please try again if you want to continue.');
        Toaster::assertDispatched('Microsoft sign-in expired. Please try again.');
        Toaster::assertDispatched('Google did not share an email address. Please use another sign-in method.');
        Toaster::assertDispatched('Google did not confirm your email address. Please use another sign-in method.');
        Toaster::assertDispatched('That Google account is already linked differently. Please use another sign-in method.');
        $this->assertGuest();
    }

    public function test_social_completion_requires_pending_session(): void
    {
        Toaster::fake();

        $this->get('/auth/social/complete')
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['social']);

        Toaster::assertDispatched('Your social sign-up session expired. Please try again.');
    }

    public function test_registration_requires_valid_terms_password_activity_and_sex(): void
    {
        $response = $this->from('/register')->post('/register', [
            'email' => 'bad@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'first_name' => 'Bad',
            'last_name' => 'Input',
            'birthdate' => '1990-01-01',
            'height_value' => 1.7,
            'height_unit' => 'm',
            'weight_value' => 65,
            'weight_unit' => 'kg',
            'activity' => 'invalid',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password', 'activity', 'sex', 'check']);
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'bad@gmail.com']);
    }

    public function test_registration_normalizes_email_and_rejects_duplicate_email(): void
    {
        User::create([
            'email' => 'ada@gmail.com',
            'password' => 'V3ry!UniquePassphrase2026#Greekgods',
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'birthdate' => '1990-01-01',
            'height' => 1.7,
            'weight' => 65,
            'activity' => 'moderate',
        ]);

        $response = $this->from('/register')->post('/register', [
            'email' => ' ADA@GMAIL.COM ',
            'password' => 'V3ry!UniquePassphrase2026#Greekgods',
            'password_confirmation' => 'V3ry!UniquePassphrase2026#Greekgods',
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'birthdate' => '1990-01-01',
            'height_value' => 1.7,
            'height_unit' => 'm',
            'weight_value' => 65,
            'weight_unit' => 'kg',
            'activity' => 'moderate',
            'sex' => 'female',
            'check' => '1',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_registration_converts_selected_height_and_weight_units(): void
    {
        $response = $this->post('/register', [
            'email' => 'unitconvert@gmail.com',
            'password' => 'V3ry!UniquePassphrase2026#Greekgods',
            'password_confirmation' => 'V3ry!UniquePassphrase2026#Greekgods',
            'first_name' => 'Unit',
            'last_name' => 'Convert',
            'birthdate' => '1990-01-01',
            'height_value' => '5.7',
            'height_unit' => 'ft',
            'weight_value' => '75',
            'weight_unit' => 'kg',
            'activity' => 'moderate',
            'sex' => 'female',
            'check' => '1',
        ]);

        $response->assertRedirect('/profile');

        $user = User::where('email', 'unitconvert@gmail.com')->firstOrFail();
        $this->assertEqualsWithDelta(1.70, (float) $user->height, 0.01);
        $this->assertEqualsWithDelta(75.00, (float) $user->weight, 0.01);
        $this->assertSame('5.7', $user->height_value);
        $this->assertSame('ft', $user->height_unit);
        $this->assertSame('75', $user->weight_value);
        $this->assertSame('kg', $user->weight_unit);

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee('5.7 ft')
            ->assertSee('75 kg')
            ->assertSee('25.95')
            ->assertSee('Overweight');
    }

    public function test_registration_converts_feet_inches_and_pounds(): void
    {
        $response = $this->post('/register', [
            'email' => 'feetpounds@gmail.com',
            'password' => 'V3ry!UniquePassphrase2026#Greekgods',
            'password_confirmation' => 'V3ry!UniquePassphrase2026#Greekgods',
            'first_name' => 'Feet',
            'last_name' => 'Pounds',
            'birthdate' => '1990-01-01',
            'height_value' => '5.11',
            'height_unit' => 'ft',
            'weight_value' => '165',
            'weight_unit' => 'lb',
            'activity' => 'moderate',
            'sex' => 'male',
            'check' => '1',
        ]);

        $response->assertRedirect('/profile');

        $user = User::where('email', 'feetpounds@gmail.com')->firstOrFail();
        $this->assertEqualsWithDelta(1.80, (float) $user->height, 0.01);
        $this->assertEqualsWithDelta(74.84, (float) $user->weight, 0.01);
        $this->assertSame('5.11', $user->height_value);
        $this->assertSame('ft', $user->height_unit);
        $this->assertSame('165', $user->weight_value);
        $this->assertSame('lb', $user->weight_unit);
    }

    public function test_registration_rejects_invalid_feet_inches_shorthand(): void
    {
        $response = $this->from('/register')->post('/register', [
            'email' => 'badheight@gmail.com',
            'password' => 'V3ry!UniquePassphrase2026#Greekgods',
            'password_confirmation' => 'V3ry!UniquePassphrase2026#Greekgods',
            'first_name' => 'Bad',
            'last_name' => 'Height',
            'birthdate' => '1990-01-01',
            'height_value' => '5.13',
            'height_unit' => 'ft',
            'weight_value' => '75',
            'weight_unit' => 'kg',
            'activity' => 'moderate',
            'sex' => 'female',
            'check' => '1',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['height_value']);
        $this->assertDatabaseMissing('users', ['email' => 'badheight@gmail.com']);
    }

    public function test_profile_displays_registered_user_information(): void
    {
        $user = User::create([
            'email' => 'athena@gmail.com',
            'password' => 'V3ry!UniquePassphrase2026#Greekgods',
            'first_name' => 'Athena',
            'last_name' => 'Nike',
            'birthdate' => '1995-05-10',
            'height' => 1.72,
            'weight' => 68,
            'activity' => 'active',
            'sex' => 'male',
        ]);

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee('Welcome,')
            ->assertSee('Athena Nike')
            ->assertSee('athena@gmail.com')
            ->assertSee('1995-05-10')
            ->assertSee('Sex')
            ->assertSee('Male')
            ->assertSee('1.72 m')
            ->assertSee('68.00 kg')
            ->assertSee('22.99')
            ->assertSee('Normal weight')
            ->assertSee('Fitness metrics')
            ->assertSee("Today's workout", false)
            ->assertSee('No workouts scheduled for today.')
            ->assertSee('Edit information')
            ->assertDontSee('Profile basics');
    }

    public function test_account_settings_update_name_email_and_reject_duplicate_email(): void
    {
        $user = $this->createUser(['email' => 'hera@gmail.com']);
        $this->createUser(['email' => 'takenprofile@gmail.com']);

        $this->actingAs($user)
            ->patchJson('/profile/account', [
                'first_name' => 'Hera',
                'last_name' => 'Olympia',
                'email' => 'updatedprofile@gmail.com',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Account details updated.')
            ->assertJsonPath('user.email', 'updatedprofile@gmail.com')
            ->assertJsonPath('user.fullName', 'Hera Olympia')
            ->assertSessionHas(AuthSessionIdentity::USER_ID, $user->id)
            ->assertSessionHas(AuthSessionIdentity::FULL_NAME, 'Hera Olympia')
            ->assertSessionHas(AuthSessionIdentity::FIRST_NAME, 'Hera');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'updatedprofile@gmail.com',
            'first_name' => 'Hera',
            'last_name' => 'Olympia',
        ]);

        $duplicateResponse = $this->actingAs($user)
            ->patchJson('/profile/account', [
                'first_name' => 'Hera',
                'last_name' => 'Olympia',
                'email' => 'takenprofile@gmail.com',
            ]);

        $duplicateResponse->assertStatus(422);
        $this->assertArrayHasKey('email', $duplicateResponse->json('errors'));
    }

    public function test_body_metrics_update_and_reject_invalid_values(): void
    {
        $user = $this->createUser(['email' => 'ares@gmail.com']);

        $this->actingAs($user)
            ->patchJson('/profile/body-metrics', [
                'birthdate' => '1992-03-12',
                'height_value' => '5.11',
                'height_unit' => 'ft',
                'weight_value' => '190',
                'weight_unit' => 'lb',
                'activity' => 'very_active',
                'sex' => 'male',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Body metrics updated.')
            ->assertJsonPath('user.sex', 'male')
            ->assertJsonPath('user.heightDisplay', '5.11 ft')
            ->assertJsonPath('user.weightDisplay', '190 lb')
            ->assertJsonPath('metrics.canEstimateCalories', true);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'height_value' => '5.11',
            'height_unit' => 'ft',
            'weight_value' => '190',
            'weight_unit' => 'lb',
            'activity' => 'very_active',
            'sex' => 'male',
        ]);

        $invalidResponse = $this->actingAs($user)
            ->patchJson('/profile/body-metrics', [
                'birthdate' => '2020-01-01',
                'height_value' => '5.13',
                'height_unit' => 'ft',
                'weight_value' => '10',
                'weight_unit' => 'kg',
                'activity' => 'every_hour',
                'sex' => 'robot',
            ]);

        $invalidResponse->assertStatus(422);
        foreach (['birthdate', 'height_value', 'weight_value', 'activity', 'sex'] as $field) {
            $this->assertArrayHasKey($field, $invalidResponse->json('errors'));
        }
    }

    public function test_password_update_requires_current_password_and_hashes_new_password(): void
    {
        $user = $this->createUser([
            'email' => 'apollo@gmail.com',
            'password' => 'Current!123Password',
        ]);

        $wrongPasswordResponse = $this->actingAs($user)
            ->patchJson('/profile/password', [
                'current_password' => 'wrong-password',
                'password' => 'N3w!UniquePassphrase2026#Greekgods',
                'password_confirmation' => 'N3w!UniquePassphrase2026#Greekgods',
            ]);

        $wrongPasswordResponse->assertStatus(422);
        $this->assertArrayHasKey('current_password', $wrongPasswordResponse->json('errors'));

        $this->actingAs($user)
            ->patchJson('/profile/password', [
                'current_password' => 'Current!123Password',
                'password' => 'N3w!UniquePassphrase2026#Greekgods',
                'password_confirmation' => 'N3w!UniquePassphrase2026#Greekgods',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Password updated.');

        $user->refresh();

        $this->assertFalse(Hash::check('Current!123Password', $user->password));
        $this->assertTrue(Hash::check('N3w!UniquePassphrase2026#Greekgods', $user->password));
    }

    public function test_users_without_calorie_sex_see_bmi_without_exact_bmr_tdee(): void
    {
        $user = $this->createUser([
            'email' => 'hermes@gmail.com',
            'height' => 1.72,
            'weight' => 68,
            'sex' => 'prefer_not_to_say',
        ]);

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee('22.99')
            ->assertSee('Normal weight')
            ->assertSee('Complete sex in Body Metrics to estimate BMR and TDEE.')
            ->assertDontSee('kcal/day');

        $this->actingAs($user)
            ->getJson('/profile/data')
            ->assertOk()
            ->assertJsonPath('metrics.canEstimateCalories', false)
            ->assertJsonPath('metrics.bmr', null)
            ->assertJsonPath('metrics.tdee', null);
    }

    public function test_authenticated_user_can_save_program_then_manage_workouts(): void
    {
        $user = User::create([
            'email' => 'grace@example.com',
            'password' => 'Password!123',
            'first_name' => 'Grace',
            'last_name' => 'Hopper',
            'birthdate' => '1988-01-01',
            'height' => 1.65,
            'weight' => 60,
            'activity' => 'active',
        ]);

        $this->actingAs($user)
            ->post('/program', [
                'split' => 'full-body',
                'schedule' => '3-day-eod',
            ])
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('message', 'Program saved. Preconfigured workouts added.')
            ->assertJsonCount(18, 'workouts')
            ->assertJsonPath('workouts.0.workoutSets', null)
            ->assertJsonPath('workouts.0.workoutReps', null);

        $this->assertDatabaseHas('programs', [
            'user_id' => $user->id,
            'program' => 'full-body',
            'schedule' => '3-day-eod',
        ]);
        $this->assertDatabaseHas('workouts', [
            'user_id' => $user->id,
            'workout_day' => 'Monday',
            'workout_name' => 'Incline Bench Press',
            'workout_focus' => 'Chest',
            'workout_sets' => null,
            'workout_reps' => null,
        ]);

        $addResponse = $this->actingAs($user)
            ->postJson('/program/workouts', [
                'day' => 'Monday',
                'workout_name' => 'Bench Press',
                'workout_sets' => 3,
                'workout_reps' => 10,
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Workout added.')
            ->assertJsonPath('workout.workoutDay', 'Monday')
            ->assertJsonPath('workout.workoutName', 'Bench Press');

        $workoutId = $addResponse->json('workout.id');
        $this->assertDatabaseHas('workouts', [
            'id' => $workoutId,
            'user_id' => $user->id,
            'workout_name' => 'Bench Press',
            'workout_sets' => 3,
            'workout_reps' => 10,
        ]);

        $this->actingAs($user)
            ->patchJson("/program/workouts/{$workoutId}", [
                'day' => 'Wednesday',
                'workout_name' => 'Incline Bench Press',
                'workout_sets' => 4,
                'workout_reps' => 8,
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Workout updated.')
            ->assertJsonPath('workout.workoutDay', 'Wednesday')
            ->assertJsonPath('workout.workoutName', 'Incline Bench Press');

        $this->assertDatabaseHas('workouts', [
            'id' => $workoutId,
            'workout_day' => 'Wednesday',
            'workout_name' => 'Incline Bench Press',
        ]);

        $this->actingAs($user)
            ->deleteJson("/program/workouts/{$workoutId}")
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('message', 'Workout removed.');

        $this->assertDatabaseMissing('workouts', ['id' => $workoutId]);

        $this->actingAs($user)
            ->postJson('/program/workouts', [
                'day' => 'Friday',
                'workout_name' => 'Squat',
                'workout_sets' => 5,
                'workout_reps' => 5,
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Workout added.');

        $this->actingAs($user)
            ->delete('/program')
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('message', 'Program and workouts deleted successfully');
        $this->assertDatabaseMissing('programs', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('workouts', ['user_id' => $user->id]);
    }

    public function test_ppl_upper_sharms_program_seeds_user_template_without_metrics(): void
    {
        $user = $this->createUser(['email' => 'sharmsprogram@gmail.com']);

        $this->actingAs($user)
            ->postJson('/program', [
                'split' => 'ppl-upper-sharms',
                'schedule' => '5-day',
            ])
            ->assertOk()
            ->assertJsonPath('program.split', 'ppl-upper-sharms')
            ->assertJsonPath('message', 'Program saved. Preconfigured workouts added.')
            ->assertJsonCount(30, 'workouts')
            ->assertJsonPath('workouts.0.workoutDay', 'Monday')
            ->assertJsonPath('workouts.0.workoutName', 'Smith Machine Incline Bench Press')
            ->assertJsonPath('workouts.0.workoutFocus', 'Upper Chest')
            ->assertJsonPath('workouts.0.workoutSets', null)
            ->assertJsonPath('workouts.0.workoutReps', null);

        $this->assertDatabaseHas('programs', [
            'user_id' => $user->id,
            'program' => 'ppl-upper-sharms',
            'schedule' => '5-day',
        ]);
        $this->assertDatabaseHas('workouts', [
            'user_id' => $user->id,
            'workout_day' => 'Saturday',
            'workout_name' => 'Shoulder Press',
            'workout_focus' => 'Front Delt',
            'workout_sets' => null,
            'workout_reps' => null,
        ]);
        $this->assertDatabaseHas('workouts', [
            'user_id' => $user->id,
            'workout_day' => 'Wednesday',
            'workout_name' => 'Supported Leg Raise',
            'workout_focus' => 'Lower Abs',
            'workout_sets' => null,
            'workout_reps' => null,
        ]);
    }

    public function test_every_training_day_has_preconfigured_template_data(): void
    {
        $templates = config('preconfigured_workouts');

        foreach (WorkoutSplitCatalog::all() as $splitId => $split) {
            $this->assertArrayHasKey($splitId, $templates);

            foreach ($split['schedules'] as $schedule) {
                foreach ($schedule['days'] as $dayLabel) {
                    if ($dayLabel === 'Rest') {
                        continue;
                    }

                    $this->assertArrayHasKey($dayLabel, $templates[$splitId], "{$splitId} is missing {$dayLabel} template data.");
                    $this->assertNotEmpty($templates[$splitId][$dayLabel], "{$splitId} {$dayLabel} template data is empty.");

                    foreach ($templates[$splitId][$dayLabel] as $workout) {
                        $this->assertNotEmpty($workout['name'] ?? null);
                        $this->assertDoesNotMatchRegularExpression('/\d+\s*(lbs?|reps?|r)\b/i', implode(' ', $workout));
                    }
                }
            }
        }
    }

    public function test_program_rejects_invalid_schedules_and_rest_day_workouts(): void
    {
        $user = $this->createUser(['email' => 'programrules@gmail.com']);

        $this->actingAs($user)
            ->postJson('/program', [
                'split' => 'not-a-split',
                'schedule' => '3-day',
            ])
            ->assertStatus(422);

        $this->actingAs($user)
            ->postJson('/program/workouts', [
                'day' => 'Monday',
                'workout_name' => 'Bench Press',
                'workout_sets' => 3,
                'workout_reps' => 10,
            ])
            ->assertStatus(422);

        $this->actingAs($user)
            ->postJson('/program', [
                'split' => 'full-body',
                'schedule' => '2-day',
            ])
            ->assertOk();

        $this->actingAs($user)
            ->postJson('/program/workouts', [
                'day' => 'Monday',
                'workout_name' => 'Bench Press',
            ])
            ->assertStatus(422);

        $workout = $user->workouts()->create([
            'workout_day' => 'Monday',
            'workout_name' => 'Bench Press',
            'workout_sets' => 3,
            'workout_reps' => 10,
        ]);

        $this->actingAs($user)
            ->patchJson("/program/workouts/{$workout->id}", [
                'day' => 'Monday',
                'workout_name' => 'Incline Bench Press',
                'workout_sets' => 4,
            ])
            ->assertStatus(422);

        $this->actingAs($user)
            ->postJson('/program/workouts', [
                'day' => 'Tuesday',
                'workout_name' => 'Bench Press',
                'workout_sets' => 3,
                'workout_reps' => 10,
            ])
            ->assertStatus(422);

        $otherUser = $this->createUser(['email' => 'otherprogramowner@gmail.com']);
        $otherWorkout = $otherUser->workouts()->create([
            'workout_day' => 'Monday',
            'workout_name' => 'Private Row',
            'workout_sets' => 3,
            'workout_reps' => 10,
        ]);

        $this->actingAs($user)
            ->deleteJson("/program/workouts/{$otherWorkout->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('workouts', [
            'id' => $otherWorkout->id,
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user)
            ->deleteJson('/program/workouts/999999')
            ->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'email' => 'defaultuser@gmail.com',
            'password' => 'Password!123',
            'first_name' => 'Default',
            'last_name' => 'User',
            'birthdate' => '1990-01-01',
            'height' => 1.7,
            'weight' => 65,
            'activity' => 'moderate',
            'sex' => null,
        ], $overrides));
    }

    private function configureSocialProviders(): void
    {
        Config::set('services.google.client_id', 'test-google-client-id');
        Config::set('services.google.client_secret', 'test-google-client-secret');
        Config::set('services.google.redirect', 'http://localhost/auth/google/callback');
        Config::set('services.microsoft.client_id', 'test-microsoft-client-id');
        Config::set('services.microsoft.client_secret', 'test-microsoft-client-secret');
        Config::set('services.microsoft.redirect', 'http://localhost/auth/microsoft/callback');
    }

    /**
     * @param  array<string, mixed>  $raw
     */
    private function socialiteUser(string $id, ?string $email, string $name, ?string $avatar = null, array $raw = []): SocialiteUser
    {
        return (new SocialiteUser)
            ->setRaw(array_merge([
                'id' => $id,
                'sub' => $id,
                'name' => $name,
                'email' => $email,
                'picture' => $avatar,
            ], $raw))
            ->map([
                'id' => $id,
                'nickname' => null,
                'name' => $name,
                'email' => $email,
                'avatar' => $avatar,
            ]);
    }
}
