<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>Quotation #{{ $quotation->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
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
    </style>
</head>
<body>
<h2>عرض سعر #{{ $quotation->id }}</h2>
<p>المشروع: {{ $quotation->project->name ?? '' }}</p>
<p>الحالة: {{ $quotation->status }}</p>
<hr>
<table>
    <thead>
    <tr>
        <th>#</th>
        <th>العنصر</th>
        <th>الوحدة</th>
        <th>الكمية</th>
        <th>سعر الوحدة</th>
        <th>المبلغ</th>
    </tr>
    </thead>
    <tbody>
    @foreach($quotation->items as $i => $it)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{!! $it->title !!}</td>
            <td>{{ optional($it->unit)->code }}</td>
            <td>{{ $it->quantity }}</td>
            <td>{{ number_format($it->unit_price ?? $it->lump_sum, 2) }}</td>
            <td>{{ number_format($it->line_total, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h4 style="text-align:right;margin-top:10px">
    الإجمالي: {{ number_format($quotation->grand_total, 2) }}
</h4>
</body>
</html>
