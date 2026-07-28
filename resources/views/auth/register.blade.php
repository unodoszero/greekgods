@extends('layouts.app', ['title' => 'GreekGods | Register'])

@push('styles')
    @vite('resources/css/pages/register.css')
@endpush

@section('content')
    <form class="container" action="/register" method="POST" id="registrationForm">
        @csrf
        <div id="registerAccount">
            <img src="/graphics/logo/logo.png" alt="Register">
            <p id="register">Register</p>
            <p id="description">Start strong with GreekGods, every journey begins here!</p>

            @include('auth.partials.social-buttons')

            <div class="auth-divider"><span>or create an account with email</span></div>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="e.g. ada.lovelace@icloud.com" value="{{ old('email') }}" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm-password">Confirm Password</label>
            <input type="password" id="confirm-password" name="password_confirmation" required>

            <div class="terms">
                <input type="checkbox" id="check" name="check" value="1" required>
                <label for="check">
                    I agree to the GreekGods <a href="/laws" target="_blank">Terms of Service</a>
                    and acknowledge the <a href="/laws" target="_blank">Privacy Policy</a>.
                </label>
            </div>

            <div class="error-message-container">
                @foreach ($errors->all() as $error)
                    <div class="error-message">{{ $error }}</div>
                @endforeach
            </div>

            <button type="button" id="registerButton">Agree and Start Now</button>
            <hr>
            <p>Already have an account? <a href="/login">Login</a></p>
        </div>
        <div id="registerInfo">
            <p id="register">Register</p>
            <p id="description">Start strong with GreekGods, every journey begins here!</p>

            <div class="section">
                <label for="first-name">First Name</label>
                <input type="text" id="first-name" name="first_name" placeholder="e.g. Ada" value="{{ old('first_name') }}" required>

                <label for="last-name">Last Name</label>
                <input type="text" id="last-name" name="last_name" placeholder="e.g. Lovelace" value="{{ old('last_name') }}" required>

                <label for="birthdate">Birthdate</label>
                <input type="date" id="birthdate" name="birthdate" min="1923-01-01" max="2011-01-01" value="{{ old('birthdate') }}" required>

                <label for="sex">Sex</label>
                <select name="sex" id="sex" required>
                    <option value="" disabled @selected(old('sex') === null)>Select sex</option>
                    <option value="male" @selected(old('sex') === 'male')>Male</option>
                    <option value="female" @selected(old('sex') === 'female')>Female</option>
                    <option value="prefer_not_to_say" @selected(old('sex') === 'prefer_not_to_say')>Prefer not to say</option>
                </select>
            </div>

            <label for="height">Height</label>
            <div class="section-metrics">
                <input id="height" type="number" name="height_value" placeholder="e.g. 5.7" step="0.01" value="{{ old('height_value') }}" required>
                <select name="height_unit" id="heightMetric" required>
                    <option value="cm" @selected(old('height_unit', 'cm') === 'cm')>.cm</option>
                    <option value="in" @selected(old('height_unit') === 'in')>.in</option>
                    <option value="m" @selected(old('height_unit') === 'm')>.m</option>
                    <option value="ft" @selected(old('height_unit') === 'ft')>.ft</option>
                </select>
            </div>
            <p class="input-hint">For ft, type 5.7 for 5 ft 7 in.</p>

            <label for="weight">Weight</label>
            <div class="section-metrics">
                <input id="weight" type="number" name="weight_value" placeholder="e.g. 75" step="0.01" value="{{ old('weight_value') }}" required>
                <select name="weight_unit" id="weightMetric" required>
                    <option value="kg" @selected(old('weight_unit', 'kg') === 'kg')>.kg</option>
                    <option value="lb" @selected(old('weight_unit') === 'lb')>.lb</option>
                </select>
            </div>

            <div class="section">
                <label for="activity">Activity</label>
                <select name="activity" id="activity" required>
                    <option value="" disabled selected></option>
                    <option value="sedentary" @selected(old('activity') === 'sedentary')>Sedentary: little or no exercise</option>
                    <option value="light" @selected(old('activity') === 'light')>Light: exercise 1-3 times/week</option>
                    <option value="moderate" @selected(old('activity') === 'moderate')>Moderate: exercise 3-5 times/week</option>
                    <option value="active" @selected(old('activity') === 'active')>Active: intense exercise 6-7 times/week</option>
                    <option value="very_active" @selected(old('activity') === 'very_active')>Very Active: very intense exercise daily, or physical job</option>
                </select>
            </div>
            <hr>
            <button type="submit" id="startJourneyNow">Start Journey Now</button>
        </div>
    </form>
@endsection

@push('scripts')
    @vite('resources/js/pages/register.js')
@endpush
