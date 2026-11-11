@extends('layouts.app')
@section('title', 'Qualification')

@section('content')
<h1 class="page-title">
    <span>
        @if($qualification->user)
            {{ $qualification->user->name }}
        @else
            {{ $qualification->full_name ?: 'Walk-in' }}
        @endif
    </span>
    @if($qualification->isWalkIn())
        <span class="status-badge warning" style="font-size:0.7rem;vertical-align:middle;margin-left:0.5rem;">Walk-in</span>
    @endif
</h1>
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        @if($isAdmin)
        <a href="{{ route('qualifications.pdf', $qualification) }}" class="btn-edit" data-no-loader>
            <i class="bi bi-file-earmark-pdf"></i> Download PDF
        </a>
        @endif
        <a href="{{ route('qualifications.edit', $qualification) }}" class="btn-create">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('qualifications.index') }}" class="btn-edit">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

@if(session('success'))
<div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:0.75rem 1rem;border-radius:4px;margin-bottom:1.5rem;font-size:0.85rem;">
    {{ session('success') }}
</div>
@endif

{{-- Meta --}}
<div style="display:flex;gap:2rem;flex-wrap:wrap;margin-bottom:1.5rem;padding:1.25rem;border:1px solid var(--border);border-radius:6px;background:var(--surface);">
<div>
    <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--brand-muted);font-weight:600;margin-bottom:0.25rem;">Applicant</div>
    @if($qualification->user)
        <div style="font-size:0.9rem;font-weight:500;">{{ $qualification->user->name }}</div>
        <div style="font-size:0.75rem;color:var(--brand-muted);">{{ $qualification->user->email }}</div>
    @else
        <div style="font-size:0.9rem;font-weight:500;">{{ $qualification->full_name ?: '—' }}</div>
        <div style="font-size:0.75rem;color:var(--brand-muted);">{{ $qualification->data['email'] ?? '—' }}</div>
    @endif
</div>
    <div>
        <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--brand-muted);font-weight:600;margin-bottom:0.25rem;">Last edited</div>
        <div style="font-size:0.9rem;font-weight:500;">{{ $qualification->updated_at->format('M j, Y · g:i A') }}</div>
        <div style="font-size:0.75rem;color:var(--brand-muted);">{{ $qualification->updated_at->diffForHumans() }}</div>
    </div>
    <div>
        <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--brand-muted);font-weight:600;margin-bottom:0.25rem;">Last editor</div>
        <div style="font-size:0.9rem;font-weight:500;">{{ $qualification->lastEditor?->name ?? '—' }}</div>
        @if($qualification->lastEditor)
        <div style="font-size:0.75rem;color:var(--brand-muted);">{{ $qualification->lastEditor->email }}</div>
        @endif
    </div>
    <div>
        <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--brand-muted);font-weight:600;margin-bottom:0.25rem;">Type</div>
        <div style="font-size:0.9rem;font-weight:500;">
            @if($qualification->isVoucher())
            <span style="display:inline-block;padding:0.15rem 0.6rem;border-radius:9999px;font-size:0.7rem;font-weight:600;background:#dbeafe;color:#1e40af;">
                Voucher
            </span>
            @else
            <span style="display:inline-block;padding:0.15rem 0.6rem;border-radius:9999px;font-size:0.7rem;font-weight:600;background:#f3f4f6;color:#4b5563;">
                Standard
            </span>
            @endif
        </div>
    </div>
    @if($qualification->submitted_at)
    <div>
        <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--brand-muted);font-weight:600;margin-bottom:0.25rem;">Submitted</div>
        <div style="font-size:0.9rem;font-weight:500;">{{ $qualification->submitted_at->format('M j, Y') }}</div>
    </div>
    @endif
</div>

{{-- Secciones --}}
@foreach($schema as $pageKey => $page)
@php
$visibleFields = [];
foreach ($page['fields'] ?? [] as $k => $f) {
$val = $qualification->data[$k] ?? null;
if ($val === null || $val === '' || $val === []) continue;
if (is_bool($val) && $val === false) continue;
$visibleFields[$k] = $val;
}
@endphp

@if(!empty($visibleFields))
<div class="card-custom" style="margin-bottom:1.25rem;">
    <div class="card-header-inner">
        <i class="bi bi-file-text"></i>
        <span>{{ $page['title'] }}</span>
    </div>
    <div style="padding:1.5rem;">
        <dl style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.25rem 2rem;margin:0;">
            @foreach($visibleFields as $k => $val)
            @php $f = $page['fields'][$k] ?? null; @endphp
            @if($f)
            <div>
                <dt style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.05em;color:var(--brand-muted);font-weight:600;margin-bottom:0.35rem;">
                    {{ $f['label'] }}
                </dt>
                <dd style="font-size:0.88rem;font-weight:500;line-height:1.5;margin:0;color:var(--brand-primary);">
                    @if(is_array($val))
                    {{ implode(', ', $val) }}
                    @elseif(is_bool($val))
                    {{ $val ? 'Yes' : 'No' }}
                    @else
                    {{ $val }}
                    @endif
                </dd>
            </div>
            @endif
            @endforeach
        </dl>
    </div>
</div>
@endif
@endforeach

@endsection