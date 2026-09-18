@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">User Details</h5>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center mb-4">
                    @if($user->avatar)
                    <img src="{{ $user->avatar_full_path }}" alt="{{ $user->full_name }}" class="rounded-circle mb-2" width="120" height="120">
                    @else
                    <div class="rounded-circle bg-label-primary d-flex align-items-center justify-content-center mx-auto mb-2" style="width:120px;height:120px;">
                        <span class="fs-1 text-white">{{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}</span>
                    </div>
                    @endif
                    <h5>{{ $user->full_name }}</h5>
                    <span class="badge bg-label-{{ $user->status == 'active' ? 'success' : 'warning' }}">{{ ucfirst($user->status) }}</span>
                </div>
                <div class="col-md-9">
                    <table class="table table-bordered">
                        <tr><th width="200">Email</th><td>{{ $user->email }}</td></tr>
                        <tr><th>Phone</th><td>{{ $user->phone }}</td></tr>
                        <tr><th>Role</th><td><span class="badge bg-label-primary">{{ $user->roleDetail?->name ?? $user->role }}</span></td></tr>
                        <tr><th>City</th><td>{{ $user->city ?: '-' }}</td></tr>
                        <tr><th>State</th><td>{{ $user->state ?: '-' }}</td></tr>
                        <tr><th>Country</th><td>{{ $user->country ?: '-' }}</td></tr>
                        <tr><th>Created At</th><td>{{ $user->created_at->format('d-m-Y h:i A') }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
