@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-bell"></i></div>
            <div>
                <h4 class="m-page-title">Notifications</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Notifications</li>
                </ul>
            </div>
        </div>
        <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-primary"><i class="bx bx-check-double"></i> Mark All Read</button>
        </form>
    </div>
    <div class="card">
        <div class="card-body">
            <table id="notif-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Title</th><th>Message</th><th>Type</th><th>Status</th><th>Date</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$('#notif-table').DataTable({
    processing: true, serverSide: true, ajax: "{{ route('admin.notifications.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'title', name: 'title' },
        { data: 'message', name: 'message' },
        { data: 'type_badge', name: 'type', orderable: false, searchable: false },
        { data: 'status_badge', name: 'is_read', orderable: false, searchable: false },
        { data: 'formatted_date', name: 'created_at', orderable: false, searchable: false }
    ], order: [[0, 'desc']]
});
</script>
@endsection
