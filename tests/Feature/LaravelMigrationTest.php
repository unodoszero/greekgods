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
            ->assertSee('id="calculator-form"', false)
            ->assertSee('id="calculator-results" hidden', false)
            ->assertSee('For adults aged 18–100.', false);
        $this->get('/laws')
            ->assertOk()
            ->assertSee('id="toaster"', false);
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
        $this->assertStringContainsString("@vite('resources/css/palette.css')", $layout);
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

    public function test_active_app_uses_the_shared_palette_without_page_specific_colors(): void
    {
        $palette = file_get_contents(resource_path('css/palette.css'));

        $this->assertIsString($palette);
        $this->assertStringContainsString('--gg-color-primary:', $palette);
        $this->assertStringContainsString('--gg-color-action-soft:', $palette);
        $this->assertStringContainsString('--gg-color-overlay:', $palette);

        foreach (['/', '/about', '/blog', '/calculator', '/laws', '/articles/bmi', '/login', '/register'] as $path) {
            $this->get($path)->assertOk();
        }

        $user = $this->createUser(['email' => 'palette@gmail.com']);
        $this->actingAs($user)->get('/profile')->assertOk();
        $this->actingAs($user)->get('/program')->assertOk();

        $activeStylePaths = [
            resource_path('css/site.css'),
            ...glob(resource_path('css/pages/*.css')) ?: [],
            resource_path('js/app.js'),
            resource_path('views/vendor/toaster/hub.blade.php'),
        ];

        foreach ($activeStylePaths as $stylePath) {
            $contents = file_get_contents($stylePath);

            $this->assertIsString($contents);
            $this->assertDoesNotMatchRegularExpression(
                '/#[0-9a-f]{3,8}\b|(?:rgb|hsl)a?\s*\(/i',
                $contents,
                basename($stylePath).' must consume shared palette tokens instead of hard-coded colors.'
            );
        }
    }

    public function test_active_public_scripts_do_not_use_native_browser_dialogs(): void
    {
        $scriptPaths = [
            resource_path('js/site.js'),
            ...glob(resource_path('js/pages/*.js')) ?: [],
            ...glob(resource_path('js/pages/calculator/*.js')) ?: [],
        ];

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

    public function test_program_script_uses_draft_and_pending_states(): void
    {
        $script = file_get_contents(resource_path('js/pages/program.js'));
        $styles = file_get_contents(resource_path('css/pages/program.css'));

        $this->assertIsString($script);
        $this->assertIsString($styles);
        $this->assertStringContainsString('draftWorkouts', $script);
        $this->assertStringContainsString('savedWorkouts', $script);
        $this->assertStringContainsString('allowDraftReset', $script);
        $this->assertStringContainsString('setPending(true)', $script);
        $this->assertStringContainsString('workouts: draftWorkouts.map', $script);
        $this->assertStringContainsString('document.querySelectorAll("[data-builder-chrome]")', $script);
        $this->assertStringContainsString('renderGuide()', $script);
        $this->assertStringContainsString('bindCarousels()', $script);
        $this->assertStringContainsString('visibleCarouselRange', $script);
        $this->assertStringContainsString('document.createElementNS("http://www.w3.org/2000/svg", "svg")', $script);
        $this->assertStringContainsString('grid-template-columns: repeat(4, minmax(0, 1fr))', $styles);
        $this->assertStringContainsString('var(--gg-color-primary)', $styles);
        $this->assertStringContainsString('var(--gg-color-action)', $styles);
        $this->assertStringNotContainsString('--program-blue', $styles);
        $this->assertStringContainsString('.weekly-board {', $styles);
        $this->assertStringContainsString('padding: 2px 2px 14px', $styles);
        $this->assertStringContainsString('scrollbar-color: var(--gg-color-primary)', $styles);
        $this->assertStringContainsString('border-style: dashed', $styles);
        $this->assertStringContainsString('letter-spacing: .1em', $styles);
        $this->assertStringContainsString('overflow-x: auto', $styles);
        $this->assertStringContainsString('scroll-snap-type: inline mandatory', $styles);
        $this->assertStringContainsString('flex-basis: clamp(165px, 16vw, 190px)', $styles);
        $this->assertStringContainsString('.day-card.is-rest {'.PHP_EOL.'    min-height: 250px', $styles);
        $this->assertStringContainsString('writing-mode: vertical-rl', $styles);
    }

    public function test_calculator_uses_guarded_modular_calculations(): void
    {
        $sourceScript = file_get_contents(resource_path('js/pages/calculator/index.js'));
        $sourceCore = file_get_contents(resource_path('js/pages/calculator/core.js'));
        $sourceStyles = file_get_contents(resource_path('css/pages/calculator.css'));

        $this->assertIsString($sourceScript);
        $this->assertIsString($sourceCore);
        $this->assertIsString($sourceStyles);

        $this->assertStringContainsString('calculateCalorieTargets', $sourceCore);
        $this->assertStringContainsString('calories < minimumCalories', $sourceCore);
        $this->assertStringContainsString('if (bmi < 25)', $sourceCore);
        $this->assertStringContainsString('results.hidden = false', $sourceScript);
        $this->assertStringContainsString('resultsHeading.focus', $sourceScript);
        $this->assertStringContainsString('var(--gg-color-primary)', $sourceStyles);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr))', $sourceStyles);
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
            ->assertSee('<a id="register-button" href="/register">GET STARTED</a>', false)
            ->assertDontSee('id="profile-button"', false)
            ->assertDontSee('id="profile-name"', false);
    }

    public function test_authenticated_nav_shows_clickable_user_name_on_public_pages(): void
    {
        $user = $this->createUser([
            'email' => 'navuser@gmail.com',
            'first_name' => 'Nav',
            'last_name' => 'User',
        ]);

        foreach (['/', '/about', '/blog', '/calculator'] as $path) {
            $this->actingAs($user)
                ->get($path)
                ->assertOk()
                ->assertSee('<a id="profile-button" href="/profile" aria-label="Open profile">', false)
                ->assertSee('<a id="profile-name" href="/profile">Nav User</a>', false)
                ->assertDontSee('GET STARTED');
        }

        $this->actingAs($user)
            ->get('/laws')
            ->assertOk()
            ->assertDontSee('GET STARTED');

        $this->actingAs($user)
            ->get('/program')
            ->assertOk()
            ->assertSee('const userFullName = "Nav User";', false)
            ->assertSee('<a id="profile-button" href="/profile" aria-label="Open profile">', false)
            ->assertSee('<a id="profile-name" href="/profile">Nav User</a>', false);

        $navScript = file_get_contents(resource_path('js/site.js'));
        $navStyles = file_get_contents(resource_path('css/site.css'));

        $this->assertIsString($navScript);
        $this->assertIsString($navStyles);
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
        $user = $this->createUser(['email' => 'grace@example.com']);
        $payload = [
            'split' => 'anterior-posterior',
            'schedule' => '4-day',
            'workouts' => [
                [
                    'day' => 'Monday',
                    'name' => 'Bench Press',
                    'focus' => 'Chest',
                    'sets' => 3,
                    'reps_min' => 8,
                    'reps_max' => 12,
                    'position' => 0,
                ],
                [
                    'day' => 'Tuesday',
                    'name' => 'Romanian Deadlift',
                    'focus' => 'Hamstrings',
                    'sets' => 3,
                    'reps_min' => 6,
                    'reps_max' => 10,
                    'position' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($user)
            ->postJson('/program', $payload)
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('message', 'Program saved.')
            ->assertJsonCount(2, 'workouts')
            ->assertJsonPath('workouts.0.repsMin', 8)
            ->assertJsonPath('workouts.0.repsMax', 12);

        $programId = $response->json('program.id');
        $this->assertDatabaseHas('workouts', [
            'program_id' => $programId,
            'workout_day' => 'Monday',
            'workout_name' => 'Bench Press',
            'workout_focus' => 'Chest',
            'workout_sets' => 3,
            'reps_min' => 8,
            'reps_max' => 12,
        ]);

        $addResponse = $this->actingAs($user)
            ->postJson('/program/workouts', [
                'day' => 'Monday',
                'name' => 'Incline Press',
                'focus' => 'Upper Chest',
                'sets' => 3,
                'reps_min' => 8,
                'reps_max' => 12,
            ])
            ->assertOk()
            ->assertJsonPath('workout.workoutName', 'Incline Press');

        $workoutId = $addResponse->json('workout.id');

        $this->actingAs($user)
            ->patchJson("/program/workouts/{$workoutId}", [
                'day' => 'Thursday',
                'name' => 'Incline Bench Press',
                'focus' => 'Upper Chest',
                'sets' => 4,
                'reps_min' => 8,
                'reps_max' => 10,
            ])
            ->assertOk()
            ->assertJsonPath('workout.workoutDay', 'Thursday');

        $this->actingAs($user)
            ->deleteJson("/program/workouts/{$workoutId}")
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('message', 'Workout removed.');

        $this->assertDatabaseMissing('workouts', ['id' => $workoutId]);

        $this->actingAs($user)
            ->delete('/program')
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('message', 'Program removed.');
        $this->assertDatabaseMissing('programs', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('workouts', ['program_id' => $programId]);
    }

    public function test_anterior_posterior_catalog_has_balanced_four_and_six_day_templates(): void
    {
        $split = WorkoutSplitCatalog::all()['anterior-posterior'];
        $templates = config('preconfigured_workouts.anterior-posterior');

        $this->assertSame('Anterior A', $split['schedules']['4-day']['days']['Monday']);
        $this->assertSame('Posterior B', $split['schedules']['4-day']['days']['Friday']);
        $this->assertTrue($split['schedules']['6-day']['advanced']);
        $this->assertSame('Anterior A', $split['schedules']['6-day']['days']['Friday']);
        $this->assertSame('Posterior A', $split['schedules']['6-day']['days']['Saturday']);

        foreach (['Anterior A', 'Anterior B', 'Posterior A', 'Posterior B'] as $label) {
            $this->assertNotEmpty($templates[$label]);
            foreach ($templates[$label] as $workout) {
                $this->assertGreaterThan(0, $workout['sets']);
                $this->assertGreaterThan(0, $workout['reps_min']);
                $this->assertGreaterThanOrEqual($workout['reps_min'], $workout['reps_max']);
            }
        }
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
        $validWorkout = [
            'day' => 'Monday',
            'name' => 'Bench Press',
            'focus' => 'Chest',
            'sets' => 3,
            'reps_min' => 8,
            'reps_max' => 12,
            'position' => 0,
        ];

        $this->actingAs($user)
            ->postJson('/program', [
                'split' => 'not-a-split',
                'schedule' => '3-day',
                'workouts' => [$validWorkout],
            ])
            ->assertStatus(422);

        $this->actingAs($user)
            ->postJson('/program/workouts', [
                'day' => 'Monday',
                'name' => 'Bench Press',
                'sets' => 3,
                'reps_min' => 8,
                'reps_max' => 12,
            ])
            ->assertStatus(422);

        $saved = $this->actingAs($user)
            ->postJson('/program', [
                'split' => 'full-body',
                'schedule' => '2-day',
                'workouts' => [$validWorkout],
            ])
            ->assertOk();

        $this->actingAs($user)
            ->postJson('/program/workouts', [
                'day' => 'Monday',
                'name' => 'Bench Press',
            ])
            ->assertStatus(422);

        $workoutId = $saved->json('workouts.0.id');

        $this->actingAs($user)
            ->patchJson("/program/workouts/{$workoutId}", [
                'day' => 'Monday',
                'name' => 'Incline Bench Press',
                'sets' => 4,
            ])
            ->assertStatus(422);

        $this->actingAs($user)
            ->postJson('/program/workouts', [
                'day' => 'Tuesday',
                'name' => 'Bench Press',
                'sets' => 3,
                'reps_min' => 8,
                'reps_max' => 12,
            ])
            ->assertStatus(422);

        $otherUser = $this->createUser(['email' => 'otherprogramowner@gmail.com']);
        $otherResponse = $this->actingAs($otherUser)->postJson('/program', [
            'split' => 'full-body',
            'schedule' => '2-day',
            'workouts' => [[...$validWorkout, 'name' => 'Private Row']],
        ])->assertOk();
        $otherWorkoutId = $otherResponse->json('workouts.0.id');

        $this->actingAs($user)
            ->deleteJson("/program/workouts/{$otherWorkoutId}")
            ->assertNotFound();

        $this->assertDatabaseHas('workouts', [
            'id' => $otherWorkoutId,
            'program_id' => $otherResponse->json('program.id'),
        ]);

        $this->actingAs($user)
            ->deleteJson('/program/workouts/999999')
            ->assertNotFound();

        $this->actingAs($user)
            ->postJson('/program', [
                'split' => 'full-body',
                'schedule' => '2-day',
                'workouts' => [[...$validWorkout, 'reps_min' => 15, 'reps_max' => 8]],
            ])
            ->assertStatus(422);

        $this->assertDatabaseHas('programs', [
            'user_id' => $user->id,
            'program' => 'full-body',
            'schedule' => '2-day',
        ]);
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
