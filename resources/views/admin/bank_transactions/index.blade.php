@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Bank Transactions</h4>
            <a href="{{ route('voyager.bank_transactions.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Transaction
            </a>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach</ul>
            </div>
        @endif

        {{-- Filters --}}
        <form method="GET" class="card p-3 mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All types</option>
                        @foreach(['deposit','withdrawal','transfer_in','transfer_out','fee','interest'] as $t)
                            <option value="{{ $t }}" @selected(request('type')===$t)>{{ ucfirst(str_replace('_',' ', $t)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-secondary flex-grow-1" type="submit">Filter</button>
                    @if(request()->hasAny(['from','to','type']))
                        <a href="{{ route('voyager.bank_transactions.index') }}" class="btn btn-light">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        {{-- List --}}
        <div class="card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th class="text-end">Amount</th>
                        <th>Currency</th>
                        <th>Account</th>
                        <th>Counterparty</th>
                        <th>Reference</th>
                        <th>Attachment</th>
                        <th>Reconciled</th>
                        <th style="width:190px"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($transactions as $t)
                        <tr>
                            <td>{{ \Illuminate\Support\Carbon::parse($t->txn_date)->format('Y-m-d') }}</td>
                            <td class="text-capitalize">{{ str_replace('_',' ', $t->type) }}</td>
                            <td class="text-end">{{ number_format((float)$t->amount, 2) }}</td>
                            <td>{{ $t->currency }}</td>
                            <td>{{ $t->account_label ?: trim(($t->bank_name ?? '').' '.($t->account_number ?? '')) ?: '—' }}</td>
                            <td>{{ $t->counterparty ?: '—' }}</td>
                            <td>{{ $t->reference ?: '—' }}</td>
                            <td>
                                @if($t->attachment_path)
                                    <a href="{{ route('voyager.bank_transactions.download',$t->id) }}"
                                       class="btn btn-sm btn-outline-primary">Download</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($t->reconciled)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                @can('edit', app(\App\Models\BankTransaction::class))
                                    <a href="{{ route('voyager.bank_transactions.edit', $t->id) }}"
                                       class="btn btn-sm btn-warning">Edit</a>
                                @endcan

                                @can('edit', app(\App\Models\BankTransaction::class))
                                    <form action="{{ route('voyager.bank_transactions.changeReconciled', $t->id) }}"
                                          method="post" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="reconciled" value="{{ $t->reconciled ? 0 : 1 }}">
                                        <button class="btn btn-sm btn-secondary" type="submit">
                                            {{ $t->reconciled ? 'Unreconcile' : 'Reconcile' }}
                                        </button>
                                    </form>
                                @endcan

                                @can('delete', app(\App\Models\BankTransaction::class))
                                    <form action="{{ route('voyager.bank_transactions.destroy', $t->id) }}"
                                          method="post" class="d-inline"
                                          onsubmit="return confirm('Delete this transaction?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No transactions found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
@endsection
