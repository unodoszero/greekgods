<?php

if (getenv('VERCEL')) {
    $cachePath = '/tmp/greekgods-bootstrap-cache';

    if (! is_dir($cachePath)) {
        mkdir($cachePath, 0755, true);
    }

    foreach ([
        'APP_PACKAGES_CACHE' => $cachePath.'/packages.php',
        'APP_SERVICES_CACHE' => $cachePath.'/services.php',
    ] as $key => $value) {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

require __DIR__.'/../public/index.php';
