@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">GSTR-1 Export</h4>
    @include('admin.layouts.elements.sweet_alerts')
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.reports.gstr1-export') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select @error('month') is-invalid @enderror">
                        <option value="">Select</option>
                        @foreach($months as $m)
                        <option value="{{ $m['value'] }}">{{ $m['label'] }}</option>
                        @endforeach
                    </select>
                    @error('month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select @error('year') is-invalid @enderror">
                        <option value="">Select</option>
                        @foreach($years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                    @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-download"></i> Export CSV</button>
                </div>
            </div>
        </form>
    </div></div>
</div>
@endsection
