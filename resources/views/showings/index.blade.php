@extends('layouts.app')
@section('title', 'My Showings')

@section('content')
<div class="page-header">
    <h1 class="page-title">My <span>Showings</span></h1>
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

<div class="card-custom">
    <div class="card-header-inner">
        <i class="bi bi-calendar-event"></i>
        <span style="font-size:.875rem;font-weight:600;">Scheduled showings</span>
        <span class="badge-count">{{ $showings->total() }}</span>
    </div>

    @if($showings->isEmpty())
        <div class="empty-state">
            <i class="bi bi-calendar-x"></i>
            <p>You haven't scheduled any showings yet.</p>
            <a href="{{ route('apartments.index') }}" class="btn-create" style="margin-top:1rem;">
                <i class="bi bi-house"></i> Browse apartments
            </a>
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
                            <div class="user-cell">
                                @if($showing->property?->primaryImage)
                                    <img src="{{ $showing->property->primaryImage->image_url }}"
                                         alt=""
                                         style="width:40px;height:40px;border-radius:6px;object-fit:cover;flex-shrink:0;">
                                @else
                                    <div style="width:40px;height:40px;border-radius:6px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#d1d5db;">
                                        <i class="bi bi-house"></i>
                                    </div>
                                @endif
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
                                        onclick="openEditShowing({{ $showing->id }}, '{{ $showing->showing_date->format('Y-m-d') }}', '{{ $showing->showing_time }}')">
                                    <i class="bi bi-pencil"></i>
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

        <div style="padding:1rem 1.25rem;">{{ $showings->links() }}</div>
    @endif
</div>

{{-- Edit Modal --}}
<div id="editShowingModal" style="display:none;position:fixed;inset:0;z-index:99999;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);padding:1.5rem;">
    <div style="background:white;border-radius:12px;max-width:420px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,0.2);overflow:hidden;">
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-family:var(--font-display);font-size:1.05rem;font-weight:700;margin:0;">Edit showing</h3>
            <button type="button" onclick="closeEditShowing()" style="background:none;border:none;font-size:1.25rem;cursor:pointer;color:var(--brand-muted);">&times;</button>
        </div>
        <form method="POST" id="editShowingForm">
            @csrf
            @method('PUT')
            <div style="padding:1.5rem;">
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" name="showing_date" id="edit_showing_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Time</label>
                    <select name="showing_time" id="edit_showing_time" class="form-control" required>
                        @foreach(['8:00 AM','9:00 AM','10:00 AM','11:00 AM','12:00 PM','1:00 PM','2:00 PM','3:00 PM','4:00 PM','5:00 PM','6:00 PM','7:00 PM','8:00 PM'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="padding:1rem 1.5rem;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:0.5rem;background:var(--surface);">
                <button type="button" class="btn-edit" onclick="closeEditShowing()">Cancel</button>
                <button type="submit" class="btn-create">
                    <i class="bi bi-check-lg"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditShowing(id, date, time) {
        const modal = document.getElementById('editShowingModal');
        const form = document.getElementById('editShowingForm');
        form.action = '{{ url("/showings") }}/' + id;
        document.getElementById('edit_showing_date').value = date;
        document.getElementById('edit_showing_time').value = time;
        modal.style.display = 'flex';
    }
    function closeEditShowing() {
        document.getElementById('editShowingModal').style.display = 'none';
    }
    document.getElementById('editShowingModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeEditShowing();
    });
</script>
@endpush