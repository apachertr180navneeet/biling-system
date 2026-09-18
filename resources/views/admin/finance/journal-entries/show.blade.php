@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-book"></i></div>
            <div>
                <h4 class="m-page-title">Journal Entry #{{ $entry->entry_number }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.finance.journal-entries.index') }}">Journal Entries</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">View</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.finance.journal-entries.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><strong>Entry Number:</strong><br>{{ $entry->entry_number }}</div>
                <div class="col-md-3"><strong>Date:</strong><br>{{ $entry->entry_date->format('d-m-Y') }}</div>
                <div class="col-md-3"><strong>Type:</strong><br><span class="badge bg-label-info">{{ ucfirst($entry->type) }}</span></div>
                <div class="col-md-3"><strong>Status:</strong><br>
                    @php $colors = ['draft' => 'warning', 'posted' => 'success', 'void' => 'danger']; @endphp
                    <span class="badge bg-label-{{ $colors[$entry->status] ?? 'secondary' }}">{{ ucfirst($entry->status) }}</span>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6"><strong>Description:</strong><br>{{ $entry->description }}</div>
                <div class="col-md-3"><strong>Created By:</strong><br>{{ $entry->creator?->name }}</div>
                <div class="col-md-3">
                    @if($entry->status === 'draft' && auth()->user()->hasPermission('journal_entries.edit'))
                    <form action="{{ route('admin.finance.journal-entries.post', $entry) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm"><i class="bx bx-check"></i> Post Entry</button>
                    </form>
                    @endif
                    @if($entry->status === 'posted' && auth()->user()->hasPermission('journal_entries.edit'))
                    <form action="{{ route('admin.finance.journal-entries.void', $entry) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Void this entry?')"><i class="bx bx-x"></i> Void Entry</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>Journal Lines</h5>
            <table class="table table-bordered table-striped">
                <thead><tr><th>Account</th><th>Description</th><th class="text-end">Debit</th><th class="text-end">Credit</th></tr></thead>
                <tbody>
                    @foreach($entry->lines as $line)
                    <tr>
                        <td>{{ $line->account?->code }} - {{ $line->account?->name }}</td>
                        <td>{{ $line->description }}</td>
                        <td class="text-end">{{ $line->debit > 0 ? number_format($line->debit, 2) : '' }}</td>
                        <td class="text-end">{{ $line->credit > 0 ? number_format($line->credit, 2) : '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <td colspan="2"><strong>Total</strong></td>
                        <td class="text-end"><strong>{{ number_format($entry->total_debit, 2) }}</strong></td>
                        <td class="text-end"><strong>{{ number_format($entry->total_credit, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
