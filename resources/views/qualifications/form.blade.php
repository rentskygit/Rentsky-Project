@extends('layouts.app')
@section('title', $qualification ? 'Edit Qualification' : 'New Qualification')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/css/intlTelInput.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<style>
    .wiz-progress {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 2rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }

    .wiz-step {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--brand-muted);
        padding: 0.4rem 0.75rem;
        border-radius: 9999px;
        border: 1px solid var(--border);
        white-space: nowrap;
        background: white;
        cursor: pointer;
    }

    .wiz-step.active {
        color: white;
        background: var(--brand-primary);
        border-color: var(--brand-primary);
    }

    .wiz-step.done {
        color: #16a34a;
        border-color: #86efac;
        background: #f0fdf4;
    }

    .wiz-step-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.06);
        font-size: 0.7rem;
        font-weight: 700;
    }

    .wiz-step.active .wiz-step-num {
        background: rgba(255, 255, 255, 0.2);
    }

    .wiz-step.done .wiz-step-num {
        background: #16a34a;
        color: white;
    }

    .wiz-page-title {
        font-family: var(--font-display);
        font-size: 1.35rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        margin-bottom: 0.25rem;
    }

    .wiz-page-desc {
        font-size: 0.85rem;
        color: var(--brand-muted);
        margin-bottom: 1.5rem;
    }

    .wiz-field {
        margin-bottom: 1.25rem;
    }

    .wiz-field label {
        display: block;
        font-size: 0.83rem;
        font-weight: 500;
        margin-bottom: 0.4rem;
        color: var(--brand-primary);
    }

    .wiz-field .req {
        color: var(--brand-secondary);
        margin-left: 0.15rem;
    }

    .wiz-field .help {
        font-size: 0.75rem;
        color: var(--brand-muted);
        margin-top: 0.3rem;
    }

    .wiz-field .help.error-text {
        color: #dc2626;
        font-weight: 500;
    }

    .wiz-input {
        width: 100%;
        padding: 0.65rem 0.85rem;
        border: 1px solid var(--border-strong);
        border-radius: 4px;
        font-size: 0.88rem;
        font-family: inherit;
        color: var(--brand-primary);
        background: white;
        transition: all 0.15s ease;
        min-height: 42px;
    }

    .wiz-input:focus {
        outline: none;
        border-color: var(--brand-primary);
    }

    .wiz-input.error {
        border-color: #dc2626;
    }

    select.wiz-input {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236b7280'%3e%3cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.85rem center;
        background-size: 12px 12px;
        padding-right: 2.5rem;
        cursor: pointer;
    }

    select.wiz-input:hover {
        border-color: #9ca3af;
    }

    select.wiz-input:focus {
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%230a0a0a'%3e%3cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3e%3c/svg%3e");
    }

    input.date-input {
        cursor: pointer;
        background: white;
    }

    input.date-input[readonly] {
        cursor: not-allowed;
        background: var(--surface);
        opacity: 0.7;
    }

    .flatpickr-calendar {
        font-family: var(--font-sans);
        border-radius: 6px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        z-index: 100;
    }

    .flatpickr-day.selected,
    .flatpickr-day.selected:hover {
        background: var(--brand-primary);
        border-color: var(--brand-primary);
    }

    .flatpickr-day.today {
        border-color: var(--brand-primary);
    }

    .flatpickr-day:hover {
        background: var(--surface-alt);
    }

    .flatpickr-months .flatpickr-month {
        color: var(--brand-primary);
    }

    .wiz-multi-wrap {
        position: relative;
    }

    .wiz-multi-trigger {
        width: 100%;
        padding: 0.65rem 0.85rem;
        min-height: 42px;
        border: 1px solid var(--border-strong);
        border-radius: 4px;
        font-size: 0.88rem;
        font-family: inherit;
        color: var(--brand-primary);
        background: white;
        text-align: left;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        transition: all 0.15s ease;
    }

    .wiz-multi-trigger:hover {
        border-color: #9ca3af;
    }

    .wiz-multi-trigger:focus {
        outline: none;
        border-color: var(--brand-primary);
    }

    .wiz-multi-trigger.error {
        border-color: #dc2626;
    }

    .wiz-multi-label {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: var(--brand-primary);
    }

    .wiz-multi-label.placeholder {
        color: #9ca3af;
    }

    .wiz-multi-arrow {
        transition: transform 0.15s ease;
        font-size: 0.75rem;
        color: var(--brand-muted);
        flex-shrink: 0;
    }

    .wiz-multi-arrow.rotated {
        transform: rotate(180deg);
    }

    .wiz-multi-menu {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: white;
        border: 1px solid var(--border-strong);
        border-radius: 4px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        z-index: 60;
        max-height: 260px;
        overflow-y: auto;
        padding: 0.35rem;
    }

    .wiz-multi-menu::-webkit-scrollbar {
        width: 6px;
    }

    .wiz-multi-menu::-webkit-scrollbar-thumb {
        background: var(--border-strong);
        border-radius: 3px;
    }

    .wiz-multi-item {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.5rem 0.65rem;
        border-radius: 3px;
        cursor: pointer;
        font-size: 0.83rem;
        font-weight: 500;
        transition: background 0.1s ease;
        user-select: none;
    }

    .wiz-multi-item:hover {
        background: var(--surface);
    }

    .wiz-multi-item.checked {
        background: var(--surface-alt);
    }

    .wiz-multi-item input[type="checkbox"] {
        accent-color: var(--brand-secondary);
        cursor: pointer;
        width: 15px;
        height: 15px;
        flex-shrink: 0;
    }

    .wiz-check-single {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 0.85rem;
        border: 1px solid var(--border-strong);
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.83rem;
        font-weight: 500;
        background: white;
        transition: all 0.15s ease;
        user-select: none;
    }

    .wiz-check-single:hover {
        border-color: var(--brand-primary);
    }

    .wiz-check-single.checked {
        border-color: var(--brand-primary);
        background: var(--surface-alt);
    }

    .wiz-check-single input {
        accent-color: var(--brand-secondary);
        cursor: pointer;
    }

    .wiz-input-group {
        display: flex;
        align-items: stretch;
        border: 1px solid var(--border-strong);
        border-radius: 4px;
        overflow: hidden;
        background: white;
    }

    .wiz-input-group:focus-within {
        border-color: var(--brand-primary);
    }

    .wiz-input-group.error {
        border-color: #dc2626;
    }

    .wiz-input-prefix {
        display: flex;
        align-items: center;
        padding: 0 0.85rem;
        background: var(--surface);
        color: var(--brand-muted);
        font-weight: 600;
        font-size: 0.85rem;
        border-right: 1px solid var(--border);
    }

    .wiz-input-group .wiz-input {
        border: none;
        border-radius: 0;
    }

    .wiz-input-group .wiz-input:focus {
        box-shadow: none;
    }

    .wiz-nav {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .phone-field .iti {
        width: 100%;
    }

    .phone-field .iti__tel-input {
        width: 100%;
        padding: 0.65rem 0.85rem;
        min-height: 42px;
        border: 1px solid var(--border-strong);
        border-radius: 0 4px 4px 0;
        font-size: 0.88rem;
        font-family: inherit;
        transition: all 0.15s ease;
    }

    .phone-field .iti__tel-input:focus {
        outline: none;
        border-color: var(--brand-primary);
    }

    .phone-field .iti__tel-input.error {
        border-color: #dc2626;
    }

    .phone-field .iti--separate-dial-code .iti__selected-dial-code {
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--brand-primary);
    }

    .phone-field .iti__selected-country {
        border-radius: 4px 0 0 4px;
        background: var(--surface);
        border: 1px solid var(--border-strong);
        border-right: none;
        padding: 0 0.5rem;
    }

    .phone-field .iti__selected-country:hover {
        background: var(--surface-alt);
    }

    .phone-field .iti__arrow {
        border-top-color: var(--brand-muted);
    }

    .phone-field .iti__country-list {
        max-height: 280px;
        font-size: 0.85rem;
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <h1 class="page-title">
        @if($qualification) Edit <span>Qualification</span>
        @else New <span>Qualification</span>
        @endif
    </h1>
    <a href="{{ route('qualifications.index') }}" class="btn-create" style="background:#6b7280;border-color:#6b7280;">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:0.75rem 1rem;border-radius:4px;margin-bottom:1.5rem;font-size:0.85rem;">
    {{ session('error') }}
</div>
@endif

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:0.75rem 1rem;border-radius:4px;margin-bottom:1.5rem;font-size:0.85rem;">
    <strong>Please check the following errors:</strong>
    <ul style="margin-top:0.4rem;padding-left:1.25rem;">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if($isAdmin)
<div class="card-custom" style="margin-bottom:1.5rem;">
    <div class="card-header-inner">
        <i class="bi bi-person-plus"></i>
        <span>Assign to user</span>
        <span class="badge-count" style="background:var(--surface);color:var(--brand-muted);">optional</span>
    </div>
    <div style="padding:1.25rem 1.5rem;">
        <div class="wiz-field" style="margin-bottom:0.75rem;">
            <label style="display:block;font-size:0.83rem;font-weight:500;margin-bottom:0.4rem;color:var(--brand-primary);">
                Client
                <span class="help" style="display:inline;font-size:0.75rem;color:var(--brand-muted);font-weight:400;margin-left:0.35rem;">
                    — leave empty for a walk-in intake
                </span>
            </label>
            <select
                id="user_id_select"
                class="wiz-input"
                style="max-width:520px;"
            >
                <option value="">— Walk-in / intake (no linked user) —</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" @selected($targetUser && $targetUser->id === $u->id)>
                        {{ $u->name }} — {{ $u->email }}
                    </option>
                @endforeach
            </select>
        </div>
        <p style="font-size:0.78rem;color:var(--brand-muted);line-height:1.5;margin:0;">
            <i class="bi bi-info-circle" style="margin-right:0.25rem;"></i>
            Only users with the <strong>Client</strong> role appear in this list. If no user is selected, the qualification is saved as a walk-in intake using the data below.
        </p>

        <div id="conflict-warning" style="display:none;margin-top:0.85rem;padding:0.85rem 1rem;background:#fffbeb;border:1px solid #fde68a;border-radius:6px;font-size:0.85rem;color:#78350f;line-height:1.5;">
            <i class="bi bi-exclamation-triangle-fill" style="margin-right:0.35rem;"></i>
            <strong id="conflict-user-name">This user</strong> already has a qualification (<span id="conflict-qual-id">#</span>).
            Submitting this form will <strong>overwrite</strong> that qualification with the current data.
        </div>

        <div id="conflict-confirm-row" style="display:none;margin-top:0.75rem;align-items:center;gap:0.5rem;font-size:0.85rem;font-weight:500;color:var(--brand-primary);">
            <input type="checkbox" id="confirm_overwrite_visual" style="width:16px;height:16px;cursor:pointer;">
            <label for="confirm_overwrite_visual" style="cursor:pointer;margin:0;">Yes, overwrite the existing qualification.</label>
        </div>
    </div>
</div>
@endif

<form method="POST"
    action="{{ $qualification ? route('qualifications.update', $qualification) : route('qualifications.store') }}"
    id="qualificationForm"
    novalidate
    x-data="qualificationWizard({
          schema: @js($schema),
          standardPath: @js($standardPath),
          voucherPath: @js($voucherPath),
          initial: @js($initialData),
          authId: {{ auth()->id() }},
          qualId: {{ $qualification?->id ?? 'null' }},
          isAdmin: {{ $isAdmin ? 'true' : 'false' }},
      })"
    @input="saveDraft()"
    @change="saveDraft()"
    @submit="prepareSubmit($event)">
    @csrf
    @if($qualification)
    @method('PUT')
    @endif

    @if($isAdmin)
    <input type="hidden" name="user_id" id="user_id_hidden" value="{{ $qualification?->user_id ?? '' }}">
    <input type="hidden" name="confirm_overwrite" id="confirm_overwrite_hidden" value="0">
    @endif

    <div class="wiz-progress">
        <template x-for="(stepKey, index) in visibleSteps" :key="stepKey">
            <button type="button" class="wiz-step"
                :class="{ active: currentStep === index, done: currentStep > index }"
                @click="gotoStep(index)">
                <span class="wiz-step-num" x-text="index + 1"></span>
                <span x-text="schema[stepKey].title"></span>
            </button>
        </template>
    </div>

    <div class="card-custom">
        <div style="padding:2rem;">
            <template x-for="(stepKey, stepIndex) in visibleSteps" :key="stepKey">
                <div x-show="currentStep === stepIndex">
                    <h2 class="wiz-page-title" x-text="schema[stepKey].title"></h2>
                    <p class="wiz-page-desc" x-show="schema[stepKey].description" x-text="schema[stepKey].description"></p>

                    <template x-for="(field, fieldKey) in schema[stepKey].fields" :key="fieldKey">
                        <div class="wiz-field" x-show="isVisible(field)">

                            <template x-if="field.type === 'heading'">
                                <div style="margin:0.5rem 0 1rem;">
                                    <h3 style="font-family:var(--font-display);font-size:1.05rem;font-weight:700;color:var(--brand-primary);letter-spacing:-0.01em;margin:0 0 0.25rem;" x-text="field.text"></h3>
                                    <p x-show="field.subtext" style="font-size:0.85rem;color:var(--brand-muted);margin:0;" x-text="field.subtext"></p>
                                </div>
                            </template>

                            <label x-show="field.type !== 'checkbox' && field.type !== 'heading'">
                                <span x-text="field.label"></span>
                                <span class="req" x-show="field.required">*</span>
                            </label>

                            <template x-if="field.type === 'text'">
                                <input type="text" class="wiz-input" x-model="data[fieldKey]"
                                    :class="{ error: errors[fieldKey] }"
                                    :maxlength="field.maxlength || null"
                                    :inputmode="field.numeric ? 'numeric' : (field.inputmode || null)"
                                    :pattern="field.numeric ? '[0-9]*' : null"
                                    @input="field.numeric && stripNonNumeric($event, fieldKey)"
                                    :placeholder="field.placeholder || ''">
                            </template>

                            <template x-if="field.type === 'email'">
                                <input type="email" class="wiz-input" x-model="data[fieldKey]"
                                    :class="{ error: errors[fieldKey] }"
                                    @input="validateField(fieldKey, field)">
                            </template>

                            <template x-if="field.type === 'textarea'">
                                <textarea class="wiz-input" x-model="data[fieldKey]"
                                    :class="{ error: errors[fieldKey] }"
                                    :rows="field.rows || 4"></textarea>
                            </template>

                            <template x-if="field.type === 'number' && field.prefix === '$'">
                                <div class="wiz-input-group" :class="{ error: errors[fieldKey] }">
                                    <span class="wiz-input-prefix">$</span>
                                    <input type="text" inputmode="decimal" class="wiz-input"
                                        :value="getMoneyDisplay(fieldKey)"
                                        @focus="moneyStates[fieldKey] = true"
                                        @blur="onMoneyBlur(fieldKey)"
                                        @input="onMoneyInput($event, fieldKey)">
                                </div>
                            </template>

                            <template x-if="field.type === 'number' && field.prefix !== '$'">
                                <input type="number" class="wiz-input" x-model="data[fieldKey]"
                                    :class="{ error: errors[fieldKey] }"
                                    :min="field.min || null"
                                    :step="field.step || 1">
                            </template>

                            <template x-if="field.type === 'date'">
                                <input type="text"
                                    class="wiz-input date-input"
                                    :class="{ error: errors[fieldKey] }"
                                    x-init="initFlatpickr($el, fieldKey, field)"
                                    placeholder="MM/DD/YYYY">
                            </template>

                            <template x-if="field.type === 'select'">
                                <select class="wiz-input"
                                    :class="{ error: errors[fieldKey] }"
                                    x-ref="selRef"
                                    @change="data[fieldKey] = $event.target.value"
                                    x-init="$nextTick(() => {
                                            const v = data[fieldKey];
                                            if (v !== undefined && v !== null && v !== '' && $refs.selRef) {
                                                $refs.selRef.value = String(v);
                                            }
                                        })">
                                    <option value="">— Select —</option>
                                    <template x-for="opt in filteredOptions(field, fieldKey)" :key="opt">
                                        <option :value="opt"
                                            :selected="String(data[fieldKey] ?? '') === String(opt)"
                                            x-text="opt"></option>
                                    </template>
                                </select>
                            </template>

                            <template x-if="field.type === 'multiselect'">
                                <div class="wiz-multi-wrap" x-data="{ open: false }" @click.outside="open = false">
                                    <button type="button" class="wiz-multi-trigger"
                                        :class="{ error: errors[fieldKey] }"
                                        @click.prevent="open = !open">
                                        <span class="wiz-multi-label"
                                            :class="{ placeholder: !data[fieldKey] || data[fieldKey].length === 0 }"
                                            x-text="multiLabel(data[fieldKey])"></span>
                                        <i class="bi bi-chevron-down wiz-multi-arrow" :class="{ rotated: open }"></i>
                                    </button>
                                    <div class="wiz-multi-menu" x-show="open" x-transition.opacity.duration.120ms style="display:none;">
                                        <template x-for="opt in field.options" :key="opt">
                                            <label class="wiz-multi-item" :class="{ checked: (data[fieldKey] || []).includes(opt) }">
                                                <input type="checkbox"
                                                    :checked="(data[fieldKey] || []).includes(opt)"
                                                    @change="toggleMulti(fieldKey, opt)">
                                                <span x-text="opt"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <template x-if="field.type === 'checkbox'">
                                <label class="wiz-check-single" :class="{ checked: !!data[fieldKey] }">
                                    <input type="checkbox" x-model="data[fieldKey]">
                                    <span x-text="field.label"></span>
                                </label>
                            </template>

                            <template x-if="field.type === 'phone'">
                                <div>
                                    <div class="phone-field">
                                        <input type="tel" x-init="initPhone($el, fieldKey)"
                                            @input="validateField(fieldKey, field)">
                                    </div>
                                    <small class="text-muted" style="display:block;margin-top:0.35rem;">
                                        Select your country and enter your phone number — the country code is added automatically.
                                    </small>
                                </div>
                            </template>

                            <div class="help" x-show="field.help && !errors[fieldKey]" x-text="field.help"></div>
                            <div class="help error-text" x-show="errors[fieldKey]" x-text="errors[fieldKey]"></div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <div style="padding:1rem 2rem 1.5rem;border-top:1px solid var(--border);">
            <div class="wiz-nav">
                <button type="button" class="btn-edit" @click="prevStep" x-show="currentStep > 0">
                    <i class="bi bi-arrow-left"></i> Previous
                </button>
                <div style="flex:1;"></div>
                <button type="button" class="btn-create" @click="nextStep" x-show="currentStep < visibleSteps.length - 1">
                    Continue <i class="bi bi-arrow-right"></i>
                </button>
                <button type="submit" class="btn-create" x-show="currentStep === visibleSteps.length - 1">
                    <i class="bi bi-check-lg"></i> Submit
                </button>
            </div>
        </div>
    </div>
</form>

@if($isAdmin && !$qualification)
<div id="userExistsModal" style="display:none; position: fixed; inset: 0; z-index: 99999; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); padding: 1.5rem;">
    <div style="background: white; border-radius: 10px; padding: 2rem; max-width: 480px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.2);">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem; color: #f59e0b; display: block; margin-bottom: 0.75rem;"></i>
            <h3 style="font-family: var(--font-display); font-size: 1.15rem; font-weight: 700; color: var(--brand-primary); margin-bottom: 0.5rem;">
                User already has a registration
            </h3>
            <p style="font-size: 0.9rem; color: var(--brand-muted); line-height: 1.5; margin: 0;">
                <strong id="existingUserName">This user</strong> already has a qualification on file. Would you like to edit it instead?
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
        try {
            fn = new Function('data', 'return (' + expr + ');');
        } catch (e) {
            fn = () => true;
        }
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
        const map = {
            us: 10, ca: 10, pr: 10, do: 10, gb: 10, es: 9,
            mx: 10, co: 10, ar: 10, br: 11, cl: 9, pe: 9,
            fr: 9, de: 11, it: 10, pt: 9
        };
        return map[iso2] || null;
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('qualificationWizard', ({
            schema,
            standardPath,
            voucherPath,
            initial,
            authId,
            qualId,
            isAdmin
        }) => ({
            schema,
            standardPath,
            voucherPath,
            authId,
            qualId,
            isAdmin,
            data: initial && typeof initial === 'object' ? {
                ...initial
            } : {},
            errors: {},
            moneyStates: {},
            currentStep: 0,
            todayDate: new Date().toISOString().split('T')[0],
            phoneInstances: {},
            _saveTimer: null,

            get storageKey() {
                return 'qual_draft_' + this.authId + '_' + (this.qualId || 'new');
            },

            init() {
                const saved = localStorage.getItem(this.storageKey);
                if (saved) {
                    try {
                        const parsed = JSON.parse(saved);
                        if (parsed && typeof parsed === 'object') {
                            this.data = {
                                ...parsed,
                                ...this.data
                            };
                        }
                    } catch (e) {}
                }
            },

            get hasVoucher() {
                const v = this.data.has_voucher;
                return v === true || v === 1 || v === '1' || v === 'true';
            },

            get visibleSteps() {
                const path = this.hasVoucher ? this.voucherPath : this.standardPath;
                return path.filter(key => this.schema[key]);
            },

            isVisible(field) {
                if (field.show_when) return this.evaluateCondition(field.show_when);
                if (field.hide_when) return !this.evaluateCondition(field.hide_when);
                return true;
            },

            evaluateCondition(expr) {
                if (!expr) return true;
                try {
                    return !!compileCondition(expr)(this.data);
                } catch (e) {
                    return true;
                }
            },

            filteredOptions(field, key) {
                let opts = (field.options || []).slice();
                if (key === 'adults_count' || key === 'minors_count' || key === 'employed_count') {
                    const pc = parseInt(this.data.people_count);
                    if (!isNaN(pc) && pc > 0) {
                        opts = opts.filter(o => {
                            const n = parseInt(o);
                            return isNaN(n) || n <= pc;
                        });
                    }
                }
                if (key === 'minors_count') {
                    const ac = parseInt(this.data.adults_count);
                    const pc = parseInt(this.data.people_count);
                    if (!isNaN(ac) && !isNaN(pc) && pc >= ac) {
                        const maxM = pc - ac;
                        opts = opts.filter(o => {
                            const n = parseInt(o);
                            return isNaN(n) || n <= maxM;
                        });
                    }
                }
                return opts;
            },

            multiLabel(arr) {
                if (!Array.isArray(arr) || arr.length === 0) return '— Select —';
                if (arr.length === 1) return arr[0];
                if (arr.length === 2) return arr.join(', ');
                return arr.length + ' selected';
            },

            toggleMulti(fieldKey, value) {
                if (!Array.isArray(this.data[fieldKey])) this.data[fieldKey] = [];
                const idx = this.data[fieldKey].indexOf(value);
                if (idx === -1) this.data[fieldKey].push(value);
                else this.data[fieldKey].splice(idx, 1);
                this.data[fieldKey] = [...this.data[fieldKey]];
            },

            stripNonNumeric(e, fieldKey) {
                const cleaned = e.target.value.replace(/[^0-9]/g, '');
                this.data[fieldKey] = cleaned;
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
                return num.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
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
                if (!iti) return {
                    digits: '',
                    iso2: '',
                    dialCode: ''
                };

                const el = iti.telInput;
                const countryData = iti.getSelectedCountryData();
                const dialCode = (countryData && countryData.dialCode) || '';
                const iso2 = (countryData && countryData.iso2) || '';

                let full = '';
                try {
                    full = iti.getNumber() || '';
                } catch (e) {
                    full = el ? el.value : '';
                }

                let digits = full.replace(/\D/g, '');
                if (dialCode && digits.startsWith(dialCode)) {
                    digits = digits.slice(dialCode.length);
                }

                return {
                    digits,
                    iso2,
                    dialCode
                };
            },

            isValidPhone(key) {
                const iti = this.phoneInstances[key];
                if (!iti) return true;

                const {
                    digits,
                    iso2
                } = this.getNationalDigits(key);
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

            validateField(key, field) {
                if (field.type === 'heading') return;

                if (field.type === 'phone' && this.phoneInstances[key]) {
                    const iti = this.phoneInstances[key];
                    const {
                        digits
                    } = this.getNationalDigits(key);
                    if (digits.length === 0) {
                        this.data[key] = '';
                    } else {
                        const full = iti.getNumber() || '';
                        this.data[key] = full;
                    }
                }

                const val = this.data[key];
                if (field.required && this.isEmpty(val)) {
                    this.errors[key] = field.label + ' is required.';
                    return;
                }
                if (!this.isEmpty(val)) {
                    if (field.type === 'email' && !this.isValidEmail(val)) {
                        this.errors[key] = 'Please enter a valid email address.';
                        return;
                    }
                    if (field.type === 'phone') {
                        if (!this.isValidPhone(key)) {
                            const {
                                digits,
                                iso2
                            } = this.getNationalDigits(key);
                            const expected = expectedNationalDigits(iso2);
                            if (expected !== null && digits.length > 0 && digits.length !== expected) {
                                this.errors[key] = 'Phone number must have exactly ' + expected + ' digits for the selected country.';
                            } else {
                                this.errors[key] = 'Please enter a valid phone number for the selected country.';
                            }
                            return;
                        }
                    }
                }
                delete this.errors[key];
            },

            validateCurrentPage() {
                const stepKey = this.visibleSteps[this.currentStep];
                const page = this.schema[stepKey];
                let valid = true;
                for (const [key, field] of Object.entries(page.fields || {})) {
                    if (!this.isVisible(field)) continue;
                    this.validateField(key, field);
                    if (this.errors[key]) valid = false;
                }
                return valid;
            },

            gotoStep(index) {
                if (index <= this.currentStep) this.currentStep = index;
            },

            nextStep() {
                if (!this.validateCurrentPage()) return;
                if (this.currentStep < this.visibleSteps.length - 1) this.currentStep++;
            },

            prevStep() {
                if (this.currentStep > 0) this.currentStep--;
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
                        try {
                            iti.setNumber(e164);
                        } catch (e) {}
                    }
                }

                const enforceLimit = () => {
                    const {
                        digits,
                        iso2,
                        dialCode
                    } = this.getNationalDigits(key);
                    const expected = expectedNationalDigits(iso2);
                    const maxDigits = expected !== null ? expected : 15;

                    if (digits.length > maxDigits) {
                        const truncated = '+' + dialCode + digits.slice(0, maxDigits);
                        try {
                            iti.setNumber(truncated);
                        } catch (e) {}
                    }
                };

                const sync = () => {
                    enforceLimit();

                    const {
                        digits
                    } = this.getNationalDigits(key);
                    if (digits.length === 0) {
                        this.data[key] = '';
                    } else {
                        const full = iti.getNumber() || '';
                        this.data[key] = full;
                    }

                    const field = this.findField(key);
                    if (field) this.validateField(key, field);
                };

                el.addEventListener('input', sync);
                el.addEventListener('blur', sync);
                el.addEventListener('change', sync);
                el.addEventListener('countrychange', sync);

                setTimeout(() => {
                    const full = iti.getNumber() || '';
                    if (full) {
                        try {
                            iti.setNumber(full);
                        } catch (e) {}
                    }
                }, 50);

                sync();
            },

            findField(key) {
                for (const pageKey of Object.keys(this.schema)) {
                    const f = this.schema[pageKey].fields?.[key];
                    if (f) return f;
                }
                return null;
            },

            saveDraft() {
                clearTimeout(this._saveTimer);
                this._saveTimer = setTimeout(() => {
                    try {
                        localStorage.setItem(this.storageKey, JSON.stringify(this.data));
                    } catch (e) {}
                }, 400);
            },

            prepareSubmit(e) {
                if (!this.validateCurrentPage()) {
                    e.preventDefault();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                    return;
                }

                Object.keys(this.phoneInstances).forEach(key => {
                    const iti = this.phoneInstances[key];
                    const full = iti.getNumber() || '';
                    this.data[key] = full;
                });

                const form = e.target;
                form.querySelectorAll('input[type="hidden"][data-dynamic]').forEach(el => el.remove());

                Object.keys(this.data).forEach(key => {
                    const value = this.data[key];
                    if (value === null || value === undefined || value === '') return;
                    if (Array.isArray(value)) {
                        value.forEach(v => {
                            const inp = document.createElement('input');
                            inp.type = 'hidden';
                            inp.name = key + '[]';
                            inp.value = v;
                            inp.dataset.dynamic = '1';
                            form.appendChild(inp);
                        });
                    } else if (typeof value === 'boolean') {
                        const inp = document.createElement('input');
                        inp.type = 'hidden';
                        inp.name = key;
                        inp.value = value ? '1' : '0';
                        inp.dataset.dynamic = '1';
                        form.appendChild(inp);
                    } else {
                        const inp = document.createElement('input');
                        inp.type = 'hidden';
                        inp.name = key;
                        inp.value = value;
                        inp.dataset.dynamic = '1';
                        form.appendChild(inp);
                    }
                });

                try {
                    localStorage.removeItem(this.storageKey);
                } catch (e) {}
            },
        }));
    });

    function checkUserQualification(userId) {
        if (!userId) return;
        const url = `{{ route('qualifications.check-user', ':id') }}`.replace(':id', userId);
        fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
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
        const hidden = document.getElementById('user_id_hidden');
        if (hidden) hidden.value = '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const userSelect = document.getElementById('user_id_select');
        const hidden = document.getElementById('user_id_hidden');

        if (userSelect && hidden) {
            const sync = () => {
                hidden.value = userSelect.value || '';
            };
            userSelect.addEventListener('change', sync);
            sync();
        }

        @if(!$qualification)
        if (userSelect) {
            userSelect.addEventListener('change', function() {
                checkUserQualification(this.value);
            });
        }
        @endif

        @if($qualification && $isAdmin)
        (function() {
            var select = userSelect;
            var warning = document.getElementById('conflict-warning');
            var confirmRow = document.getElementById('conflict-confirm-row');
            var visual = document.getElementById('confirm_overwrite_visual');
            var hiddenConfirm = document.getElementById('confirm_overwrite_hidden');
            var userNameEl = document.getElementById('conflict-user-name');
            var qualIdEl = document.getElementById('conflict-qual-id');
            var form = document.getElementById('qualificationForm');

            if (!select || !warning || !confirmRow || !form) return;

            var conflictActive = false;

            function hideWarning() {
                warning.style.display = 'none';
                confirmRow.style.display = 'none';
                if (visual) visual.checked = false;
                if (hiddenConfirm) hiddenConfirm.value = '0';
                conflictActive = false;
            }

            function showWarning(name, id) {
                userNameEl.textContent = name;
                qualIdEl.textContent = '#' + id;
                warning.style.display = 'block';
                confirmRow.style.display = 'flex';
                conflictActive = true;
            }

            select.addEventListener('change', function() {
                var userId = this.value;

                if (!userId) {
                    hideWarning();
                    return;
                }

                fetch('{{ url("/qualifications/" . $qualification->id . "/user-conflict") }}/' + userId, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(r) { return r.ok ? r.json() : { has_conflict: false }; })
                .then(function(data) {
                    if (data.has_conflict) {
                        showWarning(data.user_name, data.qualification_id);
                    } else {
                        hideWarning();
                    }
                })
                .catch(function() { hideWarning(); });
            });

            if (visual) {
                visual.addEventListener('change', function() {
                    if (hiddenConfirm) hiddenConfirm.value = this.checked ? '1' : '0';
                });
            }

            form.addEventListener('submit', function(e) {
                if (conflictActive && visual && !visual.checked) {
                    e.preventDefault();
                    alert('Please check "Yes, overwrite the existing qualification" to proceed.');
                    visual.focus();
                }
            });
        })();
        @endif
    });
</script>
@endpush