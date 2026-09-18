@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-book"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($entry) && $entry ? 'Edit Journal Entry' : 'New Journal Entry' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Finance</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.finance.journal-entries.index') }}">Journal Entries</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($entry) && $entry ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.finance.journal-entries.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($entry) && $entry ? route('admin.finance.journal-entries.update', $entry) : route('admin.finance.journal-entries.store') }}" method="POST" id="je-form">
                @csrf
                @if(isset($entry) && $entry) @method('PUT') @endif

                <div class="m-section-divider">Entry Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Entry Date <span class="text-danger">*</span></label>
                        <input type="date" name="entry_date" class="form-control" value="{{ old('entry_date', isset($entry) && $entry ? $entry->entry_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            @foreach(['general' => 'General', 'sales' => 'Sales', 'purchase' => 'Purchase', 'receipt' => 'Receipt', 'payment' => 'Payment', 'contra' => 'Contra', 'adjusting' => 'Adjusting'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type', $entry?->type ?? 'general') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Financial Year</label>
                        <select name="financial_year_id" class="form-select">
                            <option value="">-- None --</option>
                            @foreach($financialYears as $fy)
                            <option value="{{ $fy->id }}" {{ old('financial_year_id', $entry?->financial_year_id) == $fy->id ? 'selected' : '' }}>{{ $fy->name ?? $fy->start_date . ' to ' . $fy->end_date }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="2" required>{{ old('description', $entry?->description) }}</textarea>
                    </div>
                </div>

                <div class="m-section-divider">Journal Lines</div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="lines-table">
                        <thead>
                            <tr>
                                <th style="width:35%">Account <span class="text-danger">*</span></th>
                                <th style="width:15%">Debit</th>
                                <th style="width:15%">Credit</th>
                                <th style="width:25%">Description</th>
                                <th style="width:10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($entry) && $entry && $entry->lines->count() > 0)
                                @foreach($entry->lines as $line)
                                <tr class="line-row">
                                    <td>
                                        <select name="lines[{{ $loop->index }}][account_id]" class="form-select" required>
                                            <option value="">-- Select Account --</option>
                                            @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}" {{ $line->account_id == $acc->id ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="lines[{{ $loop->index }}][debit]" class="form-control debit-input" step="0.01" min="0" value="{{ $line->debit }}"></td>
                                    <td><input type="number" name="lines[{{ $loop->index }}][credit]" class="form-control credit-input" step="0.01" min="0" value="{{ $line->credit }}"></td>
                                    <td><input type="text" name="lines[{{ $loop->index }}][description]" class="form-control" value="{{ $line->description }}"></td>
                                    <td><button type="button" class="btn btn-outline-danger btn-sm btn-remove-line"><i class="bx bx-trash"></i></button></td>
                                </tr>
                                @endforeach
                            @else
                                <tr class="line-row">
                                    <td>
                                        <select name="lines[0][account_id]" class="form-select" required>
                                            <option value="">-- Select Account --</option>
                                            @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="lines[0][debit]" class="form-control debit-input" step="0.01" min="0" value="0"></td>
                                    <td><input type="number" name="lines[0][credit]" class="form-control credit-input" step="0.01" min="0" value="0"></td>
                                    <td><input type="text" name="lines[0][description]" class="form-control"></td>
                                    <td><button type="button" class="btn btn-outline-danger btn-sm btn-remove-line"><i class="bx bx-trash"></i></button></td>
                                </tr>
                                <tr class="line-row">
                                    <td>
                                        <select name="lines[1][account_id]" class="form-select" required>
                                            <option value="">-- Select Account --</option>
                                            @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="lines[1][debit]" class="form-control debit-input" step="0.01" min="0" value="0"></td>
                                    <td><input type="number" name="lines[1][credit]" class="form-control credit-input" step="0.01" min="0" value="0"></td>
                                    <td><input type="text" name="lines[1][description]" class="form-control"></td>
                                    <td><button type="button" class="btn btn-outline-danger btn-sm btn-remove-line"><i class="bx bx-trash"></i></button></td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <button type="button" class="btn btn-primary btn-sm" id="add-line"><i class="bx bx-plus"></i> Add Line</button>
                                </td>
                            </tr>
                            <tr class="table-active">
                                <td><strong>Total</strong></td>
                                <td><strong id="total-debit">0.00</strong></td>
                                <td><strong id="total-credit">0.00</strong></td>
                                <td colspan="2"><span id="balance-status" class="text-success"></span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary" id="btn-submit"><i class="bx bx-check me-1"></i> {{ isset($entry) && $entry ? 'Update' : 'Create' }} Journal Entry</button>
                    <a href="{{ route('admin.finance.journal-entries.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var lineIndex = {{ isset($entry) && $entry ? $entry->lines->count() : 2 }};

$('#add-line').on('click', function() {
    var options = '<option value="">-- Select Account --</option>';
    @foreach($accounts as $acc)
    options += '<option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>';
    @endforeach
    var row = '<tr class="line-row">' +
        '<td><select name="lines[' + lineIndex + '][account_id]" class="form-select" required>' + options + '</select></td>' +
        '<td><input type="number" name="lines[' + lineIndex + '][debit]" class="form-control debit-input" step="0.01" min="0" value="0"></td>' +
        '<td><input type="number" name="lines[' + lineIndex + '][credit]" class="form-control credit-input" step="0.01" min="0" value="0"></td>' +
        '<td><input type="text" name="lines[' + lineIndex + '][description]" class="form-control"></td>' +
        '<td><button type="button" class="btn btn-outline-danger btn-sm btn-remove-line"><i class="bx bx-trash"></i></button></td>' +
        '</tr>';
    $('#lines-table tbody').append(row);
    lineIndex++;
});

$(document).on('click', '.btn-remove-line', function() {
    if ($('#lines-table tbody tr.line-row').length > 2) {
        $(this).closest('tr').remove();
        reIndexLines();
        calculateTotals();
    }
});

$(document).on('input', '.debit-input, .credit-input', function() { calculateTotals(); });

function calculateTotals() {
    var totalDebit = 0, totalCredit = 0;
    $('.debit-input').each(function() { totalDebit += parseFloat($(this).val()) || 0; });
    $('.credit-input').each(function() { totalCredit += parseFloat($(this).val()) || 0; });
    $('#total-debit').text(totalDebit.toFixed(2));
    $('#total-credit').text(totalCredit.toFixed(2));
    if (Math.abs(totalDebit - totalCredit) < 0.01 && totalDebit > 0) {
        $('#balance-status').html('<i class="bx bx-check-circle text-success"></i> Balanced').removeClass('text-danger').addClass('text-success');
        $('#btn-submit').prop('disabled', false);
    } else {
        $('#balance-status').html('<i class="bx bx-error text-danger"></i> Unbalanced').removeClass('text-success').addClass('text-danger');
    }
}

function reIndexLines() {
    var idx = 0;
    $('#lines-table tbody tr.line-row').each(function() {
        $(this).find('select, input').each(function() {
            var name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace(/lines\[\d+\]/, 'lines[' + idx + ']'));
            }
        });
        idx++;
    });
    lineIndex = idx;
}

calculateTotals();
</script>
@endsection
