@extends('layouts.app', ['title' => 'GreekGods | About'])

@push('styles')
    @vite(['resources/css/site.css', 'resources/css/pages/about.css'])
@endpush

@section('content')
<x-site-nav />
<header>
    <h1>About GreekGods</h1>
    <img src="/graphics/images/about-image.png" alt="Athletes training">
</header>
<main>
    <section>
        <h2>Our Mission</h2>
        <p>At GreekGods, our mission is to empower individuals to achieve their ultimate fitness goals through personalized tools, expert advice, and a supportive community. We believe in the power of combining ancient wisdom with modern science to create a holistic approach to fitness. Whether you're just starting out or are a seasoned athlete, we are here to guide you every step of the way with tailored workout plans, nutrition articles, and advanced calculators to track your progress. Join us and transform your fitness journey with the strength of the ancients and the knowledge of today.</p>
    </section>
    <section>
        <h2>Our Story</h2>
        <p>GreekGods was born from a passion for the legendary physiques of ancient warriors and gods. We sought to blend the timeless aesthetics of the ancients with the latest advancements in fitness science. Our journey began with a simple idea: to create a platform that offers comprehensive fitness solutions for everyone. Today, GreekGods stands as a testament to the power of dedication, innovation, and the pursuit of excellence.</p>
    </section>
    <section>
        <h2>What We Offer</h2>
        <ul>
            <li>Comprehensive calculators for BMI, BMR, and TDEE to help you understand your body's needs.</li>
            <li>Science-backed fitness and nutrition articles to keep you informed and motivated.</li>
            <li>Tailored workout plans designed for all fitness levels, from beginners to advanced athletes.</li>
        </ul>
    </section>
    <section>
        <h2>Why Choose Us?</h2>
        <p>Unlike other platforms, GreekGods focuses on a holistic approach to fitness. We believe that true fitness is achieved through a balance of nutrition, exercise, and mindset. Our platform offers a unique blend of ancient wisdom and modern science to help you achieve lasting results. With GreekGods, you're not just working out; you're embarking on a transformative journey towards a healthier, stronger, and more confident you.</p>
    </section>
    <section>
        <h2>Testimonials</h2>
        <p>"GreekGods has completely transformed my fitness journey. The personalized workout plans and expert advice have helped me achieve goals I never thought possible. I feel stronger, healthier, and more confident than ever before!" — Alex M.</p>
        <p>"The community at GreekGods is incredibly supportive and motivating. I've learned so much about fitness and nutrition, and I've made great progress thanks to their comprehensive tools and resources." — Jamie L.</p>
    </section>
    <section>
        <h2>Join Us Today</h2>
        <p>Discover your true fitness potential with GreekGods. Explore our tools, read our guides, and start transforming your life today. Whether you're looking to build muscle, lose weight, or simply improve your overall health, we have everything you need to succeed.</p>
    </section>
</main>
<x-site-footer />
@endsection

@push('scripts')
    @vite('resources/js/site.js')
@endpush
