@extends('layouts.auth')

@section('title', 'Crear Cuenta')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/css/intlTelInput.css">
@endpush

@section('content')
<div class="auth-brand">
    <h1>JCM<span>Realty Group</span></h1>
    <p>Create your account</p>
</div>

<form method="POST" action="{{ route('register') }}" id="registerForm">
    @csrf

    <div class="form-group">
        <label class="form-label" for="name">Full Name</label>
        <input
            type="text"
            id="name"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name') }}"
            placeholder="Your full name"
            required
            autofocus
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email') }}"
            placeholder="email@example.com"
            required
        >
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="phoneInput">Phone Number</label>
        <div class="phone-field">
            <input
                type="tel"
                id="phoneInput"
                class="form-control @error('phone') is-invalid @enderror"
                placeholder="(000) 000-0000"
                autocomplete="tel"
                required
            >
        </div>
        <input type="hidden" name="phone" id="phoneHidden" value="{{ old('phone') }}">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            class="form-control @error('password') is-invalid @enderror"
            placeholder="8 characters minimum"
            required
        >
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label" for="password_confirmation">Confirm Password</label>
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            class="form-control"
            placeholder="Re-type password"
            required
        >
    </div>

    <button type="submit" class="btn-auth">
        <i class="bi bi-person-plus"></i> Create Account
    </button>

    <p class="auth-footer">
        Already have an account? <a href="{{ route('login') }}">Log in</a>
    </p>
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/intlTelInputWithUtils.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('phoneInput');
    var hidden = document.getElementById('phoneHidden');
    var form = document.getElementById('registerForm');

    if (!input || !hidden || !form || typeof window.intlTelInput !== 'function') {
        return;
    }

    var iti = window.intlTelInput(input, {
        initialCountry: 'us',
        preferredCountries: ['us', 'ca', 'mx', 'pr', 'do', 'co', 'gb', 'es'],
        nationalMode: true,
        separateDialCode: true,
        formatOnDisplay: true,
        autoPlaceholder: 'polite',
        customPlaceholder: function (placeholder, countryData) {
            if (countryData.iso2 === 'us' || countryData.iso2 === 'ca') {
                return '(000) 000-0000';
            }
            return placeholder || '';
        }
    });

    var old = hidden.value;
    if (old && /^\+\d{7,15}$/.test(old)) {
        try {
            iti.setNumber(old);
        } catch (e) {}
    }

    function syncHidden() {
        var full = iti.getNumber() || '';
        hidden.value = full;
    }

    input.addEventListener('input', syncHidden);
    input.addEventListener('blur', syncHidden);
    input.addEventListener('change', syncHidden);
    input.addEventListener('countrychange', syncHidden);

    form.addEventListener('submit', function (e) {
        syncHidden();
        if (!hidden.value || !/^\+\d{7,15}$/.test(hidden.value)) {
            e.preventDefault();
            if (window.showAlert) {
                window.showAlert('error', 'Phone required', 'Please enter a valid phone number.');
            }
            input.focus();
        }
    });
});
</script>
@endpush