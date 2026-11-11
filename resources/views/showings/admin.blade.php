@extends('layouts.app')
@section('title', 'Showings')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<style>
    .sf-filters {
        padding: 1.25rem;
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 0.85rem;
        align-items: end;
    }
    .sf-filters .form-group { margin: 0; }
    .sf-filters .form-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--brand-muted);
        font-weight: 600;
        margin-bottom: 0.35rem;
    }
    .sf-filters .form-control {
        font-size: 0.83rem;
        padding: 0.55rem 0.75rem;
        min-height: 40px;
    }
    .sf-filters select.form-control { padding-right: 2.25rem; }
    .sf-filters .btn-group { display: flex; gap: 0.4rem; }

    .show-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        padding: 1.5rem;
    }
    .show-modal-backdrop.open { display: flex; }

    .show-modal {
        background: white;
        border-radius: 12px;
        max-width: 520px;
        width: 100%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        animation: showModalIn 0.2s ease;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }
    @keyframes showModalIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
    .show-modal-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }
    .show-modal-header h3 {
        font-family: var(--font-display);
        font-size: 1.05rem;
        font-weight: 700;
        margin: 0;
    }
    .show-modal-close {
        background: none;
        border: none;
        font-size: 1.25rem;
        color: var(--brand-muted);
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .show-modal-close:hover {
        background: var(--surface);
        color: var(--brand-primary);
    }
    .show-modal-body {
        padding: 1.5rem;
        overflow-y: auto;
        flex: 1;
    }
    .show-modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        background: var(--surface);
        flex-shrink: 0;
    }

    .flatpickr-calendar {
        font-family: var(--font-sans);
        border-radius: 6px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        z-index: 100000;
    }
    .flatpickr-day.selected,
    .flatpickr-day.selected:hover {
        background: var(--brand-secondary);
        border-color: var(--brand-secondary);
    }
    .flatpickr-day.today { border-color: var(--brand-secondary); }

    @media (max-width: 1024px) { .sf-filters { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 600px)  { .sf-filters { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">Client <span>Showings</span></h1>

    @if($canManage)
        <button type="button" class="btn-create" onclick="openQuickShowingModal()">
            <i class="bi bi-plus-lg"></i> New showing
        </button>
    @endif
</div>

<div class="card-custom" style="margin-bottom:1.5rem;">
    <div class="card-header-inner">
        <i class="bi bi-funnel"></i>
        <span>Filters</span>
    </div>
    <form method="GET" action="{{ route('showings.index') }}">
        <div class="sf-filters">
            <div class="form-group">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control"
                       value="{{ request('search') }}"
                       placeholder="Name or email...">
            </div>

            <div class="form-group">
                <label class="form-label">Adviser</label>
                <select name="adviser" class="form-control">
                    <option value="">All</option>
                    <option value="unassigned" @selected(request('adviser') === 'unassigned')>Unassigned</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" @selected(request('adviser') == $agent->id)>
                            {{ $agent->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Min. showings</label>
                <input type="number" name="min_showings" min="1" class="form-control"
                       value="{{ request('min_showings') }}"
                       placeholder="e.g. 1">
            </div>

            <div class="form-group">
                <label class="form-label">Sort by</label>
                <select name="sort" class="form-control">
                    <option value="latest"        @selected(request('sort', 'latest') === 'latest')>Latest booking</option>
                    <option value="name_asc"      @selected(request('sort') === 'name_asc')>Name A–Z</option>
                    <option value="showings_desc" @selected(request('sort') === 'showings_desc')>Most showings</option>
                </select>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-create" style="padding:0.55rem 0.95rem;font-size:0.8rem;">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('showings.index') }}" class="btn-edit" style="padding:0.55rem 0.8rem;font-size:0.8rem;">
                    Clear
                </a>
            </div>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="card-header-inner">
        <i class="bi bi-calendar-event"></i>
        <span style="font-size:.875rem;font-weight:600;">
            {{ $isAdmin ? 'All clients with showings' : 'My assigned clients with showings' }}
        </span>
        <span class="badge-count">{{ $clients->total() }}</span>
    </div>

    @if($clients->isEmpty())
        <div class="empty-state">
            <i class="bi bi-people"></i>
            <p>No clients match the current filters.</p>
        </div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Showings</th>
                    <th>Latest booking</th>
                    <th>Adviser</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <span class="avatar">{{ strtoupper(substr($client->name, 0, 1)) }}</span>
                                <span class="user-name">{{ $client->name }}</span>
                            </div>
                        </td>
                        <td><span class="user-email">{{ $client->email }}</span></td>
                        <td>
                            @if($client->formatted_phone)
                                <span style="font-size:0.83rem;">{{ $client->formatted_phone }}</span>
                            @else
                                <span style="color:var(--brand-muted);font-size:0.8rem;">—</span>
                            @endif
                        </td>
                        <td><span class="badge-count">{{ $client->showings_count }}</span></td>
                        <td>
                            <span style="font-size:0.8rem;color:var(--brand-muted);">
                                {{ $client->showings_max_created_at ? \Carbon\Carbon::parse($client->showings_max_created_at)->diffForHumans() : '—' }}
                            </span>
                        </td>
                        <td>
                            @if($client->assignedAdviser)
                                <span style="font-size:0.83rem;">{{ $client->assignedAdviser->name }}</span>
                            @else
                                <span style="color:var(--brand-muted);font-size:0.8rem;">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="actions-cell justify-content-end">
                                <a href="{{ route('showings.user', $client) }}" class="btn-icon-action" title="View showings">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="padding:1rem 1.25rem;">{{ $clients->links() }}</div>
    @endif
</div>

@if($canManage)
<div class="show-modal-backdrop" id="quickShowingModal">
    <div class="show-modal">
        <div class="show-modal-header">
            <h3>New showing</h3>
            <button type="button" class="show-modal-close" onclick="closeQuickShowingModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route('showings.quick-store') }}" id="quickShowingForm">
            @csrf
            <div class="show-modal-body">
                @if($errors->any())
                    <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:0.75rem 1rem;border-radius:6px;margin-bottom:1rem;font-size:0.85rem;">
                        <ul style="margin:0;padding-left:1.25rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label class="form-label">Client</label>
                    <select name="user_id" id="quickShowingUser" class="form-control" required>
                        <option value="">— Select a client —</option>
                        @foreach($showableClients as $c)
                            <option value="{{ $c->id }}" @selected(old('user_id') == $c->id)>
                                {{ $c->name }} — {{ $c->email }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Property</label>
                    <select name="property_id" class="form-control" required>
                        <option value="">— Select a property —</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}" @selected(old('property_id') == $p->id)>
                                {{ $p->address }}
                                @if($p->unit_type) — {{ $p->unit_type }} @endif
                                @if($p->city), {{ $p->city }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="text"
                           name="showing_date"
                           id="quickShowingDate"
                           class="form-control"
                           placeholder="MM/DD/YYYY"
                           value="{{ old('showing_date') }}"
                           required>
                    <small class="text-muted" style="display:block;margin-top:0.35rem;">
                        This client can have up to <strong>3 showings per day</strong>.
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">Time</label>
                    <select name="showing_time" class="form-control" required>
                        <option value="">— Select a time —</option>
                        @foreach(['8:00 AM','9:00 AM','10:00 AM','11:00 AM','12:00 PM','1:00 PM','2:00 PM','3:00 PM','4:00 PM','5:00 PM','6:00 PM','7:00 PM','8:00 PM'] as $t)
                            <option value="{{ $t }}" @selected(old('showing_time') === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="pending"   @selected(old('status', 'pending') === 'pending')>Pending</option>
                        <option value="confirmed" @selected(old('status') === 'confirmed')>Confirmed</option>
                        <option value="completed" @selected(old('status') === 'completed')>Completed</option>
                        <option value="cancelled" @selected(old('status') === 'cancelled')>Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="show-modal-footer">
                <button type="button" class="btn-edit" onclick="closeQuickShowingModal()">Cancel</button>
                <button type="submit" class="btn-create">
                    <i class="bi bi-check-lg"></i> Create showing
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script>
(function () {
    'use strict';

    var fullDates = [];
    var datePicker = null;

    function fullDateFn(date) {
        var y = date.getFullYear();
        var m = String(date.getMonth() + 1).padStart(2, '0');
        var d = String(date.getDate()).padStart(2, '0');
        return fullDates.indexOf(y + '-' + m + '-' + d) !== -1;
    }

    function initDatePicker() {
        if (datePicker) {
            try { datePicker.destroy(); } catch (e) {}
            datePicker = null;
        }
        var input = document.getElementById('quickShowingDate');
        if (!input || typeof window.flatpickr !== 'function') return;

        datePicker = window.flatpickr('#quickShowingDate', {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'm/d/Y',
            allowInput: false,
            disableMobile: true,
            minDate: 'today',
            defaultDate: input.value || null,
            disable: [fullDateFn]
        });
    }

    window.openQuickShowingModal = function () {
        var modal = document.getElementById('quickShowingModal');
        if (!modal) return;
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
        initDatePicker();
    };

    window.closeQuickShowingModal = function () {
        var modal = document.getElementById('quickShowingModal');
        if (!modal) return;
        modal.classList.remove('open');
        document.body.style.overflow = '';
    };

    document.addEventListener('DOMContentLoaded', function () {
        var userSelect = document.getElementById('quickShowingUser');

        if (userSelect) {
            userSelect.addEventListener('change', function () {
                var id = this.value;
                fullDates = [];
                if (datePicker) datePicker.set('disable', [fullDateFn]);
                if (!id) return;

                fetch('{{ url("/showings") }}/user/' + id + '/full-dates', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function (r) { return r.ok ? r.json() : { full_dates: [] }; })
                .then(function (data) {
                    fullDates = data.full_dates || [];
                    if (datePicker) datePicker.set('disable', [fullDateFn]);
                })
                .catch(function () {});
            });
        }

        var modal = document.getElementById('quickShowingModal');
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === this) closeQuickShowingModal();
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeQuickShowingModal();
        });

        @if($errors->any() && old('user_id'))
            if (document.getElementById('quickShowingModal')) {
                openQuickShowingModal();
            }
        @endif
    });
})();
</script>
@endpush