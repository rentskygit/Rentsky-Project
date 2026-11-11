@extends('layouts.app')
@section('title', 'Clients')

@push('styles')
<style>
    .cf-filters {
        padding: 1.25rem;
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 0.85rem;
        align-items: end;
    }
    .cf-filters .form-group { margin: 0; }
    .cf-filters .form-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--brand-muted);
        font-weight: 600;
        margin-bottom: 0.35rem;
    }
    .cf-filters .form-control {
        font-size: 0.83rem;
        padding: 0.55rem 0.75rem;
        min-height: 40px;
    }
    .cf-filters select.form-control { padding-right: 2.25rem; }
    .cf-filters .btn-group { display: flex; gap: 0.4rem; }

    @media (max-width: 1024px) { .cf-filters { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 600px)  { .cf-filters { grid-template-columns: 1fr; } }

    .client-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }
    .client-modal-backdrop.open { display: flex; }

    .client-modal {
        background: white;
        border-radius: 12px;
        max-width: 480px;
        width: 100%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        animation: modalIn 0.2s ease;
    }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    .client-modal-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .client-modal-header h3 {
        font-family: var(--font-display);
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--brand-primary);
        margin: 0;
    }
    .client-modal-close {
        background: none;
        border: none;
        font-size: 1.25rem;
        color: var(--brand-muted);
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
    }
    .client-modal-close:hover {
        background: var(--surface);
        color: var(--brand-primary);
    }

    .client-modal-body { padding: 1.5rem; }
    .client-modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        background: var(--surface);
    }

    .client-modal-note {
        font-size: 0.8rem;
        color: var(--brand-muted);
        background: var(--surface);
        padding: 0.65rem 0.85rem;
        border-radius: 6px;
        margin-bottom: 1rem;
        border-left: 3px solid var(--brand-secondary);
        line-height: 1.5;
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <h1 class="page-title">Client <span>Management</span></h1>
</div>

<div class="card-custom" style="margin-bottom:1.5rem;">
    <div class="card-header-inner">
        <i class="bi bi-funnel"></i>
        <span>Filters</span>
    </div>
    <form method="GET" action="{{ route('clients.index') }}">
        <div class="cf-filters">
            <div class="form-group">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control"
                       value="{{ request('search') }}"
                       placeholder="Name or email...">
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All</option>
                    <option value="new"         @selected(request('status') === 'new')>New</option>
                    <option value="qualified"   @selected(request('status') === 'qualified')>Qualified</option>
                    <option value="showing"     @selected(request('status') === 'showing')>Showing</option>
                    <option value="application" @selected(request('status') === 'application')>Application</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Agent</label>
                <select name="agent" class="form-control">
                    <option value="">All</option>
                    <option value="unassigned" @selected(request('agent') === 'unassigned')>Unassigned</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" @selected(request('agent') == $agent->id)>
                            {{ $agent->name }}
                            @if(!empty($agent->agent_role_labels))
                                ({{ implode(', ', $agent->agent_role_labels) }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Property</label>
                <select name="property" class="form-control">
                    <option value="">All</option>
                    <option value="unassigned" @selected(request('property') === 'unassigned')>Unassigned</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}" @selected(request('property') == $property->id)>
                            {{ $property->address }}@if($property->unit_type) — {{ $property->unit_type }}@endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-create" style="padding:0.55rem 0.95rem;font-size:0.8rem;">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('clients.index') }}" class="btn-edit" style="padding:0.55rem 0.8rem;font-size:0.8rem;">
                    Clear
                </a>
            </div>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="card-header-inner">
        <i class="bi bi-people-fill text-muted"></i>
        <span style="font-size:.875rem;font-weight:600;">All clients</span>
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
                    <th>Status</th>
                    <th>Property of interest</th>
                    <th>Assigned agent</th>
                    @if(auth()->user()->isAdmin())
                        <th class="text-end">Actions</th>
                    @endif
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
                        <td>
                            <span class="user-email">{{ $client->email }}</span>
                        </td>
                        <td>
                            @if($client->formatted_phone)
                                <span style="font-size:0.83rem;">{{ $client->formatted_phone }}</span>
                            @else
                                <span style="color:var(--brand-muted);font-size:0.8rem;">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ $client->client_status_badge }}">
                                {{ $client->client_status_label }}
                            </span>
                        </td>
                        <td>
                            @if($client->propertyOfInterest)
                                <span style="font-size:0.83rem;">
                                    {{ $client->propertyOfInterest->address }}
                                    @if($client->propertyOfInterest->unit_type)
                                        — {{ $client->propertyOfInterest->unit_type }}
                                    @endif
                                </span>
                            @else
                                <span style="color:var(--brand-muted);font-size:0.8rem;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($client->assignedAdviser)
                                <div class="user-cell">
                                    <span class="avatar">{{ strtoupper(substr($client->assignedAdviser->name, 0, 1)) }}</span>
                                    <span class="user-name">{{ $client->assignedAdviser->name }}</span>
                                </div>
                            @else
                                <span style="color:var(--brand-muted);font-size:0.8rem;">Not assigned</span>
                            @endif
                        </td>
                        @if(auth()->user()->isAdmin())
                            <td class="text-end">
                                <div class="actions-cell justify-content-end">
                                    <button type="button"
                                            class="btn-icon-action"
                                            title="Assign"
                                            onclick="openAssignModal({{ $client->id }}, @js($client->name), {{ $client->assigned_adviser_id ?? 'null' }}, {{ $client->property_of_interest_id ?? 'null' }})">
                                        <i class="bi bi-person-plus"></i>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="padding:1rem 1.25rem;">{{ $clients->links() }}</div>
    @endif
</div>

@if(auth()->user()->isAdmin())
<div class="client-modal-backdrop" id="assignModal" onclick="if(event.target===this) closeAssignModal()">
    <div class="client-modal">
        <div class="client-modal-header">
            <h3>Assign client</h3>
            <button type="button" class="client-modal-close" onclick="closeAssignModal()" aria-label="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form method="POST" id="assignForm">
            @csrf
            @method('PUT')

            <div class="client-modal-body">
                <div class="client-modal-note">
                    <i class="bi bi-info-circle"></i>
                    Assigning an agent and/or property of interest to <strong id="assignClientName"></strong>.
                </div>

                <div class="form-group">
                    <label class="form-label" for="modal_adviser">Agent</label>
                    <select name="assigned_adviser_id" id="modal_adviser" class="form-control">
                        <option value="">— Not assigned —</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">
                                {{ $agent->name }} — {{ $agent->email }}
                                @if(!empty($agent->agent_role_labels))
                                    ({{ implode(', ', $agent->agent_role_labels) }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @if($agents->isEmpty())
                        <small class="text-muted" style="display:block;margin-top:0.35rem;">
                            No users with Admin, Super Admin, or Adviser role exist yet.
                        </small>
                    @endif
                </div>

                <div class="form-group">
                    <label class="form-label" for="modal_property">Property of interest</label>
                    <select name="property_of_interest_id" id="modal_property" class="form-control">
                        <option value="">— Not assigned —</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}">
                                {{ $property->address }}
                                @if($property->unit_type)
                                    — {{ $property->unit_type }}
                                @endif
                                @if($property->city)
                                    , {{ $property->city }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="client-modal-footer">
                <button type="button" class="btn-edit" onclick="closeAssignModal()">Cancel</button>
                <button type="submit" class="btn-create">
                    <i class="bi bi-check-lg"></i> Save assignment
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
@if(auth()->user()->isAdmin())
<script>
(function () {
    const modal = document.getElementById('assignModal');
    const form = document.getElementById('assignForm');
    const nameEl = document.getElementById('assignClientName');
    const adviserSelect = document.getElementById('modal_adviser');
    const propertySelect = document.getElementById('modal_property');

    window.openAssignModal = function (userId, userName, adviserId, propertyId) {
        if (!modal || !form) return;
        form.action = '{{ url("/clients") }}/' + userId + '/assign';
        nameEl.textContent = userName;
        adviserSelect.value = adviserId || '';
        propertySelect.value = propertyId || '';
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    };

    window.closeAssignModal = function () {
        if (!modal) return;
        modal.classList.remove('open');
        document.body.style.overflow = '';
    };

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal?.classList.contains('open')) {
            closeAssignModal();
        }
    });
})();
</script>
@endif
@endpush