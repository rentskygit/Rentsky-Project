@extends('layouts.app')
@section('title', 'My Profile')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/css/intlTelInput.css">
<style>
    .profile-grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 1.5rem;
        align-items: start;
    }

    .profile-summary {
        text-align: center;
        padding: 2rem 1.5rem;
    }

    .profile-avatar {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        background: var(--brand-secondary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        margin: 0 auto 1rem;
        box-shadow: 0 4px 20px rgba(55, 79, 167, 0.25);
    }

    .profile-name {
        font-family: var(--font-display);
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--brand-primary);
        margin-bottom: 0.25rem;
        letter-spacing: -0.02em;
    }

    .profile-email {
        font-size: 0.85rem;
        color: var(--brand-muted);
        margin-bottom: 1.25rem;
        word-break: break-all;
    }

    .profile-roles {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    .profile-role-badge {
        display: inline-block;
        padding: 0.2rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .profile-meta {
        border-top: 1px solid var(--border);
        padding-top: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        text-align: left;
    }

    .profile-meta-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 1rem;
        font-size: 0.85rem;
    }

    .profile-meta-label {
        color: var(--brand-muted);
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
    }

    .profile-meta-value {
        font-weight: 500;
        color: var(--brand-primary);
        text-align: right;
    }

    .phone-field .iti {
        width: 100%;
    }

    .phone-field .iti__tel-input {
        width: 100%;
        padding: 0.65rem 0.9rem;
        min-height: 46px;
        font-size: 0.95rem;
        border: 1px solid #d1d5db;
        border-radius: 0 0.55rem 0.55rem 0;
        font-family: inherit;
        transition: all 0.2s ease;
    }

    .phone-field .iti__tel-input:focus {
        outline: none;
        border-color: var(--brand-secondary);
        box-shadow: 0 0 0 3px rgba(55, 79, 167, 0.12);
    }

    .phone-field .iti--separate-dial-code .iti__selected-dial-code {
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--brand-primary);
    }

    .phone-field .iti__selected-country {
        border-radius: 0.55rem 0 0 0.55rem;
        background: #f9fafb;
        border: 1px solid #d1d5db;
        border-right: none;
        padding: 0 0.5rem;
    }

    .phone-field .iti__selected-country:hover {
        background: #f3f4f6;
    }

    .phone-field .iti__arrow {
        border-top-color: var(--brand-muted);
    }

    .phone-field .iti__country-list {
        max-height: 280px;
        font-size: 0.9rem;
    }

    @media (max-width: 900px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <h1 class="page-title">My <span>Profile</span></h1>
</div>

@if(session('success'))
<div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:0.75rem 1rem;border-radius:4px;margin-bottom:1.5rem;font-size:0.85rem;">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:0.75rem 1rem;border-radius:4px;margin-bottom:1.5rem;font-size:0.85rem;">
    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<div class="profile-grid">
    <div class="card-custom">
        <div class="profile-summary">
            <div class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div class="profile-name">{{ $user->name }}</div>
            <div class="profile-email">{{ $user->email }}</div>

            <div class="profile-roles">
                @if($user->is_super_admin)
                <span class="profile-role-badge" style="background:#fef3c7;color:#92400e;">Super Admin</span>
                @endif
                @forelse($user->roles as $role)
                <span class="profile-role-badge" style="background:#e0e7ff;color:#3730a3;">{{ $role->name }}</span>
                @empty
                @unless($user->is_super_admin)
                <span class="profile-role-badge" style="background:#f3f4f6;color:#6b7280;">No roles</span>
                @endunless
                @endforelse
            </div>

            <div class="profile-meta">
                <div class="profile-meta-row">
                    <span class="profile-meta-label">Member since</span>
                    <span class="profile-meta-value">{{ $user->created_at->format('M j, Y') }}</span>
                </div>
                <div class="profile-meta-row">
                    <span class="profile-meta-label">Last update</span>
                    <span class="profile-meta-value">{{ $user->updated_at->diffForHumans() }}</span>
                </div>
            </div>
            <a href="{{ route('showings.index') }}" class="btn-create" style="width:100%;justify-content:center;margin-top:1.5rem;">
                <i class="bi bi-calendar-event"></i>
                @if(auth()->user()->isClient())
                My Showings
                @else
                Manage Showings
                @endif
            </a>
        </div>
    </div>

    <div>
        <div class="card-custom" style="margin-bottom:1.5rem;">
            <div class="card-header-inner">
                <i class="bi bi-person"></i>
                <span>Personal information</span>
            </div>
            <form method="POST" action="{{ route('profile.update') }}" id="profileForm">
                @csrf
                @method('PUT')
                <div style="padding:1.5rem;">
                    <div class="form-group">
                        <label class="form-label" for="name">Full name</label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email address</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="profilePhoneInput">Phone number</label>
                        <div class="phone-field">
                            <input type="tel" id="profilePhoneInput"
                                class="form-control @error('phone') is-invalid @enderror"
                                placeholder="(000) 000-0000"
                                autocomplete="tel">
                        </div>
                        <input type="hidden" name="phone" id="profilePhoneHidden" value="{{ old('phone', $user->phone) }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn-create">
                        <i class="bi bi-check-lg"></i> Save changes
                    </button>
                </div>
            </form>
        </div>

        <div class="card-custom">
            <div class="card-header-inner">
                <i class="bi bi-shield-lock"></i>
                <span>Change password</span>
            </div>
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PUT')
                <div style="padding:1.5rem;">
                    <div class="form-group">
                        <label class="form-label" for="current_password">Current password</label>
                        <input type="password" id="current_password" name="current_password"
                            class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">New password</label>
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Minimum 8 characters" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm new password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-control" required>
                    </div>

                    <button type="submit" class="btn-create">
                        <i class="bi bi-shield-check"></i> Update password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/intlTelInputWithUtils.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('profilePhoneInput');
    var hidden = document.getElementById('profilePhoneHidden');
    var form = document.getElementById('profileForm');

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

    form.addEventListener('submit', function () {
        syncHidden();
    });
});
</script>
@endpush