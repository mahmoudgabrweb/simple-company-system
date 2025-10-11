<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>عرض رقم {{ $quotation->quotation_number ?? $quotation->id }}</title>
    <style>
        @page {
            margin: 24px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #111;
        }

        h1, h2, h3, h4 {
            margin: 0 0 8px;
        }

        .muted {
            color: #666;
        }

        .row {
            width: 100%;
        }

        .col-6 {
            width: 49%;
            display: inline-block;
            vertical-align: top;
        }

        .mb-1 {
            margin-bottom: 6px;
        }

        .mb-2 {
            margin-bottom: 10px;
        }

        .mb-3 {
            margin-bottom: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        th {
            background: #f5f5f5;
        }

        .totals {
            width: 45%;
            margin-left: auto;
        }
    </style>
</head>
<body>
<h2>عرض سعر</h2>
<div class="row mb-2">
    <div class="col-6">
        <div><strong>المشروع:</strong> {{ $quotation->project->name ?? '—' }}</div>
        <div><strong>رقم العرض:</strong> {{ $quotation->quotation_number ?? $quotation->id }}</div>
        <div><strong>النسخة:</strong> {{ $quotation->version }}</div>
        @if($quotation->valid_until)
            <div><strong>صالح حتى:</strong> {{ $quotation->valid_until->format('Y-m-d') }}</div>
        @endif
    </div>
    <div class="col-6">
        <div><strong>الحالة:</strong> {{ $quotation->status }}</div>
        @if($quotation->sent_at)
            <div><strong>تاريخ الإرسال:</strong> {{ $quotation->sent_at->format('Y-m-d H:i') }}</div>
        @endif
        @if($quotation->approved_at)
            <div><strong>تاريخ الموافقة:</strong> {{ $quotation->approved_at->format('Y-m-d H:i') }}</div>
        @endif
    </div>
</div>

@foreach($quotation->sections as $section)
    <h3 class="mb-1">{{ $section->letter_code ? $section->letter_code.'. ' : '' }}{{ $section->title }}</h3>
    @if($section->description)
        <div class="mb-1">{!! $section->description !!}</div>
    @endif
    <table class="mb-3">
        <thead>
        <tr>
            <th style="width:5%">#</th>
            <th>الوصف</th>
            <th style="width:12%">الكمية</th>
            <th style="width:12%">الوحدة</th>
            <th style="width:15%">سعر الوحدة/المقطوع</th>
            <th style="width:15%">الإجمالي</th>
        </tr>
        </thead>
        <tbody>
        @php $i=1; @endphp
        @foreach($section->allItems()->where('is_excluded', false)->get() as $item)
            <tr>
                <td>{{ $i++ }}</td>
                <td>
                    <strong>{{ $item->title }}</strong>
                    @if($item->description)
                        <div class="muted">{!! $item->description !!}</div>
                    @endif
                </td>
                <td>{{ $item->quantity ?? '—' }}</td>
                <td>{{ optional($item->unit)->code ?? '—' }}</td>
                <td>{{ number_format($item->unit_price ?? 0, 2) }}</td>
                <td>{{ number_format($item->total_price, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endforeach

<table class="totals">
    <tr>
        <th>الإجمالي</th>
        <td>{{ number_format($quotation->total_amount,2) }}</td>
    </tr>
</table>

@if($quotation->notes)
    <div class="mb-2"><strong>ملاحظات:</strong></div>
    <div>{!! $quotation->notes !!}</div>
@endif
</body>
</html>
