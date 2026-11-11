@extends('layouts.app')
@section('title', 'Showings for ' . $client->name)

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<style>
    .uf-filters {
        padding: 1.25rem;
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1.5fr 1fr auto;
        gap: 0.85rem;
        align-items: end;
    }
    .uf-filters .form-group { margin: 0; }
    .uf-filters .form-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--brand-muted);
        font-weight: 600;
        margin-bottom: 0.35rem;
    }
    .uf-filters .form-control {
        font-size: 0.83rem;
        padding: 0.55rem 0.75rem;
        min-height: 40px;
    }
    .uf-filters select.form-control { padding-right: 2.25rem; }
    .uf-filters .btn-group { display: flex; gap: 0.4rem; }

    @media (max-width: 1280px) { .uf-filters { grid-template-columns: 1fr 1fr 1fr; } }
    @media (max-width: 900px)  { .uf-filters { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 600px)  { .uf-filters { grid-template-columns: 1fr; } }

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
        max-width: 500px;
        width: 100%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        animation: showModalIn 0.2s ease;
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
    .show-modal-body { padding: 1.5rem; }
    .show-modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        background: var(--surface);
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
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">Showings for <span>{{ $client->name }}</span></h1>
    <div style="display:flex;gap:0.5rem;">
        @if($canManage)
            <button type="button" class="btn-create" onclick="openCreateShowingModal()">
                <i class="bi bi-plus-lg"></i> New showing
            </button>
        @endif
        <a href="{{ route('showings.index') }}" class="btn-create" style="background:#6b7280;border-color:#6b7280;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="card-custom" style="margin-bottom:1.5rem;">
    <div class="card-header-inner">
        <i class="bi bi-funnel"></i>
        <span>Filters</span>
    </div>
    <form method="GET" action="{{ route('showings.user', $client) }}">
        <div class="uf-filters">
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All</option>
                    <option value="pending"   @selected(request('status') === 'pending')>Pending</option>
                    <option value="confirmed" @selected(request('status') === 'confirmed')>Confirmed</option>
                    <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="form-group">
                <label class="form-label">To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Property</label>
                <select name="property" class="form-control">
                    <option value="">All</option>
                    @foreach($properties as $p)
                        <option value="{{ $p->id }}" @selected(request('property') == $p->id)>
                            {{ $p->address }}
                            @if($p->unit_type)
                                — {{ $p->unit_type }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Sort</label>
                <select name="sort" class="form-control">
                    <option value="date_desc" @selected(request('sort', 'date_desc') === 'date_desc')>Newest first</option>
                    <option value="date_asc"  @selected(request('sort') === 'date_asc')>Oldest first</option>
                </select>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-create" style="padding:0.55rem 0.95rem;font-size:0.8rem;">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('showings.user', $client) }}" class="btn-edit" style="padding:0.55rem 0.8rem;font-size:0.8rem;">
                    Clear
                </a>
            </div>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="card-header-inner">
        <i class="bi bi-calendar-event"></i>
        <span style="font-size:.875rem;font-weight:600;">All showings</span>
        <span class="badge-count">{{ $showings->count() }}</span>
    </div>

    @if($showings->isEmpty())
        <div class="empty-state">
            <i class="bi bi-calendar-x"></i>
            <p>No showings match the current filters.</p>
            @if($canManage)
                <button type="button" class="btn-create" style="margin-top:1rem;" onclick="openCreateShowingModal()">
                    <i class="bi bi-plus-lg"></i> Schedule first showing
                </button>
            @endif
        </div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Property</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($showings as $showing)
                    <tr>
                        <td>
                            <div style="min-width:0;">
                                <div style="font-weight:500;font-size:0.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $showing->property->address ?? '—' }}
                                    @if($showing->property?->unit_type)
                                        {{ $showing->property->unit_type }}
                                    @endif
                                </div>
                                <div style="font-size:0.75rem;color:var(--brand-muted);">
                                    {{ $showing->property->city ?? '' }}
                                </div>
                            </div>
                        </td>
                        <td><span style="font-size:0.85rem;">{{ $showing->showing_date->format('M j, Y') }}</span></td>
                        <td><span style="font-size:0.85rem;">{{ $showing->showing_time }}</span></td>
                        <td>
                            <span class="status-badge {{ $showing->status_badge }}">
                                {{ $showing->status_label }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="actions-cell justify-content-end">
                                <button type="button"
                                        class="btn-icon-action"
                                        title="Edit"
                                        onclick="openEditShowingModal({{ $showing->id }}, '{{ $showing->showing_date->format('Y-m-d') }}', '{{ $showing->showing_time }}', '{{ $showing->status }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button"
                                        class="btn-icon-action"
                                        title="Change status"
                                        onclick="openStatusModal({{ $showing->id }}, '{{ $showing->status }}')">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                                <form method="POST"
                                      action="{{ route('showings.destroy', $showing) }}"
                                      onsubmit="return confirmDelete(event, '{{ addslashes($showing->property->address ?? '') }}')"
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon-action danger" title="Delete">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@if($canManage)
<div class="show-modal-backdrop" id="createShowingModal" onclick="if(event.target===this) closeCreateShowingModal()">
    <div class="show-modal">
        <div class="show-modal-header">
            <h3>New showing for {{ $client->name }}</h3>
            <button type="button" class="show-modal-close" onclick="closeCreateShowingModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route('showings.user.store', $client) }}">
            @csrf
            <div class="show-modal-body">
                <div class="form-group">
                    <label class="form-label">Property</label>
                    <select name="property_id" class="form-control" required>
                        <option value="">— Select a property —</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->address }}
                                @if($p->unit_type)
                                    — {{ $p->unit_type }}
                                @endif
                                @if($p->city)
                                    , {{ $p->city }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="text" name="showing_date" id="createShowingDate" class="form-control" placeholder="MM/DD/YYYY" required>
                    <small class="text-muted" style="display:block;margin-top:0.35rem;">
                        This client can have up to <strong>3 showings per day</strong>.
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">Time</label>
                    <select name="showing_time" class="form-control" required>
                        <option value="">— Select a time —</option>
                        @foreach(['8:00 AM','9:00 AM','10:00 AM','11:00 AM','12:00 PM','1:00 PM','2:00 PM','3:00 PM','4:00 PM','5:00 PM','6:00 PM','7:00 PM','8:00 PM'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="pending" selected>Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="show-modal-footer">
                <button type="button" class="btn-edit" onclick="closeCreateShowingModal()">Cancel</button>
                <button type="submit" class="btn-create">
                    <i class="bi bi-check-lg"></i> Create showing
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<div class="show-modal-backdrop" id="editShowingModal" onclick="if(event.target===this) closeEditShowingModal()">
    <div class="show-modal">
        <div class="show-modal-header">
            <h3>Edit showing</h3>
            <button type="button" class="show-modal-close" onclick="closeEditShowingModal()">&times;</button>
        </div>
        <form method="POST" id="editShowingForm">
            @csrf
            @method('PUT')
            <div class="show-modal-body">
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="text" name="showing_date" id="editShowingDate" class="form-control" placeholder="MM/DD/YYYY" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Time</label>
                    <select name="showing_time" id="editShowingTime" class="form-control" required>
                        @foreach(['8:00 AM','9:00 AM','10:00 AM','11:00 AM','12:00 PM','1:00 PM','2:00 PM','3:00 PM','4:00 PM','5:00 PM','6:00 PM','7:00 PM','8:00 PM'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="show-modal-footer">
                <button type="button" class="btn-edit" onclick="closeEditShowingModal()">Cancel</button>
                <button type="submit" class="btn-create">
                    <i class="bi bi-check-lg"></i> Save changes
                </button>
            </div>
        </form>
    </div>
</div>

<div class="show-modal-backdrop" id="statusModal" onclick="if(event.target===this) closeStatusModal()">
    <div class="show-modal">
        <div class="show-modal-header">
            <h3>Update status</h3>
            <button type="button" class="show-modal-close" onclick="closeStatusModal()">&times;</button>
        </div>
        <form method="POST" id="statusForm">
            @csrf
            @method('PATCH')
            <div class="show-modal-body">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="statusSelect" class="form-control" required>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="show-modal-footer">
                <button type="button" class="btn-edit" onclick="closeStatusModal()">Cancel</button>
                <button type="submit" class="btn-create">
                    <i class="bi bi-check-lg"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script>
(function () {
    'use strict';

    var fullDates = @json($clientFullDates ?? []);

    function disableFullDates(date) {
        var y = date.getFullYear();
        var m = String(date.getMonth() + 1).padStart(2, '0');
        var d = String(date.getDate()).padStart(2, '0');
        return fullDates.indexOf(y + '-' + m + '-' + d) !== -1;
    }

    var createDateInput = document.getElementById('createShowingDate');
    if (createDateInput && typeof window.flatpickr === 'function') {
        window.flatpickr('#createShowingDate', {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'm/d/Y',
            allowInput: false,
            disableMobile: true,
            minDate: 'today',
            disable: [disableFullDates]
        });
    }

    var editDateInput = document.getElementById('editShowingDate');
    var editDatePicker = null;
    if (editDateInput && typeof window.flatpickr === 'function') {
        editDatePicker = window.flatpickr('#editShowingDate', {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'm/d/Y',
            allowInput: false,
            disableMobile: true,
            minDate: 'today',
            disable: [disableFullDates]
        });
    }

    window.openCreateShowingModal = function () {
        var modal = document.getElementById('createShowingModal');
        if (modal) modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    };
    window.closeCreateShowingModal = function () {
        var modal = document.getElementById('createShowingModal');
        if (modal) modal.classList.remove('open');
        document.body.style.overflow = '';
    };

    window.openEditShowingModal = function (id, date, time, status) {
        var modal = document.getElementById('editShowingModal');
        var form = document.getElementById('editShowingForm');
        if (!modal || !form) return;
        form.action = '{{ url("/showings") }}/' + id;
        document.getElementById('editShowingTime').value = time;
        if (editDatePicker) {
            editDatePicker.setDate(date, false);
        } else if (editDateInput) {
            editDateInput.value = date;
        }
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    };
    window.closeEditShowingModal = function () {
        var modal = document.getElementById('editShowingModal');
        if (modal) modal.classList.remove('open');
        document.body.style.overflow = '';
    };

    window.openStatusModal = function (id, current) {
        var modal = document.getElementById('statusModal');
        var form = document.getElementById('statusForm');
        if (!modal || !form) return;
        form.action = '{{ url("/showings") }}/' + id + '/status';
        document.getElementById('statusSelect').value = current;
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    };
    window.closeStatusModal = function () {
        var modal = document.getElementById('statusModal');
        if (modal) modal.classList.remove('open');
        document.body.style.overflow = '';
    };

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeCreateShowingModal();
            closeEditShowingModal();
            closeStatusModal();
        }
    });
})();
</script>
@endpush