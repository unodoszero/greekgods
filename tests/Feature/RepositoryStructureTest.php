<?php

namespace Tests\Feature;

use App\Content\ArticleCatalog;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RepositoryStructureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_article_catalog_and_blog_stay_in_sync(): void
    {
        $articles = ArticleCatalog::all();
        $blog = $this->get('/blog')->assertOk();

        $this->assertCount(12, $articles);

        foreach ($articles as $article) {
            $this->assertNotEmpty($article['title']);
            $this->assertNotEmpty($article['summary']);
            $this->assertNotEmpty($article['category']);
            $this->assertFileExists(public_path(ltrim($article['image'], '/')));

            $this->get('/articles/'.$article['slug'])
                ->assertOk()
                ->assertSee($article['title']);

            $blog->assertSee('/articles/'.$article['slug'], false);

            $this->get('/articles/'.$article['slug'].'.php')
                ->assertRedirect('/articles/'.$article['slug']);
        }

        $this->get('/articles/not-a-real-article')->assertNotFound();
    }

    public function test_historical_page_urls_redirect_without_legacy_files(): void
    {
        foreach ([
            '/index.php' => '/',
            '/files/about.php' => '/about',
            '/files/blog.php' => '/blog',
            '/files/calculator.php' => '/calculator',
            '/files/laws.html' => '/laws',
            '/files/login.php' => '/login',
            '/files/register.html' => '/register',
        ] as $legacy => $canonical) {
            $this->get($legacy)->assertRedirect($canonical);
        }
    }

    public function test_repository_has_one_laravel_frontend_source_tree(): void
    {
        foreach ([
            'index.php',
            'index.css',
            'index.js',
            'articles',
            'files',
            'fonts',
            'graphics',
            'migrations/postgres_schema.sql',
            'scripts/migrate_mysql_to_postgres.php',
            'resources/css/app.css',
            'resources/views/welcome.blade.php',
            'public/files',
            'public/articles',
            'public/index.css',
            'public/index.js',
            'public/favicon.ico',
            '.vscode/tasks.json',
        ] as $obsoletePath) {
            $this->assertFalse(file_exists(base_path($obsoletePath)), "{$obsoletePath} should not exist.");
        }

        $this->assertDirectoryExists(resource_path('css/pages'));
        $this->assertDirectoryExists(resource_path('js/pages'));
        $this->assertDirectoryExists(resource_path('views/pages'));
        $this->assertDirectoryExists(public_path('graphics'));
        $this->assertFileExists(resource_path('css/palette.css'));
    }

    public function test_runtime_directories_define_placeholder_ignore_rules(): void
    {
        foreach ([
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('logs'),
        ] as $directory) {
            $ignore = $directory.'/.gitignore';

            $this->assertFileExists($ignore);
            $this->assertStringContainsString('*', (string) file_get_contents($ignore));
            $this->assertStringContainsString('!.gitignore', (string) file_get_contents($ignore));
        }
    }

    public function test_forwarded_https_requests_generate_secure_asset_urls(): void
    {
        Route::get('/_deployment/asset-url', fn (): string => asset('build/example.css'));

        $this->withServerVariables([
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_HOST' => 'internal-vercel-runtime',
            'HTTP_X_FORWARDED_HOST' => 'greekgods-psi.vercel.app',
            'HTTP_X_FORWARDED_PORT' => '443',
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ])
            ->get('/_deployment/asset-url')
            ->assertOk()
            ->assertSeeText('https://greekgods-psi.vercel.app/build/example.css');
    }
}
