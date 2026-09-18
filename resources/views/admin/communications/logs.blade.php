@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-list-ul"></i></div>
            <div>
                <h4 class="m-page-title">Communication Logs</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.communications.index') }}">Communications</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Logs</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.communications.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>
    <div class="card">
        <div class="card-body">
            <table id="logs-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Channel</th><th>Recipient</th><th>Subject</th><th>Status</th><th>Error</th><th>Date</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$('#logs-table').DataTable({
    processing: true, serverSide: true, ajax: "{{ route('admin.communications.logs-data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'channel_badge', name: 'channel', orderable: false, searchable: false },
        { data: 'recipient', name: 'recipient' },
        { data: 'subject', name: 'subject' },
        { data: 'status_badge', name: 'status', orderable: false, searchable: false },
        { data: 'error_message', name: 'error_message' },
        { data: 'formatted_date', name: 'created_at', orderable: false, searchable: false }
    ], order: [[0, 'desc']]
});
</script>
@endsection
