@extends('layouts.app')
@section('title', $application ? 'Edit Application' : 'New Application')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/css/intlTelInput.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<style>
    .app-wrapper { max-width: 960px; margin: 0 auto; }

    .app-intro {
        background: linear-gradient(135deg, #fafafa 0%, #ffffff 100%);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    .app-intro h2 {
        font-family: var(--font-display);
        font-size: 1.4rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--brand-primary);
        margin-bottom: 0.35rem;
    }
    .app-intro p { font-size: 0.88rem; color: var(--brand-muted); line-height: 1.6; margin: 0; }

    .app-section {
        background: white;
        border: 1px solid var(--border);
        border-radius: 10px;
        margin-bottom: 1.25rem;
        overflow: hidden;
    }
    .app-section-header {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 1.15rem 1.5rem;
        border-bottom: 1px solid var(--border);
        background: white;
    }
    .app-section-num {
        display: inline-flex; align-items: center; justify-content: center;
        width: 32px; height: 32px; border-radius: 50%;
        background: var(--brand-primary); color: white;
        font-size: 0.82rem; font-weight: 700; flex-shrink: 0;
    }
    .app-section-icon {
        display: inline-flex; align-items: center; justify-content: center;
        width: 32px; height: 32px; border-radius: 8px;
        background: var(--surface-alt); color: var(--brand-secondary);
        font-size: 1rem; flex-shrink: 0;
    }
    .app-section-title {
        font-family: var(--font-display); font-size: 1rem; font-weight: 700;
        letter-spacing: -0.01em; color: var(--brand-primary); margin: 0; flex: 1;
    }
    .app-section-body { padding: 1.5rem; }
    .app-section-desc {
        font-size: 0.85rem; color: var(--brand-muted); line-height: 1.55;
        margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px dashed var(--border);
    }

    .app-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem 1.25rem; }
    .app-field { display: flex; flex-direction: column; min-width: 0; }
    .app-field.full { grid-column: 1 / -1; }

    .app-field-label {
        display: block; font-size: 0.78rem; font-weight: 500;
        color: var(--brand-primary); margin-bottom: 0.4rem; letter-spacing: 0.01em;
    }
    .app-field-label .req { color: var(--brand-secondary); margin-left: 0.15rem; }
    .app-field-help { font-size: 0.72rem; color: var(--brand-muted); margin-top: 0.3rem; }
    .app-field-error { font-size: 0.75rem; color: #dc2626; margin-top: 0.3rem; font-weight: 500; }

    .app-input {
        width: 100%; padding: 0.65rem 0.85rem;
        border: 1px solid var(--border-strong); border-radius: 6px;
        font-size: 0.88rem; font-family: inherit; color: var(--brand-primary);
        background: white; transition: all 0.15s ease; min-height: 42px;
    }
    .app-input:focus {
        outline: none; border-color: var(--brand-secondary);
        box-shadow: 0 0 0 3px rgba(55, 79, 167, 0.08);
    }
    .app-input.error { border-color: #dc2626; }

    select.app-input {
        appearance: none; -webkit-appearance: none; -moz-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236b7280'%3e%3cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3e%3c/svg%3e");
        background-repeat: no-repeat; background-position: right 0.85rem center;
        background-size: 12px 12px; padding-right: 2.5rem; cursor: pointer;
    }
    select.app-input:hover { border-color: #9ca3af; }

    .app-input-group {
        display: flex; align-items: stretch;
        border: 1px solid var(--border-strong); border-radius: 6px;
        overflow: hidden; background: white; transition: all 0.15s ease;
    }
    .app-input-group:focus-within {
        border-color: var(--brand-secondary);
        box-shadow: 0 0 0 3px rgba(55, 79, 167, 0.08);
    }
    .app-input-group.error { border-color: #dc2626; }
    .app-input-prefix {
        display: flex; align-items: center; padding: 0 0.85rem;
        background: var(--surface); color: var(--brand-muted);
        font-weight: 600; font-size: 0.85rem; border-right: 1px solid var(--border);
    }
    .app-input-group .app-input { border: none; border-radius: 0; min-height: 40px; }
    .app-input-group .app-input:focus { box-shadow: none; }

    .app-check-single {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.6rem 0.95rem;
        border: 1px solid var(--border-strong); border-radius: 6px;
        cursor: pointer; font-size: 0.83rem; font-weight: 500;
        background: white; transition: all 0.15s ease; user-select: none;
    }
    .app-check-single:hover { border-color: var(--brand-primary); }
    .app-check-single.checked { border-color: var(--brand-secondary); background: #f5f7fb; }
    .app-check-single input { accent-color: var(--brand-secondary); cursor: pointer; }

    .app-single-check-group { display: flex; flex-wrap: wrap; gap: 0.5rem; }

    .app-checkbox-label {
        display: inline-flex; align-items: center; gap: 0.55rem;
        padding: 0.7rem 0.95rem;
        border: 1px solid var(--border-strong); border-radius: 6px;
        cursor: pointer; font-size: 0.85rem; font-weight: 500;
        background: white; transition: all 0.15s ease;
        user-select: none; line-height: 1.4;
    }
    .app-checkbox-label:hover { border-color: var(--brand-primary); }
    .app-checkbox-label.checked { border-color: var(--brand-secondary); background: #f5f7fb; font-weight: 600; }
    .app-checkbox-label input { accent-color: var(--brand-secondary); cursor: pointer; flex-shrink: 0; }

    .app-subheading {
        grid-column: 1 / -1; padding: 0.75rem 0 0.25rem;
        margin-top: 0.5rem; border-bottom: 1px solid var(--border);
    }
    .app-subheading:first-child { margin-top: 0; padding-top: 0; border-bottom: none; }
    .app-subheading h4 {
        font-family: var(--font-display); font-size: 0.82rem; font-weight: 700;
        color: var(--brand-secondary); text-transform: uppercase;
        letter-spacing: 0.06em; margin: 0 0 0.25rem;
    }
    .app-subheading p { font-size: 0.78rem; color: var(--brand-muted); margin: 0; }

    .app-footer {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 1rem 1.25rem;
        background: white; border: 1px solid var(--border); border-radius: 10px;
        margin-top: 1.5rem; flex-wrap: wrap;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    }
    .app-footer-step {
        flex: 1; font-size: 0.8rem; color: var(--brand-muted); font-weight: 500;
        min-width: 120px;
    }
    .app-footer-step strong { color: var(--brand-primary); font-weight: 700; }

    .app-nav-btn {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.6rem 1.2rem; border-radius: 6px;
        font-weight: 600; font-size: 0.85rem; cursor: pointer;
        transition: all 0.15s; font-family: inherit;
        border: 1px solid var(--border-strong);
        background: white; color: var(--brand-primary);
    }
    .app-nav-btn:hover { background: var(--surface); border-color: var(--brand-primary); }
    .app-nav-btn.primary {
        background: var(--brand-secondary);
        border-color: var(--brand-secondary); color: white;
    }
    .app-nav-btn.primary:hover { background: #2a3d85; border-color: #2a3d85; }
    .app-nav-btn.submit {
        background: #16a34a; border-color: #16a34a; color: white;
    }
    .app-nav-btn.submit:hover { background: #15803d; border-color: #15803d; }

    .phone-field .iti { width: 100%; }
    .phone-field .iti__tel-input {
        width: 100%; padding: 0.65rem 0.85rem; min-height: 42px;
        border: 1px solid var(--border-strong);
        border-radius: 0 6px 6px 0; font-size: 0.88rem;
        font-family: inherit; transition: all 0.15s ease;
    }
    .phone-field .iti__tel-input:focus {
        outline: none; border-color: var(--brand-secondary);
    }
    .phone-field .iti__tel-input.error { border-color: #dc2626; }
    .phone-field .iti__selected-country {
        border-radius: 6px 0 0 6px; background: var(--surface);
        border: 1px solid var(--border-strong); border-right: none;
        padding: 0 0.5rem;
    }
    .phone-field .iti__selected-country:hover { background: var(--surface-alt); }
    .phone-field .iti--separate-dial-code .iti__selected-dial-code {
        font-size: 0.85rem; font-weight: 500; color: var(--brand-primary);
    }

    .flatpickr-calendar {
        font-family: var(--font-sans); border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12); z-index: 100;
    }
    .flatpickr-day.selected,
    .flatpickr-day.selected:hover {
        background: var(--brand-secondary);
        border-color: var(--brand-secondary);
    }
    .flatpickr-day.today { border-color: var(--brand-secondary); }
    .flatpickr-day:hover { background: var(--surface-alt); }

    input.date-input { cursor: pointer; background: white; }
    input.date-input[readonly] { cursor: not-allowed; background: var(--surface); opacity: 0.7; }

    @media (max-width: 768px) {
        .app-grid { grid-template-columns: 1fr; gap: 0.85rem; }
        .app-section-body { padding: 1rem; }
        .app-section-header { padding: 0.85rem 1rem; }
        .app-footer { flex-direction: column; align-items: stretch; }
        .app-footer-step { text-align: center; margin-bottom: 0.25rem; }
        .app-nav-btn { width: 100%; justify-content: center; }
        .app-intro { padding: 1.25rem 1rem; }
    }
</style>
@endpush

@section('content')

<div class="app-wrapper">

<div class="page-header">
    <h1 class="page-title">
        @if($application) Edit <span>Application</span>
        @else New <span>Application</span>
        @endif
    </h1>
    <a href="{{ route('applications.index') }}" class="btn-edit">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

@if($isAdmin)
<div class="card-custom" style="margin-bottom:1.5rem;">
    <div style="padding:1rem 1.25rem;">
        <label class="form-label" style="font-size:0.83rem;font-weight:600;">Assign to user</label>
        <select id="user_id_select" name="user_id" class="form-control" style="max-width:420px;" {{ $application ? 'disabled' : '' }}>
            <option value="">— Select a user —</option>
            @foreach($users as $u)
            <option value="{{ $u->id }}" @selected(($targetUser && $targetUser->id === $u->id) || (!$application && auth()->id() === $u->id))>{{ $u->name }} — {{ $u->email }}</option>
            @endforeach
        </select>
        @if($application)
        <small class="text-muted" style="display:block;margin-top:0.4rem;">User cannot be changed once created.</small>
        @endif
    </div>
</div>
@endif

<form method="POST"
    action="{{ $application ? route('applications.update', $application) : route('applications.store') }}"
    id="applicationForm"
    novalidate
    x-data="applicationForm({
        schema: @js($schema),
        path: @js($path),
        initial: @js($initialData),
        authId: {{ auth()->id() }},
        appId: {{ $application?->id ?? 'null' }},
        isAdmin: {{ $isAdmin ? 'true' : 'false' }},
    })"
    @input="saveDraft()"
    @change="saveDraft()"
    @submit="prepareSubmit($event)">
    @csrf
    @if($application)
    @method('PUT')
    @endif

    @if($isAdmin && !$application)
    <input type="hidden" name="user_id" id="user_id_hidden" value="">
    @endif

    <div class="app-intro" x-show="currentStep === 0">
        <h2>Rental Application</h2>
        <p>Complete each section below. You can navigate back and forth; your progress is saved automatically.</p>
    </div>

    @php
        $sectionMeta = [
            'rental_application' => ['num' => 1, 'icon' => 'bi-person-badge', 'title' => 'Applicant & Contact'],
            'current_address' => ['num' => 2, 'icon' => 'bi-house-door', 'title' => 'Current Address'],
            'previous_addresses' => ['num' => 3, 'icon' => 'bi-clock-history', 'title' => 'Previous Address'],
            'references' => ['num' => 4, 'icon' => 'bi-people', 'title' => 'References & Emergency Contact'],
            'occupants' => ['num' => 5, 'icon' => 'bi-people-fill', 'title' => 'Co-Applicants & Occupants'],
            'pets' => ['num' => 6, 'icon' => 'bi-heart', 'title' => 'Pets'],
            'employment' => ['num' => 7, 'icon' => 'bi-briefcase', 'title' => 'Employment & Income'],
            'questions' => ['num' => 8, 'icon' => 'bi-clipboard-check', 'title' => 'Background & Signature'],
        ];
    @endphp

    @foreach($path as $stepKey)
        @php
            $page = $schema[$stepKey] ?? null;
            $meta = $sectionMeta[$stepKey] ?? ['num' => '?', 'icon' => 'bi-file-text', 'title' => $page['title'] ?? 'Section'];
        @endphp
        @if($page)
            <div class="app-section" x-show="currentStepKey === '{{ $stepKey }}'">
                <div class="app-section-header">
                    <span class="app-section-num">{{ $meta['num'] }}</span>
                    <span class="app-section-icon"><i class="bi {{ $meta['icon'] }}"></i></span>
                    <h3 class="app-section-title">{{ $meta['title'] }}</h3>
                </div>
                <div class="app-section-body">
                    @if(!empty($page['description']))
                        <div class="app-section-desc">{{ $page['description'] }}</div>
                    @endif

                    <div class="app-grid">
                        @foreach($page['fields'] as $fieldKey => $field)
                            <div class="app-field"
                                 :class="{ 'full': {{ in_array($field['type'], ['textarea', 'single_check', 'heading', 'subheading', 'checkbox']) ? 'true' : 'false' }} }"
                                 x-show="isVisible('{{ $fieldKey }}')">

                                @if($field['type'] === 'heading' || $field['type'] === 'subheading')
                                    <div class="app-subheading">
                                        <h4>{{ $field['text'] ?? '' }}</h4>
                                        @if(!empty($field['subtext']))
                                            <p>{{ $field['subtext'] }}</p>
                                        @endif
                                    </div>

                                @elseif($field['type'] === 'checkbox')
                                    <label class="app-checkbox-label" :class="{ checked: !!data['{{ $fieldKey }}'] }">
                                        <input type="checkbox" x-model="data['{{ $fieldKey }}']">
                                        <span>
                                            {{ $field['label'] ?? '' }}
                                            @if(!empty($field['required']))<span class="req">*</span>@endif
                                        </span>
                                    </label>

                                @elseif($field['type'] === 'single_check')
                                    <div class="app-field-label">
                                        {{ $field['label'] ?? '' }}
                                        @if(!empty($field['required']))<span class="req">*</span>@endif
                                    </div>
                                    <div class="app-single-check-group">
                                        @foreach(($field['options'] ?? []) as $val => $optLabel)
                                            <label class="app-check-single" :class="{ checked: isSingleChecked('{{ $fieldKey }}', '{{ $val }}') }">
                                                <input type="checkbox"
                                                       :checked="isSingleChecked('{{ $fieldKey }}', '{{ $val }}')"
                                                       @change="setSingleCheck('{{ $fieldKey }}', '{{ $val }}')">
                                                <span>{{ $optLabel }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="app-field-error" x-show="errors['{{ $fieldKey }}']" x-text="errors['{{ $fieldKey }}']"></div>

                                @elseif($field['type'] === 'phone')
                                    <label class="app-field-label">
                                        {{ $field['label'] ?? 'Phone' }}
                                        @if(!empty($field['required']))<span class="req">*</span>@endif
                                    </label>
                                    <div class="phone-field">
                                        <input type="tel" x-init="initPhone($el, '{{ $fieldKey }}')"
                                               @input="validateField('{{ $fieldKey }}')">
                                    </div>
                                    <div class="app-field-error" x-show="errors['{{ $fieldKey }}']" x-text="errors['{{ $fieldKey }}']"></div>

                                @elseif($field['type'] === 'number' && ($field['prefix'] ?? '') === '$')
                                    <label class="app-field-label">
                                        {{ $field['label'] ?? '' }}
                                        @if(!empty($field['required']))<span class="req">*</span>@endif
                                    </label>
                                    <div class="app-input-group" :class="{ error: errors['{{ $fieldKey }}'] }">
                                        <span class="app-input-prefix">$</span>
                                        <input type="text" inputmode="decimal" class="app-input"
                                               :value="getMoneyDisplay('{{ $fieldKey }}')"
                                               @focus="moneyStates['{{ $fieldKey }}'] = true"
                                               @blur="onMoneyBlur('{{ $fieldKey }}')"
                                               @input="onMoneyInput($event, '{{ $fieldKey }}')">
                                    </div>
                                    <div class="app-field-error" x-show="errors['{{ $fieldKey }}']" x-text="errors['{{ $fieldKey }}']"></div>

                                @elseif($field['type'] === 'number')
                                    <label class="app-field-label">
                                        {{ $field['label'] ?? '' }}
                                        @if(!empty($field['required']))<span class="req">*</span>@endif
                                    </label>
                                    <input type="number" class="app-input" x-model="data['{{ $fieldKey }}']"
                                           :class="{ error: errors['{{ $fieldKey }}'] }"
                                           @if(!empty($field['min'])) min="{{ $field['min'] }}" @endif
                                           @if(!empty($field['step'])) step="{{ $field['step'] }}" @endif>
                                    <div class="app-field-error" x-show="errors['{{ $fieldKey }}']" x-text="errors['{{ $fieldKey }}']"></div>

                                @elseif($field['type'] === 'date')
                                    <label class="app-field-label">
                                        {{ $field['label'] ?? '' }}
                                        @if(!empty($field['required']))<span class="req">*</span>@endif
                                    </label>
                                    <input type="text" class="app-input date-input"
                                           :class="{ error: errors['{{ $fieldKey }}'] }"
                                           x-init="initFlatpickr($el, '{{ $fieldKey }}', {{ json_encode($field) }})"
                                           placeholder="MM/DD/YYYY">
                                    <div class="app-field-error" x-show="errors['{{ $fieldKey }}']" x-text="errors['{{ $fieldKey }}']"></div>

                                @elseif($field['type'] === 'select')
                                    <label class="app-field-label">
                                        {{ $field['label'] ?? '' }}
                                        @if(!empty($field['required']))<span class="req">*</span>@endif
                                    </label>
                                    <select class="app-input" :class="{ error: errors['{{ $fieldKey }}'] }"
                                            x-model="data['{{ $fieldKey }}']">
                                        <option value="">— Select —</option>
                                        @foreach(($field['options'] ?? []) as $opt)
                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                    <div class="app-field-error" x-show="errors['{{ $fieldKey }}']" x-text="errors['{{ $fieldKey }}']"></div>

                                @elseif($field['type'] === 'email')
                                    <label class="app-field-label">
                                        {{ $field['label'] ?? 'Email' }}
                                        @if(!empty($field['required']))<span class="req">*</span>@endif
                                    </label>
                                    <input type="email" class="app-input" x-model="data['{{ $fieldKey }}']"
                                           :class="{ error: errors['{{ $fieldKey }}'] }"
                                           @input="validateField('{{ $fieldKey }}')">
                                    <div class="app-field-error" x-show="errors['{{ $fieldKey }}']" x-text="errors['{{ $fieldKey }}']"></div>

                                @elseif($field['type'] === 'zip')
                                    <label class="app-field-label">
                                        {{ $field['label'] ?? 'ZIP Code' }}
                                        @if(!empty($field['required']))<span class="req">*</span>@endif
                                    </label>
                                    <input type="text" inputmode="numeric" maxlength="5" pattern="\d{5}"
                                           class="app-input" x-model="data['{{ $fieldKey }}']"
                                           :class="{ error: errors['{{ $fieldKey }}'] }"
                                           @input="stripNonNumeric($event, '{{ $fieldKey }}'); validateField('{{ $fieldKey }}')">
                                    <div class="app-field-error" x-show="errors['{{ $fieldKey }}']" x-text="errors['{{ $fieldKey }}']"></div>

                                @elseif($field['type'] === 'textarea')
                                    <label class="app-field-label">
                                        {{ $field['label'] ?? '' }}
                                        @if(!empty($field['required']))<span class="req">*</span>@endif
                                    </label>
                                    <textarea class="app-input" x-model="data['{{ $fieldKey }}']"
                                              :class="{ error: errors['{{ $fieldKey }}'] }"
                                              rows="{{ $field['rows'] ?? 3 }}"></textarea>
                                    <div class="app-field-error" x-show="errors['{{ $fieldKey }}']" x-text="errors['{{ $fieldKey }}']"></div>

                                @else
                                    <label class="app-field-label">
                                        {{ $field['label'] ?? '' }}
                                        @if(!empty($field['required']))<span class="req">*</span>@endif
                                    </label>
                                    <input type="text" class="app-input" x-model="data['{{ $fieldKey }}']"
                                           :class="{ error: errors['{{ $fieldKey }}'] }"
                                           @if(!empty($field['maxlength'])) maxlength="{{ $field['maxlength'] }}" @endif
                                           @if(!empty($field['numeric'])) inputmode="numeric" pattern="[0-9]*" @input="stripNonNumeric($event, '{{ $fieldKey }}')" @endif
                                           placeholder="{{ $field['placeholder'] ?? '' }}">
                                    @if(!empty($field['help']))
                                        <div class="app-field-help">{{ $field['help'] }}</div>
                                    @endif
                                    <div class="app-field-error" x-show="errors['{{ $fieldKey }}']" x-text="errors['{{ $fieldKey }}']"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <div class="app-footer" x-show="visibleSteps.length > 0">
        <div class="app-footer-step">
            Section <strong x-text="currentStep + 1"></strong> of <strong x-text="visibleSteps.length"></strong>
        </div>
        <button type="button" class="app-nav-btn" @click="prevStep()" x-show="currentStep > 0">
            <i class="bi bi-arrow-left"></i> Previous
        </button>
        <button type="button" class="app-nav-btn primary" @click="nextStep()" x-show="currentStep < visibleSteps.length - 1">
            Continue <i class="bi bi-arrow-right"></i>
        </button>
        <button type="submit" class="app-nav-btn submit" x-show="currentStep === visibleSteps.length - 1">
            <i class="bi bi-send"></i> Submit Application
        </button>
    </div>
</form>

@if($isAdmin && !$application)
<div id="userExistsModal" style="display:none; position: fixed; inset: 0; z-index: 99999; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); padding: 1.5rem;">
    <div style="background: white; border-radius: 10px; padding: 2rem; max-width: 480px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.2);">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem; color: #f59e0b; display: block; margin-bottom: 0.75rem;"></i>
            <h3 style="font-family: var(--font-display); font-size: 1.15rem; font-weight: 700; color: var(--brand-primary); margin-bottom: 0.5rem;">
                User already has a registration
            </h3>
            <p style="font-size: 0.9rem; color: var(--brand-muted); line-height: 1.5; margin: 0;">
                <strong id="existingUserName">This user</strong> already has an application on file. Would you like to edit it instead?
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
            <a id="existingEditLink" href="#" class="btn-create" style="min-width: 140px; justify-content: center;">
                <i class="bi bi-pencil"></i> Edit existing
            </a>
            <button type="button" onclick="closeUserExistsModal()" class="btn-edit" style="min-width: 100px; justify-content: center;">
                Cancel
            </button>
        </div>
    </div>
</div>
@endif

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/intlTelInputWithUtils.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"></script>
<script>
    const conditionCache = new Map();

    function compileCondition(expr) {
        if (conditionCache.has(expr)) return conditionCache.get(expr);
        let fn;
        try { fn = new Function('data', 'return (' + expr + ');'); }
        catch (e) { fn = () => true; }
        conditionCache.set(expr, fn);
        return fn;
    }

    function normalizeToE164(raw) {
        if (!raw) return '';
        const trimmed = String(raw).trim();
        const digits = trimmed.replace(/\D/g, '');
        if (digits.length === 0) return '';
        if (trimmed.startsWith('+')) return digits.length >= 7 ? '+' + digits : '';
        if (digits.length === 10) return '+1' + digits;
        if (digits.length === 11 && digits.startsWith('1')) return '+' + digits;
        return digits.length >= 7 ? '+' + digits : '';
    }

    function expectedNationalDigits(iso2) {
        const map = { us: 10, ca: 10, pr: 10, do: 10, gb: 10, es: 9, mx: 10, co: 10, ar: 10, br: 11, cl: 9, pe: 9, fr: 9, de: 11, it: 10, pt: 9 };
        return map[iso2] || null;
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('applicationForm', ({ schema, path, initial, authId, appId, isAdmin }) => ({
            schema, path, authId, appId, isAdmin,
            data: initial && typeof initial === 'object' ? { ...initial } : {},
            errors: {},
            moneyStates: {},
            currentStep: 0,
            todayDate: new Date().toISOString().split('T')[0],
            phoneInstances: {},
            _saveTimer: null,

            get storageKey() {
                return 'app_draft_' + this.authId + '_' + (this.appId || 'new');
            },

            init() {
                const saved = localStorage.getItem(this.storageKey);
                if (saved) {
                    try {
                        const parsed = JSON.parse(saved);
                        if (parsed && typeof parsed === 'object') {
                            this.data = { ...parsed, ...this.data };
                        }
                    } catch (e) {}
                }
            },

            get showPreviousAddresses() {
                if (!this.data.resided_from) return false;
                const rf = new Date(this.data.resided_from);
                if (isNaN(rf.getTime())) return false;
                const cutoff = new Date();
                cutoff.setFullYear(cutoff.getFullYear() - 5);
                return rf > cutoff;
            },

            get visibleSteps() {
                return this.path.filter(k => {
                    if (k === 'previous_addresses') return this.showPreviousAddresses;
                    return !!this.schema[k];
                });
            },

            get currentStepKey() {
                return this.visibleSteps[this.currentStep] || null;
            },

            isVisible(fieldKey) {
                for (const pageKey of Object.keys(this.schema)) {
                    const field = this.schema[pageKey]?.fields?.[fieldKey];
                    if (!field) continue;
                    if (field.show_when) return this.evaluateCondition(field.show_when);
                    if (field.hide_when) return !this.evaluateCondition(field.hide_when);
                    return true;
                }
                return true;
            },

            evaluateCondition(expr) {
                if (!expr) return true;
                try { return !!compileCondition(expr)(this.data); }
                catch (e) { return true; }
            },

            findField(key) {
                for (const pageKey of Object.keys(this.schema)) {
                    const f = this.schema[pageKey].fields?.[key];
                    if (f) return f;
                }
                return null;
            },

            isSingleChecked(fieldKey, val) {
                return String(this.data[fieldKey] ?? '') === String(val);
            },

            setSingleCheck(fieldKey, val) {
                const current = String(this.data[fieldKey] ?? '');
                const v = String(val);
                this.data[fieldKey] = (current === v) ? '' : val;
            },

            stripNonNumeric(e, key) {
                const cleaned = e.target.value.replace(/[^0-9]/g, '');
                this.data[key] = cleaned;
                e.target.value = cleaned;
            },

            initFlatpickr(el, key, field) {
                if (!el || el._flatpickr) return;
                if (typeof window.flatpickr !== 'function') {
                    setTimeout(() => this.initFlatpickr(el, key, field), 100);
                    return;
                }

                const isLocked = field.auto_today === true && !this.isAdmin;
                if (field.auto_today && !this.data[key]) {
                    this.data[key] = this.todayDate;
                }

                const fp = window.flatpickr(el, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'm/d/Y',
                    allowInput: false,
                    disableMobile: true,
                    clickOpens: !isLocked,
                    defaultDate: this.data[key] || null,
                    minDate: field.min_today ? this.todayDate : null,
                    maxDate: field.max_today ? this.todayDate : null,
                    onChange: (selectedDates, dateStr) => {
                        this.data[key] = dateStr;
                    },
                });

                if (isLocked && this.data[key]) {
                    fp.setDate(this.data[key], false, 'Y-m-d');
                }
            },

            getMoneyDisplay(key) {
                if (this.moneyStates[key]) return this.data[key] ?? '';
                const num = parseFloat(this.data[key]);
                if (isNaN(num)) return '';
                return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },

            onMoneyBlur(key) {
                this.moneyStates[key] = false;
                const num = parseFloat(this.data[key]);
                this.data[key] = isNaN(num) ? '' : num.toFixed(2);
            },

            onMoneyInput(e, key) {
                let v = e.target.value.replace(/[^0-9.]/g, '');
                const parts = v.split('.');
                if (parts.length > 2) v = parts[0] + '.' + parts.slice(1).join('');
                else if (parts.length === 2 && parts[1].length > 2) v = parts[0] + '.' + parts[1].slice(0, 2);
                this.data[key] = v;
                e.target.value = v;
            },

            isValidEmail(email) {
                if (!email) return false;
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            },

            getNationalDigits(key) {
                const iti = this.phoneInstances[key];
                if (!iti) return { digits: '', iso2: '', dialCode: '' };

                const el = iti.telInput;
                const countryData = iti.getSelectedCountryData();
                const dialCode = (countryData && countryData.dialCode) || '';
                const iso2 = (countryData && countryData.iso2) || '';

                let full = '';
                try { full = iti.getNumber() || ''; } catch (e) { full = el ? el.value : ''; }

                let digits = full.replace(/\D/g, '');
                if (dialCode && digits.startsWith(dialCode)) {
                    digits = digits.slice(dialCode.length);
                }
                return { digits, iso2, dialCode };
            },

            isValidPhone(key) {
                const iti = this.phoneInstances[key];
                if (!iti) return true;

                const { digits, iso2 } = this.getNationalDigits(key);
                const expected = expectedNationalDigits(iso2);

                if (expected !== null) {
                    if (digits.length !== expected) return false;
                } else {
                    if (digits.length < 7 || digits.length > 15) return false;
                }

                if (typeof iti.isValidNumber === 'function') {
                    try {
                        const result = iti.isValidNumber();
                        if (result === true) return true;
                        if (result === false) return false;
                    } catch (e) {}
                }
                return true;
            },

            isEmpty(val) {
                if (val === null || val === undefined || val === '') return true;
                if (Array.isArray(val) && val.length === 0) return true;
                return false;
            },

            validateField(key) {
                const field = this.findField(key);
                if (!field) return;
                if (field.type === 'heading' || field.type === 'subheading') return;

                if (field.type === 'phone' && this.phoneInstances[key]) {
                    const iti = this.phoneInstances[key];
                    const { digits } = this.getNationalDigits(key);
                    this.data[key] = digits.length === 0 ? '' : (iti.getNumber() || '');
                }

                const val = this.data[key];
                if (field.required && this.isEmpty(val)) {
                    this.errors[key] = (field.label || field.type) + ' is required.';
                    return;
                }
                if (!this.isEmpty(val)) {
                    if (field.type === 'email' && !this.isValidEmail(val)) {
                        this.errors[key] = 'Please enter a valid email address.';
                        return;
                    }
                    if (field.type === 'phone' && !this.isValidPhone(key)) {
                        const { digits, iso2 } = this.getNationalDigits(key);
                        const expected = expectedNationalDigits(iso2);
                        if (expected !== null && digits.length > 0 && digits.length !== expected) {
                            this.errors[key] = 'Phone number must have exactly ' + expected + ' digits for the selected country.';
                        } else {
                            this.errors[key] = 'Please enter a valid phone number.';
                        }
                        return;
                    }
                    if (field.type === 'zip' && !/^\d{5}$/.test(String(val))) {
                        this.errors[key] = 'ZIP code must be exactly 5 digits.';
                        return;
                    }
                }
                delete this.errors[key];
            },

            validateSection(stepKey) {
                const page = this.schema[stepKey];
                if (!page || !page.fields) return true;
                let valid = true;
                for (const [key] of Object.entries(page.fields)) {
                    if (!this.isVisible(key)) continue;
                    this.validateField(key);
                    if (this.errors[key]) valid = false;
                }
                return valid;
            },

            scrollToFirstError() {
                setTimeout(() => {
                    const firstError = document.querySelector('.app-field-error:not([style*="display: none"])');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 100);
            },

            nextStep() {
                if (!this.validateSection(this.currentStepKey)) {
                    if (window.showAlert) {
                        window.showAlert('warning', 'Please complete this section', 'Some required fields need your attention before continuing.');
                    }
                    this.scrollToFirstError();
                    return;
                }
                if (this.currentStep < this.visibleSteps.length - 1) {
                    this.currentStep++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            prevStep() {
                if (this.currentStep > 0) {
                    this.currentStep--;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            initPhone(el, key) {
                if (this.phoneInstances[key]) return;
                if (typeof window.intlTelInput !== 'function') {
                    setTimeout(() => this.initPhone(el, key), 100);
                    return;
                }

                const iti = window.intlTelInput(el, {
                    initialCountry: 'us',
                    preferredCountries: ['us', 'ca', 'mx', 'pr', 'do', 'co', 'gb', 'es'],
                    nationalMode: true,
                    separateDialCode: true,
                    formatOnDisplay: true,
                    autoPlaceholder: 'polite',
                    customPlaceholder: (placeholder, countryData) => {
                        if (countryData.iso2 === 'us' || countryData.iso2 === 'ca') return '(000) 000-0000';
                        return placeholder || '';
                    },
                });
                this.phoneInstances[key] = iti;

                if (this.data[key]) {
                    const e164 = normalizeToE164(this.data[key]);
                    if (e164) {
                        try { iti.setNumber(e164); } catch (e) {}
                    }
                }

                const sync = () => {
                    const { digits, iso2, dialCode } = this.getNationalDigits(key);
                    const expected = expectedNationalDigits(iso2);
                    const maxDigits = expected !== null ? expected : 15;

                    if (digits.length > maxDigits) {
                        try { iti.setNumber('+' + dialCode + digits.slice(0, maxDigits)); } catch (e) {}
                    }

                    const d2 = this.getNationalDigits(key);
                    this.data[key] = d2.digits.length === 0 ? '' : (iti.getNumber() || '');
                    this.validateField(key);
                };

                el.addEventListener('input', sync);
                el.addEventListener('blur', sync);
                el.addEventListener('change', sync);
                el.addEventListener('countrychange', sync);

                setTimeout(() => {
                    const full = iti.getNumber() || '';
                    if (full) { try { iti.setNumber(full); } catch (e) {} }
                }, 50);

                sync();
            },

            saveDraft() {
                clearTimeout(this._saveTimer);
                this._saveTimer = setTimeout(() => {
                    try { localStorage.setItem(this.storageKey, JSON.stringify(this.data)); } catch (e) {}
                }, 400);
            },

            prepareSubmit(e) {
                let firstInvalidIndex = -1;
                for (let i = 0; i < this.visibleSteps.length; i++) {
                    if (!this.validateSection(this.visibleSteps[i])) {
                        if (firstInvalidIndex === -1) firstInvalidIndex = i;
                    }
                }

                if (firstInvalidIndex !== -1) {
                    e.preventDefault();
                    if (this.currentStep !== firstInvalidIndex) {
                        this.currentStep = firstInvalidIndex;
                        this.scrollToFirstError();
                    } else {
                        this.scrollToFirstError();
                    }
                    if (window.showAlert) {
                        window.showAlert('warning', 'Please check the form', 'Some required fields are missing or invalid.');
                    }
                    return;
                }

                Object.keys(this.phoneInstances).forEach(key => {
                    const iti = this.phoneInstances[key];
                    this.data[key] = iti.getNumber() || '';
                });

                const form = e.target;
                form.querySelectorAll('input[type="hidden"][data-dynamic]').forEach(el => el.remove());

                Object.keys(this.data).forEach(key => {
                    const value = this.data[key];
                    if (value === null || value === undefined || value === '') return;

                    if (Array.isArray(value)) {
                        value.forEach(v => {
                            const inp = document.createElement('input');
                            inp.type = 'hidden'; inp.name = key + '[]'; inp.value = v; inp.dataset.dynamic = '1';
                            form.appendChild(inp);
                        });
                    } else if (typeof value === 'boolean') {
                        const inp = document.createElement('input');
                        inp.type = 'hidden'; inp.name = key; inp.value = value ? '1' : '0'; inp.dataset.dynamic = '1';
                        form.appendChild(inp);
                    } else {
                        const inp = document.createElement('input');
                        inp.type = 'hidden'; inp.name = key; inp.value = value; inp.dataset.dynamic = '1';
                        form.appendChild(inp);
                    }
                });

                try { localStorage.removeItem(this.storageKey); } catch (e) {}
            },
        }));
    });

    function checkUserApplication(userId) {
        if (!userId) return;
        const url = `{{ route('applications.check-user', ':id') }}`.replace(':id', userId);
        fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (data && data.exists) {
                document.getElementById('existingUserName').textContent = data.user_name;
                document.getElementById('existingEditLink').href = data.edit_url;
                document.getElementById('userExistsModal').style.display = 'flex';
            }
        })
        .catch(err => console.error('Check user failed:', err));
    }

    function closeUserExistsModal() {
        document.getElementById('userExistsModal').style.display = 'none';
        const select = document.getElementById('user_id_select');
        if (select) select.value = '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const userSelect = document.getElementById('user_id_select');
        const hidden = document.getElementById('user_id_hidden');

        if (userSelect && hidden) {
            const sync = () => { hidden.value = userSelect.value || ''; };
            userSelect.addEventListener('change', sync);
            sync();
        }

        if (userSelect && !userSelect.disabled) {
            userSelect.addEventListener('change', function () {
                checkUserApplication(this.value);
            });
        }
    });
</script>
@endpush