<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/blog', [PageController::class, 'legacyPage'])->defaults('page', 'blog')->name('blog');
Route::get('/calculator', [PageController::class, 'legacyPage'])->defaults('page', 'calculator')->name('calculator');
Route::get('/about', [PageController::class, 'legacyPage'])->defaults('page', 'about')->name('about');
Route::get('/laws', [PageController::class, 'legacyPage'])->defaults('page', 'laws')->name('laws');
Route::get('/articles/{slug}.php', fn (string $slug) => redirect("/articles/{$slug}"));
Route::get('/articles/{slug}', [PageController::class, 'article'])->name('articles.show');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->whereIn('provider', ['google', 'microsoft'])
        ->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->whereIn('provider', ['google', 'microsoft'])
        ->name('social.callback');
    Route::get('/auth/social/complete', [SocialAuthController::class, 'showCompletion'])
        ->middleware('pending.social')
        ->name('social.complete');
    Route::post('/auth/social/complete', [SocialAuthController::class, 'completeRegistration'])
        ->middleware('pending.social');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/data', [ProfileController::class, 'data']);
    Route::patch('/profile/account', [ProfileController::class, 'updateAccount']);
    Route::patch('/profile/body-metrics', [ProfileController::class, 'updateBodyMetrics']);
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword']);
    Route::get('/program', [ProgramController::class, 'show'])->name('program');
    Route::post('/program', [ProgramController::class, 'store']);
    Route::delete('/program', [ProgramController::class, 'destroy']);
    Route::post('/program/delete', [ProgramController::class, 'destroy']);
    Route::post('/program/workouts', [ProgramController::class, 'storeWorkout']);
    Route::patch('/program/workouts/{workout}', [ProgramController::class, 'updateWorkout']);
    Route::delete('/program/workouts/{workoutId}', [ProgramController::class, 'destroyWorkout'])->whereNumber('workoutId');
    Route::redirect('/files/profile.php', '/profile');
    Route::redirect('/files/profile', '/profile');
    Route::redirect('/includes/users/fetch_user_data.php', '/profile/data');
});

Route::redirect('/files/login.php', '/login');
Route::redirect('/files/register.html', '/register');
Route::redirect('/files/program.php', '/program');
Route::redirect('/files/blog.php', '/blog');
Route::redirect('/files/calculator.php', '/calculator');
Route::redirect('/files/about.php', '/about');
Route::redirect('/files/laws.html', '/laws');
Route::redirect('/files/login', '/login');
Route::redirect('/files/register', '/register');
Route::redirect('/files/program', '/program');
Route::redirect('/files/blog', '/blog');
Route::redirect('/files/calculator', '/calculator');
Route::redirect('/files/about', '/about');
Route::redirect('/files/laws', '/laws');
Route::get('/files/{path?}', function (?string $path = null) {
    $page = trim((string) $path, '/');
    $page = preg_replace('/\.(php|html)$/', '', $page);

    return match ($page) {
        'login' => redirect('/login'),
        'register' => redirect('/register'),
        'profile' => redirect('/profile'),
        'program' => redirect('/program'),
        'blog' => redirect('/blog'),
        'calculator' => redirect('/calculator'),
        'about' => redirect('/about'),
        'laws' => redirect('/laws'),
        default => abort(404),
    };
})->where('path', '.*');
Route::redirect('/index.php', '/');
Route::redirect('/includes/logout.php', '/logout');
