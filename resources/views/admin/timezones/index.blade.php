@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Timezones</h5>
            <a href="{{ route('admin.timezones.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add Timezone
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered data-table w-100">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Label</th>
                        <th>Offset</th>
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
        ajax: '{{ route("admin.timezones.data") }}',
        columns: [
            { data: 'name' },
            { data: 'label', defaultContent: '-' },
            { data: 'offset', defaultContent: '-' },
            { data: 'status_badge', orderable: false, searchable: false },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>
@endsection
