@extends('admin.main')
@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Transaction #{{ $txn->id }}</h4>
            <a href="{{ route('voyager.bank_transactions.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                <div class="card p-3">
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Date</div>
                        <div class="col-8">{{ $txn->txn_date->format('Y-m-d') }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Type</div>
                        <div class="col-8 text-capitalize">{{ str_replace('_',' ',$txn->type) }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Amount</div>
                        <div class="col-8">{{ number_format($txn->amount,2) }} {{ $txn->currency }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Exchange Rate</div>
                        <div class="col-8">{{ $txn->exchange_rate }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Account</div>
                        <div class="col-8">{{ $txn->account_label ?: ($txn->bank_name.' '.$txn->account_number) }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Counterparty</div>
                        <div class="col-8">{{ $txn->counterparty ?: '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Reference</div>
                        <div class="col-8">{{ $txn->reference ?: '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Reconciled</div>
                        <div class="col-8">{{ $txn->reconciled ? 'Yes' : 'No' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-4 text-muted">Description</div>
                        <div class="col-8">{{ $txn->description ?: '-' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <div class="text-muted mb-1">Attachment</div>
                    @if($txn->attachment_path)
                        <a class="btn btn-outline-primary" target="_blank"
                           href="{{ asset('storage/'.$txn->attachment_path) }}">Open</a>
                    @else
                        <div class="text-muted">No attachment</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
