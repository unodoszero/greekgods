@extends('layouts.app', ['title' => 'GreekGods | Blog'])

@push('styles')
    @vite(['resources/css/site.css', 'resources/css/pages/blog.css'])
@endpush

@section('content')
<x-site-nav />
<header>
    <div class="header-container">
        <h3>EVERY JOURNEY BEGINS HERE</h3>
        <p>Welcome to the GreekGods Blog—your destination for fitness knowledge and inspiration. Start with the fundamentals or explore advanced training topics.</p>
    </div>
</header>
<main>
    <div class="main-container">
        @foreach ($articleGroups as $category => $articles)
            <section class="{{ strtolower($category) === 'beginner' ? 'main-beginner' : 'main-all' }}">
                <h3>{{ $category }}</h3>
                <div class="{{ strtolower($category) }}-articles">
                    @foreach ($articles as $article)
                        <article tabindex="0" role="link" onclick="window.location.href='/articles/{{ $article['slug'] }}'" onkeydown="if(event.key === 'Enter' || event.key === ' '){event.preventDefault(); window.location.href='/articles/{{ $article['slug'] }}';}">
                            <img src="{{ $article['image'] }}" alt="">
                            <h4>{{ $article['title'] }}</h4>
                            <p>{{ $article['summary'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</main>
<x-site-footer />
@endsection

@push('scripts')
    @vite('resources/js/site.js')
@endpush
