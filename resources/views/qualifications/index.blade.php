@extends('layouts.app')
@section('title', 'Qualifications')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<style>
    .qf-filters {
        padding: 1.25rem;
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 0.85rem;
        align-items: end;
    }

    .qf-filters .form-group {
        margin: 0;
    }

    .qf-filters .form-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--brand-muted);
        font-weight: 600;
        margin-bottom: 0.35rem;
    }

    .qf-filters .form-control {
        font-size: 0.83rem;
        padding: 0.55rem 0.75rem;
        min-height: 40px;
    }

    .qf-filters select.form-control {
        padding-right: 2.25rem;
    }

    .qf-filters .btn-group {
        display: flex;
        gap: 0.4rem;
    }

    .date-input {
        cursor: pointer;
        background: white;
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

    @media (max-width: 900px) {
        .qf-filters {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 600px) {
        .qf-filters {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">
        @if($isAdmin) All <span>Qualifications</span>
        @else My <span>Qualification</span>
        @endif
    </h1>

    @if($isAdmin)
    <a href="{{ route('qualifications.create') }}" class="btn-create">
        <i class="bi bi-plus-lg"></i> New Qualification
    </a>
    @endif
</div>

@if(session('success'))
<div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:0.75rem 1rem;border-radius:4px;margin-bottom:1.5rem;font-size:0.85rem;">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:0.75rem 1rem;border-radius:4px;margin-bottom:1.5rem;font-size:0.85rem;">
    {{ session('error') }}
</div>
@endif

@if($isAdmin)
<div class="card-custom" style="margin-bottom:1.5rem;">
    <div class="card-header-inner">
        <i class="bi bi-funnel"></i>
        <span>Filters</span>
    </div>
    <form method="GET" action="{{ route('qualifications.index') }}">
        <div class="qf-filters">
            <div class="form-group">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control"
                    value="{{ request('search') }}"
                    placeholder="Name, email, phone...">
            </div>

            <div class="form-group">
                <label class="form-label">Type</label>
                <select name="type" class="form-control">
                    <option value="">All</option>
                    <option value="voucher" @selected(request('type')==='voucher' )>Voucher</option>
                    <option value="standard" @selected(request('type')==='standard' )>Standard</option>
                    <option value="walkin" @selected(request('type')==='walkin' )>Walk-in</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">From</label>
                <input type="text" name="date_from" class="form-control date-input"
                    value="{{ request('date_from') }}"
                    placeholder="MM/DD/YYYY">
            </div>

            <div class="form-group">
                <label class="form-label">To</label>
                <input type="text" name="date_to" class="form-control date-input"
                    value="{{ request('date_to') }}"
                    placeholder="MM/DD/YYYY">
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-create" style="padding:0.55rem 0.95rem;font-size:0.8rem;">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('qualifications.index') }}" class="btn-edit" style="padding:0.55rem 0.8rem;font-size:0.8rem;">
                    Clear
                </a>
            </div>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="card-header-inner">
        <i class="bi bi-clipboard-check"></i>
        <span>Qualifications</span>
        <span class="badge-count">{{ $qualifications->total() }}</span>
    </div>

    @if($qualifications->isEmpty())
    <div class="empty-state">
        <i class="bi bi-clipboard"></i>
        <p>No qualifications found.</p>
    </div>
    @else
    <table class="table">
        <thead>
            <tr>
                <th>Applicant</th>
                <th>Email</th>
                <th>Type</th>
                <th>Last edited by</th>
                <th>Last update</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($qualifications as $q)
            <tr>
                <td>
                    <strong>
                        @if($q->user)
                        {{ $q->user->name }}
                        @else
                        {{ $q->full_name ?: 'Walk-in' }}
                        @endif
                    </strong>
                    @if($q->isWalkIn())
                    <span class="status-badge warning" style="font-size:0.6rem;margin-left:0.4rem;">Walk-in</span>
                    @endif
                </td>
                <td>
                    <span style="color:var(--brand-muted);">
                        @if($q->user)
                        {{ $q->user->email }}
                        @else
                        {{ $q->data['email'] ?? '—' }}
                        @endif
                    </span>
                </td>
                <td>
                    @if($q->isVoucher())
                    <span class="status-badge info">Voucher</span>
                    @else
                    <span class="status-badge neutral">Standard</span>
                    @endif
                </td>
                <td>{{ $q->lastEditor?->name ?? '—' }}</td>
                <td><span style="color:var(--brand-muted);font-size:0.8rem;">{{ $q->updated_at->diffForHumans() }}</span></td>
                <td class="text-end">
                    <div class="actions-cell justify-content-end">
                        <a href="{{ route('qualifications.show', $q) }}" class="btn-icon-action" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('qualifications.edit', $q) }}" class="btn-icon-action" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="{{ route('qualifications.pdf', $q) }}" class="btn-icon-action" title="Download PDF" data-no-loader>
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="padding:1rem 1.25rem;">{{ $qualifications->links() }}</div>
    @endif
</div>
@else
@if(!$qualification)
<div class="card-custom">
    <div class="empty-state" style="padding:4rem 2rem;">
        <i class="bi bi-clipboard-plus" style="font-size:3rem;color:#d1d5db;display:block;margin-bottom:1rem;"></i>
        <h3 style="font-family:var(--font-display);font-size:1.1rem;font-weight:600;margin-bottom:0.5rem;">You haven't filled out your qualification yet</h3>
        <p style="font-size:0.85rem;color:var(--brand-muted);margin-bottom:1.25rem;">
            Complete the form once to be considered for showings.
        </p>
        <a href="{{ route('qualifications.create') }}" class="btn-create">
            <i class="bi bi-pencil-square"></i> Start Qualification
        </a>
    </div>
</div>
@else
<div class="card-custom">
    <div class="card-header-inner">
        <i class="bi bi-clipboard-check"></i>
        <span>Rental Qualification</span>
        @if($qualification->isVoucher())
        <span class="status-badge info">Voucher</span>
        @else
        <span class="status-badge neutral">Standard</span>
        @endif
    </div>

    <div style="padding:1.5rem;">
        <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid var(--border);">
            <div>
                <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--brand-muted);font-weight:600;margin-bottom:0.25rem;">Last edited</div>
                <div style="font-size:0.9rem;font-weight:500;">{{ $qualification->updated_at->format('M j, Y · g:i A') }}</div>
                <div style="font-size:0.75rem;color:var(--brand-muted);">{{ $qualification->updated_at->diffForHumans() }}</div>
            </div>
            <div>
                <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--brand-muted);font-weight:600;margin-bottom:0.25rem;">Last editor</div>
                <div style="font-size:0.9rem;font-weight:500;">{{ $qualification->lastEditor?->name ?? 'You' }}</div>
            </div>
        </div>

        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            <a href="{{ route('qualifications.show', $qualification) }}" class="btn-edit">
                <i class="bi bi-eye"></i> View details
            </a>
            <a href="{{ route('qualifications.edit', $qualification) }}" class="btn-create">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('qualifications.pdf', $qualification) }}" class="btn-edit" data-no-loader>
                <i class="bi bi-download"></i> Download PDF
            </a>
        </div>
    </div>
</div>
@endif
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script>
    document.querySelectorAll('.date-input').forEach(function(el) {
        if (el._flatpickr) return;
        window.flatpickr(el, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'm/d/Y',
            allowInput: false,
            disableMobile: true,
        });
    });
</script>
@endpush