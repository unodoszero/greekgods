@extends('layouts.app', ['title' => 'GreekGods | Complete Profile'])

@push('styles')
    <link rel="stylesheet" href="/files/register.css">
@endpush

@section('content')
    <form class="container social-complete-form" action="/auth/social/complete" method="POST">
        @csrf
        <div id="registerAccount" class="social-complete-panel">
            <img src="/graphics/logo/logo.png" alt="GreekGods">
            <p id="register">Complete profile</p>
            <p id="description">
                Signed in as {{ $pendingUser['email'] ?? 'your social account' }}. Add your body metrics to finish creating your account.
            </p>

            @if (session('status'))
                <div class="status-message">{{ session('status') }}</div>
            @endif

            <div class="error-message-container">
                @foreach ($errors->all() as $error)
                    <div class="error-message">{{ $error }}</div>
                @endforeach
            </div>

            <div class="section">
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
                    <option value="" disabled @selected(old('activity') === null)>Select activity</option>
                    <option value="sedentary" @selected(old('activity') === 'sedentary')>Sedentary: little or no exercise</option>
                    <option value="light" @selected(old('activity') === 'light')>Light: exercise 1-3 times/week</option>
                    <option value="moderate" @selected(old('activity') === 'moderate')>Moderate: exercise 3-5 times/week</option>
                    <option value="active" @selected(old('activity') === 'active')>Active: intense exercise 6-7 times/week</option>
                    <option value="very_active" @selected(old('activity') === 'very_active')>Very Active: very intense exercise daily, or physical job</option>
                </select>
            </div>

            <div class="terms">
                <input type="checkbox" id="check" name="check" value="1" required>
                <label for="check">
                    I agree to the GreekGods <a href="/laws" target="_blank">Terms of Service</a>
                    and acknowledge the <a href="/laws" target="_blank">Privacy Policy</a>.
                </label>
            </div>

            <button type="submit">Complete Signup</button>
            <hr>
            <p>Need a different method? <a href="/login">Back to login</a></p>
        </div>
    </form>
@endsection
