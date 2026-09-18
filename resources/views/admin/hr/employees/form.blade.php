@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-user"></i></div>
            <div>
                <h4 class="m-page-title">{{ isset($employee) ? 'Edit Employee' : 'Create Employee' }}</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">HR</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.hr.employees.index') }}">Employees</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">{{ isset($employee) ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.hr.employees.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($employee) ? route('admin.hr.employees.update', $employee) : route('admin.hr.employees.store') }}" method="POST">
                @csrf
                @if(isset($employee)) @method('PUT') @endif

                <div class="m-section-divider">Personal Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $employee?->first_name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $employee?->last_name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $employee?->email) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee?->phone) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $employee?->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $employee?->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $employee?->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $employee?->date_of_birth?->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="m-section-divider">Employment Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Department <span class="text-danger">*</span></label>
                        <select name="department_id" class="form-select" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $employee?->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Designation <span class="text-danger">*</span></label>
                        <select name="designation_id" class="form-select" required>
                            <option value="">Select Designation</option>
                            @foreach($designations as $desg)
                            <option value="{{ $desg->id }}" {{ old('designation_id', $employee?->designation_id) == $desg->id ? 'selected' : '' }}>{{ $desg->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Branch</label>
                        <select name="branch_id" class="form-select">
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $employee?->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date of Joining <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_joining" class="form-control" value="{{ old('date_of_joining', $employee?->date_of_joining?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Basic Salary <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="basic_salary" class="form-control" value="{{ old('basic_salary', $employee?->basic_salary) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Employment Type</label>
                        <select name="employment_type" class="form-select">
                            <option value="full_time" {{ old('employment_type', $employee?->employment_type) == 'full_time' ? 'selected' : '' }}>Full Time</option>
                            <option value="part_time" {{ old('employment_type', $employee?->employment_type) == 'part_time' ? 'selected' : '' }}>Part Time</option>
                            <option value="contract" {{ old('employment_type', $employee?->employment_type) == 'contract' ? 'selected' : '' }}>Contract</option>
                            <option value="intern" {{ old('employment_type', $employee?->employment_type) == 'intern' ? 'selected' : '' }}>Intern</option>
                        </select>
                    </div>
                </div>

                <div class="m-section-divider">Bank Details</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $employee?->bank_name) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Account Number</label>
                        <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $employee?->bank_account_number) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">IFSC Code</label>
                        <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code', $employee?->ifsc_code) }}">
                    </div>
                </div>

                <div class="m-section-divider">Status</div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $employee?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $employee?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-actions mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> {{ isset($employee) ? 'Update' : 'Create' }} Employee</button>
                    <a href="{{ route('admin.hr.employees.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection