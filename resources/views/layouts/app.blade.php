<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="/graphics/logo/logo.png">
    @livewireStyles
    @stack('styles')
    <title>{{ $title ?? 'GreekGods' }}</title>
</head>
<body>
    @yield('content')
    <x-toaster-hub />
    @vite('resources/js/app.js')
    @livewireScripts
    @stack('scripts')
</body>
</html>
