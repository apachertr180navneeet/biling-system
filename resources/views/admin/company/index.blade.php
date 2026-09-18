@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Companies</h5>
            <a href="{{ route('admin.company.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Company
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered data-table w-100">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>Currency</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function() {
    $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.company.data") }}',
        columns: [
            { data: 'name' },
            { data: 'email', defaultContent: '-' },
            { data: 'phone', defaultContent: '-' },
            { data: 'city', defaultContent: '-' },
            { data: 'currency_name' },
            { data: 'status_badge', orderable: false, searchable: false },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>
@endsection
