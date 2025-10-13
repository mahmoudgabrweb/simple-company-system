@extends('admin.main')

@section('content')
    <div class="container-xxl py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Financial Details — {{ $project->name }}</h4>
            <div>
                <a href="{{ route('voyager.projects.index') }}" class="btn btn-secondary">Back</a>
                <a href="{{ route('voyager.projects.edit', $project->id) }}" class="btn btn-warning">Edit Project</a>
            </div>
        </div>

        {{-- Summary cards --}}
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <div class="border rounded p-2 h-100">
                    <div class="text-muted small">Active Quotation Total</div>
                    <div class="fs-5 fw-bold">{{ number_format($activeQuotationTotal,2) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="border rounded p-2 h-100">
                    <div class="text-muted small">Total Paid</div>
                    <div class="fs-5 fw-bold">{{ number_format($totalPaid,2) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="border rounded p-2 h-100">
                    <div class="text-muted small">Total Expenses</div>
                    <div class="fs-5 fw-bold">{{ number_format($totalExpenses,2) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="border rounded p-2 h-100">
                    <div class="text-muted small">Remaining</div>
                    <div class="fs-5 fw-bold {{ $remaining < 0 ? 'text-danger' : '' }}">{{ number_format($remaining,2) }}</div>
                </div>
            </div>
        </div>

        {{-- Lists + Filters --}}
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="m-0">Payments</h5>
                        <a href="{{ route('voyager.payments.create') }}" class="btn btn-sm btn-primary">+ Payment</a>
                    </div>

                    <form method="get" class="row g-2 mb-2">
                        <input type="hidden" name="expenses_page" value="{{ request('expenses_page') }}">
                        <div class="col-md-4">
                            <label class="form-label">Method</label>
                            <select name="method" class="form-select">
                                <option value="">— All —</option>
                                @foreach($methodsMap as $k=>$v)
                                    <option value="{{ $k }}" @selected(request('method')===$k)>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label d-block">&nbsp;</label>
                            <button class="btn btn-secondary w-100">Filter</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Receiver</th>
                                <th>Date</th>
                                <th>Reference</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($payments as $pay)
                                <tr>
                                    <td>{{ $pay->id }}</td>
                                    <td>{{ number_format($pay->amount,2) }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $methodsMap[$pay->payment_method] ?? $pay->payment_method }}</span>
                                    </td>
                                    <td>{{ $pay->receiver->name ?? '—' }}</td>
                                    <td>{{ $pay->paid_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $pay->reference ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No payments.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2">{{ $payments->links() }}</div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="m-0">Project Expenses</h5>
                        <a href="{{ route('voyager.project_expenses.create') }}" class="btn btn-sm btn-primary">+
                            Expense</a>
                    </div>

                    <form method="get" class="row g-2 mb-2">
                        <input type="hidden" name="payments_page" value="{{ request('payments_page') }}">
                        <div class="col-md-4">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                <option value="">— All —</option>
                                @foreach($methodsMap as $k=>$v)
                                    <option value="{{ $k }}" @selected(request('type')===$k)>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label d-block">&nbsp;</label>
                            <button class="btn btn-secondary w-100">Filter</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Payer</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($expenses as $e)
                                <tr>
                                    <td>{{ $e->id }}</td>
                                    <td>{{ $e->title }}</td>
                                    <td>{{ number_format($e->amount,2) }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $methodsMap[$e->payment_type] ?? $e->payment_type }}</span>
                                    </td>
                                    <td>{{ $e->payer->name ?? '—' }}</td>
                                    <td>{{ $e->paid_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No expenses.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2">{{ $expenses->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
