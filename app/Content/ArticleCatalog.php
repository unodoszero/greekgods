<?php

namespace App\Content;

final class ArticleCatalog
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        /** @var array<string, array<string, mixed>> $articles */
        $articles = require resource_path('content/articles.php');

        return $articles;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function find(string $slug): ?array
    {
        return self::all()[$slug] ?? null;
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public static function grouped(): array
    {
        $groups = [];

        foreach (self::all() as $article) {
            $groups[$article['category']][] = $article;
        }

        return $groups;
    }
}
