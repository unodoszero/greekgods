<?php

namespace App\Http\Controllers;

use App\Support\AuthSessionIdentity;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class PageController extends Controller
{
    private const ARTICLES = [
        'advance-splits' => 'articles/advance-splits.php',
        'bmi' => 'articles/bmi.php',
        'bmr' => 'articles/bmr.php',
        'bmr-vs-tdee' => 'articles/bmr-vs-tdee.php',
        'calorie-deficit' => 'articles/calorie-deficit.php',
        'isolation-vs-composite' => 'articles/isolation-vs-composite.php',
        'muscles' => 'articles/muscles.php',
        'ppl' => 'articles/ppl.php',
        'progressive-overload' => 'articles/progressive-overload.php',
        'protein' => 'articles/protein.php',
        'supplements' => 'articles/supplements.php',
        'workout-splits' => 'articles/workout-splits.php',
    ];

    public function home(): Response
    {
        return $this->legacyHtml('index.php');
    }

    public function legacyPage(string $page): Response
    {
        $pages = [
            'about' => 'files/about.php',
            'blog' => 'files/blog.php',
            'calculator' => 'files/calculator.php',
            'laws' => 'files/laws.html',
        ];

        abort_unless(isset($pages[$page]), 404);

        return $this->legacyHtml($pages[$page]);
    }

    public function article(string $slug): Response
    {
        abort_unless(isset(self::ARTICLES[$slug]), 404);

        return $this->legacyHtml(self::ARTICLES[$slug]);
    }

    private function legacyHtml(string $relativePath): Response
    {
        $source = file_get_contents(base_path($relativePath));
        abort_if($source === false, 404);

        $htmlStart = strpos($source, '<!DOCTYPE html>');
        $html = $htmlStart === false ? $source : substr($source, $htmlStart);
        $html = $this->injectAuthState($html);
        $html = $this->rewriteLegacyLinks($html);
        $html = $this->injectRuntimeAssets($html);

        return response($html);
    }

    private function injectRuntimeAssets(string $html): string
    {
        if (str_contains($html, 'id="toaster"')) {
            return $html;
        }

        $headAssets = Blade::render('@livewireStyles');
        $bodyAssets = Blade::render(implode(PHP_EOL, [
            '<x-toaster-hub />',
            "@vite('resources/js/app.js')",
            '@livewireScripts',
        ]));

        $html = preg_replace('/<\/head>/i', $headAssets.PHP_EOL.'</head>', $html, 1) ?? $html;

        return preg_replace('/<\/body>/i', $bodyAssets.PHP_EOL.'</body>', $html, 1) ?? $html;
    }

    private function injectAuthState(string $html): string
    {
        $userId = session(AuthSessionIdentity::USER_ID);
        $fullName = trim((string) session(AuthSessionIdentity::FULL_NAME, ''));

        if ($userId === null && Auth::check()) {
            $user = Auth::user();

            if ($user !== null) {
                AuthSessionIdentity::store(request(), $user);
                $userId = $user->id;
                $fullName = trim((string) session(AuthSessionIdentity::FULL_NAME, ''));
            }
        }

        $escapedFullName = e($fullName);

        $html = preg_replace(
            '/const userId = <\?php echo json_encode\(\$userId\); \?>;/',
            'const userId = '.json_encode($userId).';',
            $html
        ) ?? $html;

        $html = preg_replace(
            '/const userId = <\?= json_encode\(\$userId\) \?>;/',
            'const userId = '.json_encode($userId).';',
            $html
        ) ?? $html;

        $html = preg_replace(
            '/const userId = ([^;]*);/',
            'const userId = '.json_encode($userId).';',
            $html,
            1
        ) ?? $html;

        $html = preg_replace(
            '/(<script type="text\/javascript">\s*const userId = [^;]*;)/',
            '$1'.PHP_EOL.'        const userFullName = '.json_encode($fullName).';',
            $html,
            1
        ) ?? $html;

        $html = preg_replace(
            '/(<script>\s*const userId = [^;]*;)/',
            '$1'.PHP_EOL.'        const userFullName = '.json_encode($fullName).';',
            $html,
            1
        ) ?? $html;

        $html = preg_replace(
            '/<span id="profile-name">.*?<\/span>/s',
            '<span id="profile-name">'.$escapedFullName.'</span>',
            $html
        ) ?? $html;

        if ($userId !== null) {
            $html = preg_replace(
                '/<button id="register-button"([^>]*)>GET STARTED<\/button>/s',
                '<button id="register-button"$1 hidden style="display: none;">GET STARTED</button>',
                $html,
                1
            ) ?? $html;

            $html = preg_replace(
                '/<button id="profile-button"([^>]*)>/s',
                '<button id="profile-button"$1 style="display: inline-flex;" aria-label="Open profile">',
                $html,
                1
            ) ?? $html;

            return preg_replace(
                '/<span id="profile-name"([^>]*)>/s',
                '<span id="profile-name"$1 style="display: inline;">',
                $html,
                1
            ) ?? $html;
        }

        $html = preg_replace(
            '/<button id="profile-button"([^>]*)>/s',
            '<button id="profile-button"$1 hidden style="display: none;">',
            $html,
            1
        ) ?? $html;

        return preg_replace(
            '/<span id="profile-name"([^>]*)>/s',
            '<span id="profile-name"$1 hidden style="display: none;">',
            $html,
            1
        ) ?? $html;
    }

    private function rewriteLegacyLinks(string $html): string
    {
        $replacements = [
            '../files/register.html' => '/register',
            './files/register.html' => '/register',
            '../files/login.php' => '/login',
            './files/login.php' => '/login',
            '../files/profile.php' => '/profile',
            './files/profile.php' => '/profile',
            '../files/program.php' => '/program',
            './files/program.php' => '/program',
            '../files/blog.php' => '/blog',
            './files/blog.php' => '/blog',
            '../files/calculator.php' => '/calculator',
            './files/calculator.php' => '/calculator',
            '../files/about.php' => '/about',
            './files/about.php' => '/about',
            '../files/laws.html' => '/laws',
            './files/laws.html' => '/laws',
            'laws.html' => '/laws',
            'index.html' => '/',
            '../index.php' => '/',
            './index.php' => '/',
            'index.php' => '/',
            './login.php' => '/login',
            'login.php' => '/login',
            './register.html' => '/register',
            'register.html' => '/register',
            './profile.php' => '/profile',
            'profile.php' => '/profile',
            './program.php' => '/program',
            'program.php' => '/program',
            './blog.php' => '/blog',
            'blog.php' => '/blog',
            './calculator.php' => '/calculator',
            'calculator.php' => '/calculator',
            './about.php' => '/about',
            'about.php' => '/about',
            '../includes/logout.php' => '/logout',
            './about.css' => '/files/about.css',
            './about.js' => '/files/about.js',
            './blog.css' => '/files/blog.css',
            './blog.js' => '/files/blog.js',
            './calculator.css' => '/files/calculator.css',
            './calculator.js' => '/files/calculator.js',
            './laws.css' => '/files/laws.css',
            './laws.js' => '/files/laws.js',
        ];

        $html = strtr($html, $replacements);

        $html = preg_replace_callback('/(["\'])\.\.\/articles\/([a-z0-9-]+)\.php\\1/i', function (array $matches): string {
            return $matches[1].'/articles/'.Str::lower($matches[2]).$matches[1];
        }, $html) ?? $html;

        return $html;
    }
}
