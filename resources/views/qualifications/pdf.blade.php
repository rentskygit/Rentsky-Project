<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rental Qualification — {{ $qualification->user?->name ?? ($qualification->full_name ?: 'Walk-in') }}</title>
    <style>
        @page {
            margin: 12mm 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 7.5px;
            color: #1a1a1a;
            line-height: 1.3;
        }

        .brand-bar {
            background: #0a0a0a;
            color: #ffffff;
            padding: 10px 14px;
            border-radius: 4px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-bar .brand-name {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .brand-bar .brand-name span {
            color: #374fa7;
        }

        .brand-bar .doc-type {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            opacity: 0.85;
        }

        .title-block {
            margin-bottom: 8px;
        }

        .title-block h1 {
            font-size: 15px;
            font-weight: 700;
            color: #0a0a0a;
            margin-bottom: 2px;
        }

        .title-block .subtitle {
            font-size: 8px;
            color: #6b7280;
        }

        .meta-bar {
            display: flex;
            gap: 12px;
            padding: 6px 0;
            border-top: 2px solid #374fa7;
            border-bottom: 1px solid #ebebeb;
            margin-bottom: 10px;
            font-size: 7.5px;
        }

        .meta-bar .meta-item {
            flex: 1;
        }

        .meta-bar .meta-label {
            font-size: 6px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
            font-weight: 700;
            margin-bottom: 1px;
            display: block;
        }

        .meta-bar .meta-value {
            font-weight: 600;
            color: #0a0a0a;
            word-break: break-word;
        }

        .section {
            margin-bottom: 7px;
            page-break-inside: avoid;
        }

        .section-header {
            background: #fafafa;
            border-left: 3px solid #374fa7;
            padding: 4px 8px;
            margin-bottom: 4px;
        }

        .section-header h2 {
            font-size: 8.5px;
            font-weight: 700;
            color: #0a0a0a;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .section-body {
            padding: 0 2px;
        }

        .fields-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .field {
            display: table-cell;
            width: 50%;
            padding: 3px 8px 3px 0;
            vertical-align: top;
            border-bottom: 1px dotted #e5e7eb;
        }

        .field.full {
            display: block;
            width: 100%;
        }

        .field-label {
            font-size: 6.5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            font-weight: 700;
            margin-bottom: 1px;
            display: block;
        }

        .field-value {
            font-size: 8px;
            color: #0a0a0a;
            font-weight: 500;
            word-wrap: break-word;
            white-space: pre-line;
        }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 6px;
            font-size: 6.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-voucher {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-standard {
            background: #f3f4f6;
            color: #4b5563;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 4px 0;
            border-top: 1px solid #ebebeb;
            font-size: 6.5px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="brand-bar">
        <div class="brand-name">JCM <span>Realty Group</span></div>
        <div class="doc-type">Rental Qualification</div>
    </div>

    <div class="title-block">
        <h1>
            @if($qualification->user)
            {{ $qualification->user->name }}
            @else
            {{ $qualification->full_name ?: 'Walk-in' }}
            @endif
        </h1>
        <div class="subtitle">Submitted {{ $qualification->submitted_at ? $qualification->submitted_at->format('F j, Y') : $qualification->created_at->format('F j, Y') }}</div>
    </div>

    <div class="meta-bar">
<div class="meta-item">
    <span class="meta-label">Email</span>
    <span class="meta-value">
        @if($qualification->user)
            {{ $qualification->user->email }}
        @else
            {{ $qualification->data['email'] ?? '—' }}
        @endif
    </span>
</div>
        <div class="meta-item">
            <span class="meta-label">Type</span>
            <span class="meta-value">
                @if($qualification->isVoucher()) Voucher
                @else Standard
                @endif
            </span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Last edited</span>
            <span class="meta-value">{{ $qualification->updated_at->format('M j, Y g:i A') }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Last editor</span>
            <span class="meta-value">{{ $qualification->lastEditor?->name ?? '—' }}</span>
        </div>
    </div>

    @foreach($schema as $pageKey => $page)
    @php
    $visibleFields = [];
    foreach ($page['fields'] ?? [] as $k => $f) {
    if (in_array($f['type'] ?? '', ['heading', 'subheading'])) continue;
    $val = $qualification->data[$k] ?? null;
    if ($val === null || $val === '' || $val === []) continue;
    if (is_bool($val) && $val === false) continue;
    $visibleFields[$k] = ['field' => $f, 'value' => $val];
    }
    @endphp

    @if(!empty($visibleFields))
    <div class="section">
        <div class="section-header">
            <h2>{{ $page['title'] }}</h2>
        </div>
        <div class="section-body">
            <div class="fields-grid">
                @foreach($visibleFields as $k => $item)
                @php
                $field = $item['field'];
                $val = $item['value'];

                if (is_bool($val)) {
                $display = $val ? 'Yes' : 'No';
                } elseif ($field['type'] === 'single_check' && isset($field['options'][$val])) {
                $display = $field['options'][$val];
                } elseif (is_array($val)) {
                $display = implode(', ', $val);
                } else {
                $display = $val;
                }

                $isLong = strlen((string) $display) > 60 || in_array($field['type'], ['textarea']);
                @endphp
                <div class="field {{ $isLong ? 'full' : '' }}">
                    <span class="field-label">{{ $field['label'] }}</span>
                    <div class="field-value">{{ $display }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    @endforeach

    <div class="footer">
        JCM Realty Group · Confidential Rental Qualification · Generated {{ now()->format('F j, Y') }}
    </div>

</body>

</html>