@extends('layouts.app')
@section('title', 'Application')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <span>{{ $application->full_name ?: $application->user->name }}</span>
    </h1>
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        @if($isAdmin)
        <a href="{{ route('applications.pdf', $application) }}" class="btn-edit" data-no-loader>
            <i class="bi bi-file-earmark-pdf"></i> Download PDF
        </a>
        @endif
        <a href="{{ route('applications.edit', $application) }}" class="btn-create">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('applications.index') }}" class="btn-edit">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

@if(session('success'))
<div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:0.75rem 1rem;border-radius:4px;margin-bottom:1.5rem;font-size:0.85rem;">
    {{ session('success') }}
</div>
<script>
    try {
        const key = 'app_draft_' + {
            {
                auth() - > id()
            }
        } + '_' + ({
            {
                $application - > id ?? 'null'
            }
        } || 'new');
        localStorage.removeItem(key);
    } catch (e) {}
</script>
@endif

<div style="display:flex;gap:2rem;flex-wrap:wrap;margin-bottom:1.5rem;padding:1.25rem;border:1px solid var(--border);border-radius:6px;background:var(--surface);">
    <div>
        <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--brand-muted);font-weight:600;margin-bottom:0.25rem;">Applicant</div>
        <div style="font-size:0.9rem;font-weight:500;">{{ $application->user->name }}</div>
        <div style="font-size:0.75rem;color:var(--brand-muted);">{{ $application->user->email }}</div>
    </div>
    <div>
        <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--brand-muted);font-weight:600;margin-bottom:0.25rem;">Last edited</div>
        <div style="font-size:0.9rem;font-weight:500;">{{ $application->updated_at->format('M j, Y · g:i A') }}</div>
        <div style="font-size:0.75rem;color:var(--brand-muted);">{{ $application->updated_at->diffForHumans() }}</div>
    </div>
    <div>
        <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--brand-muted);font-weight:600;margin-bottom:0.25rem;">Last editor</div>
        <div style="font-size:0.9rem;font-weight:500;">{{ $application->lastEditor?->name ?? '—' }}</div>
        @if($application->lastEditor)
        <div style="font-size:0.75rem;color:var(--brand-muted);">{{ $application->lastEditor->email }}</div>
        @endif
    </div>
    @if($application->submitted_at)
    <div>
        <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--brand-muted);font-weight:600;margin-bottom:0.25rem;">Submitted</div>
        <div style="font-size:0.9rem;font-weight:500;">{{ $application->submitted_at->format('M j, Y') }}</div>
    </div>
    @endif
</div>

@foreach($schema as $pageKey => $page)
@php
$visibleFields = [];
foreach ($page['fields'] ?? [] as $k => $f) {
$val = $application->data[$k] ?? null;
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
                    @elseif($f['type'] === 'single_check' && isset($f['options'][$val]))
                    {{ $f['options'][$val] }}
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