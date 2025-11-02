{{-- resources/views/pdf/quotation.blade.php --}}
@include('admin.pdf.partials.header')

@php
    $money = fn($v) => number_format((float)$v, 2);
    $qty   = fn($v) => $v === null ? '' : rtrim(rtrim(number_format((float)$v, 3, '.', ''), '0'), '.');
@endphp

<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        color: #111;
    }

    p {
        margin: 0 0 6px;
        line-height: 1.5;
    }

    ul, ol {
        margin: 6px 0 6px 18px;
    }

    .meta {
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e5e7eb;
    }

    .meta-grid {
        width: 100%;
        border-collapse: collapse;
        margin-top: 6px;
    }

    .meta-grid td {
        padding: 3px 6px;
        vertical-align: top;
    }

    .meta-grid .label {
        color: #6b7280;
        width: 140px;
        white-space: nowrap;
    }

    .section-header {
        margin: 14px 0 6px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    table.table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th, .table td {
        border: 1px solid #d1d5db;
        padding: 6px 8px;
    }

    .table th {
        background: #f3f4f6;
        font-weight: 700;
    }

    .right {
        text-align: right;
    }

    .muted {
        color: #6b7280;
    }

    .strike {
        text-decoration: line-through;
        color: #9ca3af;
    }

    .indent-0 {
        padding-left: 0;
    }

    .indent-1 {
        padding-left: 14px;
    }

    .indent-2 {
        padding-left: 28px;
    }

    .indent-3 {
        padding-left: 42px;
    }

    .details {
        font-size: 11px;
        color: #374151;
        margin-top: 3px;
    }

    .table tr {
        page-break-inside: avoid;
    }

    .totals {
        margin-top: 8px;
        width: 50%;
        margin-left: auto;
        border-collapse: collapse;
    }

    .totals td {
        padding: 6px 8px;
        border: 1px solid #d1d5db;
    }

    .totals tr:nth-child(odd) {
        background: #fafafa;
    }
</style>

<main>
    {{-- Meta --}}
    <div class="meta">
        <table class="meta-grid">
            <tr>
                <td class="label">TO:</td>
                <td>{{ $quotation->project?->client?->name ?? '-' }}</td>
                <td class="label">DATE:</td>
                <td>{{ optional($quotation->created_at)->format('m/d/Y') }}</td>
            </tr>
            <tr>
                <td class="label">LOCATION:</td>
                <td>{{ $quotation->project?->location ?? $quotation->project?->name ?? '-' }}</td>
                <td class="label">REF:</td>
                <td>{{ $quotation->quotation_number ?? ('Q-'.$quotation->id) }}</td>
            </tr>
            <tr>
                <td class="label">COMPANY:</td>
                <td>{{ $quotation->company?->name ?? ($branding['brand'] ?? '') }}</td>
                <td class="label">VERSION:</td>
                <td>{{ $quotation->version ?? '1' }}</td>
            </tr>
        </table>
    </div>

    {{-- Sections --}}
    @foreach($quotation->sections as $section)
        <div class="section-header">
            {{ trim(($section->letter_code ? $section->letter_code.' — ' : '').($section->title ?? '')) }}
        </div>

        <table class="table">
            <thead>
            <tr>
                <th style="width:60px">No</th>
                <th>Description</th>
                <th style="width:90px" class="right">Qty</th>
                <th style="width:110px" class="right">Unit Price</th>
                <th style="width:120px" class="right">Total</th>
            </tr>
            </thead>
            <tbody>
            @php $rowIndex = 1; @endphp
            @foreach($section->items as $item)
                @include('admin.pdf.partials.item-row', [
                  'item' => $item,
                  'level' => 0,
                  'rowIndex' => $rowIndex,
                  'money' => $money,
                  'qty' => $qty
                ])
                @php
                    // update rowIndex to whatever the last included partial advanced to
                    $rowIndex = $rowIndex + 1 + ($item->children->count() ? $item->children->count() : 0);
                @endphp
            @endforeach
            </tbody>
        </table>
    @endforeach

    {{-- Totals --}}
    @php
        $sub   = (float)($quotation->total_amount ?? 0);
        $vat   = round($sub * 0.05, 2);
        $grand = round($sub + $vat, 2);
    @endphp

    <table class="totals">
        <tr>
            <td>Sub Total (Excl. VAT)</td>
            <td class="right">{{ $money($sub) }}</td>
        </tr>
        <tr>
            <td>VAT (5%)</td>
            <td class="right">{{ $money($vat) }}</td>
        </tr>
        <tr>
            <td><strong>Total</strong></td>
            <td class="right"><strong>{{ $money($grand) }}</strong></td>
        </tr>
    </table>

    {{-- Notes --}}
    @if(!empty($quotation->notes))
        <div class="section-header">Notes</div>
        <div style="font-size:11px; line-height:1.5;">{!! $quotation->notes !!}</div>
    @endif
</main>
