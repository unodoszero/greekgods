<?php

namespace App\Http\Controllers;

use App\Content\ArticleCatalog;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home');
    }

    public function legacyPage(string $page): View
    {
        abort_unless(in_array($page, ['about', 'blog', 'calculator', 'laws'], true), 404);

        if ($page === 'blog') {
            return view('pages.blog', ['articleGroups' => ArticleCatalog::grouped()]);
        }

        return view("pages.{$page}");
    }

    public function article(string $slug): View
    {
        $article = ArticleCatalog::find($slug);
        abort_if($article === null, 404);

        return view('articles.show', ['article' => $article]);
    }
}
