@extends('layouts.app', ['title' => 'GreekGods | Home'])

@push('styles')
    @vite('resources/css/site.css')
@endpush

@section('content')
<div class="container">
    <x-site-nav />
    <header>
        <div class="header-container">
            <img src="/graphics/images/welcome-image.png" alt="People exercising with a barbell">
            <div class="header-add">
                <span class="header-descriptions">
                    <p>Build confidence, be stronger, and transform.</p>
                    <p>START YOUR JOURNEY NOW.</p>
                </span>
                <button type="button" onclick="window.location.href='/register'">REGISTER</button>
            </div>
        </div>
    </header>
    <main class="index-main">
        <div class="index-section-container">
            <div class="section-workouts" id="picture"><img src="/graphics/images/home.jpg" alt="Progress tracking"></div>
            <div class="section-workouts" id="description">
                <h3>Calculate your body statistics</h3>
                <p>Stay on top of your fitness with real-time updates on your BMI, BMR, and workout achievements.</p>
                <button type="button" onclick="window.location.href='/calculator'">Calculate Now</button>
            </div>
        </div>
        <div class="index-section-container" id="reverse">
            <div class="section-workouts" id="description">
                <h3>Customized Workouts for Every Goal</h3>
                <p>Whether you're looking to lose weight, gain muscle, or maintain health, GreekGods has you covered.</p>
                <button type="button" onclick="window.location.href='/program'">Customize</button>
            </div>
            <div class="section-workouts" id="picture"><img src="/graphics/images/home1.jpg" alt="Customized workout plan"></div>
        </div>
        <div class="index-section-container">
            <div class="section-workouts" id="picture"><img src="/graphics/images/home2.jpg" alt="Fitness tips"></div>
            <div class="section-workouts" id="description">
                <h3>Daily Fitness Tips</h3>
                <p>Get actionable tips and tricks to keep you motivated and on track.</p>
                <button type="button" onclick="window.location.href='/blog'">Learn Now</button>
            </div>
        </div>
        <div class="index-section-container" id="reverse">
            <div class="section-workouts" id="description">
                <h3>Completely Free</h3>
                <p>All our tools are available to you at no cost, ever!</p>
                <button type="button" onclick="window.location.href='/about'">Know more about GreekGods</button>
            </div>
            <div class="section-workouts" id="picture"><img src="/graphics/images/home3.jpg" alt="Learn more"></div>
        </div>
    </main>
    <x-site-footer />
</div>
@endsection

@push('scripts')
    @vite('resources/js/site.js')
@endpush
