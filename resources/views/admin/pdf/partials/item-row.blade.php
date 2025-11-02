@php
    $pad = 'indent-'.min($level ?? 0, 3);
    $strike = ($item->is_excluded ?? false) ? 'strike' : '';
    $unitName = $item->unit?->name ? ' '.$item->unit->name : '';
@endphp

<tr>
    <td class="right">{{ $rowIndex }}</td>
    <td class="{{ $pad }} {{ $strike }}">
        @if($item->title)
            <strong>{{ $item->title }}</strong><br>
        @endif

        @if($item->description)
            <div class="muted">{!! $item->description !!}</div>
        @endif

        @if($item->details && $item->details->count())
            <div class="details">
                @foreach($item->details as $d)
                    • {{ $d->label }}: {{ $d->value }}<br>
                @endforeach
            </div>
        @endif
    </td>
    <td class="right {{ $strike }}">{{ $item->quantity ? $qty($item->quantity).$unitName : '' }}</td>
    <td class="right {{ $strike }}">{{ $item->unit_price !== null ? $money($item->unit_price) : '' }}</td>
    <td class="right {{ $strike }}">{{ $item->total_price !== null ? $money($item->total_price) : '' }}</td>
</tr>

{{-- Children --}}
@foreach($item->children as $child)
    @php $rowIndex++; @endphp
    @include('pdf.partials.item-row', [
        'item' => $child,
        'level' => ($level ?? 0) + 1,
        'rowIndex' => $rowIndex,
        'money' => $money,
        'qty' => $qty
    ])
@endforeach
