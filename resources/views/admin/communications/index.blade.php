@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-message-rounded"></i></div>
            <div>
                <h4 class="m-page-title">Communication Templates</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Communications</li>
                </ul>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.communications.logs') }}" class="btn btn-outline-primary"><i class="bx bx-list-ul"></i> Logs</a>
            <a href="{{ route('admin.communications.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add Template</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills mb-3">
                <li class="nav-item"><a class="nav-link active" data-channel="" href="#">All</a></li>
                <li class="nav-item"><a class="nav-link" data-channel="email" href="#"><i class="bx bx-envelope"></i> Email</a></li>
                <li class="nav-item"><a class="nav-link" data-channel="sms" href="#"><i class="bx bx-message"></i> SMS</a></li>
                <li class="nav-item"><a class="nav-link" data-channel="whatsapp" href="#"><i class="bxl bxl-whatsapp"></i> WhatsApp</a></li>
            </ul>
            <table id="templates-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Name</th><th>Channel</th><th>Event</th><th>Subject</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
var channelFilter = '';
$('.nav-link[data-channel]').on('click', function(e) {
    e.preventDefault(); $('.nav-link[data-channel]').removeClass('active'); $(this).addClass('active');
    channelFilter = $(this).data('channel'); table.ajax.reload();
});
var table = $('#templates-table').DataTable({
    processing: true, serverSide: true,
    ajax: { url: "{{ route('admin.communications.data') }}", data: function(d) { d.channel = channelFilter; } },
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'channel_badge', name: 'channel', orderable: false, searchable: false },
        { data: 'event', name: 'event' },
        { data: 'subject', name: 'subject' },
        { data: 'status_badge', name: 'is_active', orderable: false, searchable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ], order: [[0, 'desc']]
});
</script>
@endsection
