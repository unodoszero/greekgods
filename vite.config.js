import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/palette.css',
                'resources/css/site.css',
                'resources/css/pages/about.css',
                'resources/css/pages/article.css',
                'resources/css/pages/blog.css',
                'resources/css/pages/calculator.css',
                'resources/css/pages/laws.css',
                'resources/css/pages/login.css',
                'resources/css/pages/profile.css',
                'resources/css/pages/program.css',
                'resources/css/pages/register.css',
                'resources/js/app.js',
                'resources/js/site.js',
                'resources/js/pages/calculator/index.js',
                'resources/js/pages/laws.js',
                'resources/js/pages/login.js',
                'resources/js/pages/profile.js',
                'resources/js/pages/program.js',
                'resources/js/pages/register.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
