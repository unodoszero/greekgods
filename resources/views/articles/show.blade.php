@extends('layouts.app', ['title' => 'GreekGods | '.$article['title']])

@push('styles')
    @vite(['resources/css/site.css', 'resources/css/pages/article.css'])
@endpush

@section('content')
<x-site-nav />
<header>
    <div class="header-container">
        <h2>{{ $article['title'] }}</h2>
    </div>
</header>
<main>
    <article class="main-container">
        <div class="main-info">
            @foreach ($article['blocks'] as $block)
                @switch($block['type'])
                    @case('heading')
                        <h3>{!! $block['html'] !!}</h3>
                        @break
                    @case('list')
                        <ul>
                            @foreach ($block['items'] as $item)
                                <li>{!! $item !!}</li>
                            @endforeach
                        </ul>
                        @break
                    @case('references')
                        <ol>
                            @foreach ($block['items'] as $item)
                                <li>{!! $item !!}</li>
                            @endforeach
                        </ol>
                        @break
                    @default
                        <p>{!! $block['html'] !!}</p>
                @endswitch
            @endforeach
        </div>
    </article>
</main>
<x-site-footer />
@endsection

@push('scripts')
    @vite('resources/js/site.js')
@endpush
