@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $company ? 'Edit' : 'Create' }} Company</h5>
        </div>
        <div class="card-body">
            <form action="{{ $company ? route('admin.company.update', $company) : route('admin.company.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if($company) @method('PUT') @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Company Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $company->name ?? '') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo" class="form-control">
                        @if(!empty($company->logo))
                            <img src="{{ asset($company->logo) }}" class="mt-2" style="max-height:60px;">
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $company->email ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Website</label>
                        <input type="url" name="website" class="form-control" value="{{ old('website', $company->website ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Currency</label>
                        <select name="currency_id" class="form-select">
                            <option value="">Select Currency</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ ($company->currency_id ?? '') == $currency->id ? 'selected' : '' }}>
                                    {{ $currency->name }} ({{ $currency->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Timezone</label>
                        <input type="text" name="timezone" class="form-control" value="{{ old('timezone', $company->timezone ?? '') }}" placeholder="Asia/Kolkata">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $company->address ?? '') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $company->city ?? '') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $company->state ?? '') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', $company->country ?? '') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Zipcode</label>
                        <input type="text" name="zipcode" class="form-control" value="{{ old('zipcode', $company->zipcode ?? '') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">GSTIN</label>
                        <input type="text" name="gstin" class="form-control" value="{{ old('gstin', $company->gstin ?? '') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">PAN</label>
                        <input type="text" name="pan" class="form-control" value="{{ old('pan', $company->pan ?? '') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ ($company->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ ($company->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('admin.company.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
