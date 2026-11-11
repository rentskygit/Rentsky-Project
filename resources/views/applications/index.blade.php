@extends('layouts.app')
@section('title', 'Applications')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<style>
    .af-filters { padding: 1.25rem; display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 0.85rem; align-items: end; }
    .af-filters .form-group { margin: 0; }
    .af-filters .form-label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--brand-muted); font-weight: 600; margin-bottom: 0.35rem; }
    .af-filters .form-control { font-size: 0.83rem; padding: 0.55rem 0.75rem; min-height: 40px; }
    .af-filters select.form-control { padding-right: 2.25rem; }
    .af-filters .btn-group { display: flex; gap: 0.4rem; }
    .date-input { cursor: pointer; background: white; }
    .flatpickr-calendar { font-family: var(--font-sans); border-radius: 6px; box-shadow: 0 10px 30px rgba(0,0,0,0.12); z-index: 100; }
    .flatpickr-day.selected, .flatpickr-day.selected:hover { background: var(--brand-primary); border-color: var(--brand-primary); }
    .flatpickr-day.today { border-color: var(--brand-primary); }
    .flatpickr-day:hover { background: var(--surface-alt); }
    .flatpickr-months .flatpickr-month { color: var(--brand-primary); }
    @media (max-width: 900px) { .af-filters { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 600px) { .af-filters { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">
        @if($isAdmin) All <span>Applications</span>
        @else My <span>Application</span>
        @endif
    </h1>

    @if($isAdmin)
        <a href="{{ route('applications.create') }}" class="btn-create">
            <i class="bi bi-plus-lg"></i> New Application
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
        <form method="GET" action="{{ route('applications.index') }}">
            <div class="af-filters">
                <div class="form-group">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control"
                           value="{{ request('search') }}"
                           placeholder="Name, email, phone...">
                </div>

                <div class="form-group">
                    <label class="form-label">Applying as</label>
                    <select name="type" class="form-control">
                        <option value="">All</option>
                        <option value="tenant"    @selected(request('type') === 'tenant')>Tenant</option>
                        <option value="guarantor" @selected(request('type') === 'guarantor')>Guarantor</option>
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
                    <a href="{{ route('applications.index') }}" class="btn-edit" style="padding:0.55rem 0.8rem;font-size:0.8rem;">
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="card-custom">
        <div class="card-header-inner">
            <i class="bi bi-file-earmark-person"></i>
            <span>Applications</span>
            <span class="badge-count">{{ $applications->total() }}</span>
        </div>

        @if($applications->isEmpty())
            <div class="empty-state">
                <i class="bi bi-file-earmark"></i>
                <p>No applications found.</p>
            </div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Email</th>
                        <th>Applying as</th>
                        <th>Last edited by</th>
                        <th>Last update</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $a)
                        <tr>
                            <td><strong>{{ $a->full_name ?: $a->user->name }}</strong></td>
                            <td><span style="color:var(--brand-muted);">{{ $a->user->email }}</span></td>
                            <td>
                                @php $type = $a->data['applicant_type'] ?? null; @endphp
                                @if($type === 'tenant')
                                    <span class="status-badge info">Tenant</span>
                                @elseif($type === 'guarantor')
                                    <span class="status-badge warning">Guarantor</span>
                                @else
                                    <span style="color:var(--brand-muted);font-size:0.8rem;">—</span>
                                @endif
                            </td>
                            <td>{{ $a->lastEditor?->name ?? '—' }}</td>
                            <td><span style="color:var(--brand-muted);font-size:0.8rem;">{{ $a->updated_at->diffForHumans() }}</span></td>
                            <td class="text-end">
                                <div class="actions-cell justify-content-end">
                                    <a href="{{ route('applications.show', $a) }}" class="btn-icon-action" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('applications.edit', $a) }}" class="btn-icon-action" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('applications.pdf', $a) }}" class="btn-icon-action" title="Download PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="padding:1rem 1.25rem;">{{ $applications->links() }}</div>
        @endif
    </div>
@else
    @if(!$application)
        <div class="card-custom">
            <div class="empty-state" style="padding:4rem 2rem;">
                <i class="bi bi-file-earmark-plus" style="font-size:3rem;color:#d1d5db;display:block;margin-bottom:1rem;"></i>
                <h3 style="font-family:var(--font-display);font-size:1.1rem;font-weight:600;margin-bottom:0.5rem;">You haven't filled out your application yet</h3>
                <p style="font-size:0.85rem;color:var(--brand-muted);margin-bottom:1.25rem;">
                    Complete the rental application form once to be considered.
                </p>
                <a href="{{ route('applications.create') }}" class="btn-create">
                    <i class="bi bi-pencil-square"></i> Start Application
                </a>
            </div>
        </div>
    @else
        <div class="card-custom">
            <div class="card-header-inner">
                <i class="bi bi-file-earmark-person"></i>
                <span>Rental Application</span>
                @php $type = $application->data['applicant_type'] ?? null; @endphp
                @if($type === 'tenant')
                    <span class="status-badge info">Tenant</span>
                @elseif($type === 'guarantor')
                    <span class="status-badge warning">Guarantor</span>
                @endif
            </div>

            <div style="padding:1.5rem;">
                <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid var(--border);">
                    <div>
                        <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--brand-muted);font-weight:600;margin-bottom:0.25rem;">Last edited</div>
                        <div style="font-size:0.9rem;font-weight:500;">{{ $application->updated_at->format('M j, Y · g:i A') }}</div>
                        <div style="font-size:0.75rem;color:var(--brand-muted);">{{ $application->updated_at->diffForHumans() }}</div>
                    </div>
                    <div>
                        <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--brand-muted);font-weight:600;margin-bottom:0.25rem;">Last editor</div>
                        <div style="font-size:0.9rem;font-weight:500;">{{ $application->lastEditor?->name ?? 'You' }}</div>
                    </div>
                </div>

                <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                    <a href="{{ route('applications.show', $application) }}" class="btn-edit">
                        <i class="bi bi-eye"></i> View details
                    </a>
                    <a href="{{ route('applications.edit', $application) }}" class="btn-create">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('applications.pdf', $application) }}" class="btn-edit">
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
document.querySelectorAll('.date-input').forEach(function (el) {
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