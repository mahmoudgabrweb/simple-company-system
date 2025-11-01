@php($t = $txn)
<div class="row g-3">

    <div class="col-md-3">
        <label class="form-label">Date</label>
        <input type="date" name="txn_date" class="form-control"
               value="{{ old('txn_date', optional($t?->txn_date)->format('Y-m-d')) }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label">Type</label>
        <select name="type" class="form-select" required>
            @foreach(['deposit','withdrawal','transfer_in','transfer_out','fee','interest'] as $opt)
                <option value="{{ $opt }}" @selected(old('type', $t->type ?? '')===$opt)>{{ ucfirst(str_replace('_',' ', $opt)) }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Amount</label>
        <input type="number" step="0.01" min="0.01" name="amount" class="form-control"
               value="{{ old('amount', $t->amount ?? '') }}" required>
    </div>

    <div class="col-md-1">
        <label class="form-label">Currency</label>
        <input type="text" name="currency" class="form-control" maxlength="3"
               value="{{ old('currency', $t->currency ?? 'AED') }}">
    </div>

    <div class="col-md-2">
        <label class="form-label">Exchange Rate</label>
        <input type="number" step="0.0001" min="0" name="exchange_rate" class="form-control"
               value="{{ old('exchange_rate', $t->exchange_rate ?? 1) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Bank Name</label>
        <input type="text" name="bank_name" class="form-control"
               value="{{ old('bank_name', $t->bank_name ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Account Number</label>
        <input type="text" name="account_number" class="form-control"
               value="{{ old('account_number', $t->account_number ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Account Label</label>
        <input type="text" name="account_label" class="form-control" placeholder="e.g., Main AED"
               value="{{ old('account_label', $t->account_label ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Counterparty</label>
        <input type="text" name="counterparty" class="form-control"
               value="{{ old('counterparty', $t->counterparty ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Reference</label>
        <input type="text" name="reference" class="form-control"
               value="{{ old('reference', $t->reference ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Attachment</label>
        <input type="file" name="attachment" class="form-control">
        @if(!empty($t?->attachment_path))
            <small class="text-muted d-block mt-1">Current: <a href="{{ asset('storage/'.$t->attachment_path) }}"
                                                               target="_blank">Open</a></small>
        @endif
    </div>

    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"
                  rows="3">{{ old('description', $t->description ?? '') }}</textarea>
    </div>

    <div class="col-md-3">
        <label class="form-label d-block">Reconciled</label>
        <input type="hidden" name="reconciled" value="0">
        <input type="checkbox" name="reconciled" value="1" @checked(old('reconciled', $t->reconciled ?? false))>
    </div>

</div>
