@extends('layouts.app', ['title' => 'GreekGods | Login'])

@push('styles')
    <link rel="stylesheet" href="/files/login.css">
@endpush

@section('content')
    <div class="container">
        <form id="login-form" action="/login" method="POST">
            @csrf
            <img src="/graphics/logo/logo.png" onclick="location.href='/'" alt="Logo" title="Click here to redirect to home">
            <p>Login</p>
            <p id="description">Ready to power up your fitness journey? We're excited to see you back-let's keep reaching those goals together!</p>

            @include('auth.partials.social-buttons')

            <div class="auth-divider"><span>or login with email</span></div>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="e.g. ada.lovelace@icloud.com" value="{{ old('email') }}" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <div class="error-message-container">
                @if (session('status'))
                    <p class="status-message">{{ session('status') }}</p>
                @endif
                @foreach ($errors->all() as $error)
                    <p class="error-message">{{ $error }}</p>
                @endforeach
            </div>

            <button type="submit">Login</button>
            <hr>
            <p>New to GreekGods? Create an account to start your fitness journey with us! <a href="/register">Register</a></p>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="/files/login.js"></script>
@endpush
