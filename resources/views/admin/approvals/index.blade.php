@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-check-shield"></i></div>
            <div>
                <h4 class="m-page-title">Approval Workflow</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Approvals</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.approvals.rules') }}" class="btn btn-outline-primary"><i class="bx bx-cog"></i> Approval Rules</a>
    </div>
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills mb-3">
                <li class="nav-item"><a class="nav-link active" data-status="" href="#">All</a></li>
                <li class="nav-item"><a class="nav-link" data-status="pending" href="#">Pending</a></li>
                <li class="nav-item"><a class="nav-link" data-status="approved" href="#">Approved</a></li>
                <li class="nav-item"><a class="nav-link" data-status="rejected" href="#">Rejected</a></li>
            </ul>
            <table id="approval-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Module</th><th>Record ID</th><th>Requested By</th><th>Status</th><th>Remarks</th><th>Date</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
var statusFilter = '';
$('.nav-link[data-status]').on('click', function(e) {
    e.preventDefault(); $('.nav-link[data-status]').removeClass('active'); $(this).addClass('active');
    statusFilter = $(this).data('status'); table.ajax.reload();
});
var table = $('#approval-table').DataTable({
    processing: true, serverSide: true,
    ajax: { url: "{{ route('admin.approvals.data') }}", data: function(d) { d.status = statusFilter; } },
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'module_badge', name: 'module', orderable: false, searchable: false },
        { data: 'record_id', name: 'record_id' },
        { data: 'requester_name', name: 'requester_name' },
        { data: 'status_badge', name: 'status', orderable: false, searchable: false },
        { data: 'remarks', name: 'remarks' },
        { data: 'formatted_date', name: 'created_at', orderable: false, searchable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ], order: [[0, 'desc']]
});
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
$(document).on('click', '.btn-approve', function() {
    var url = $(this).data('url');
    Swal.fire({ title: 'Approve?', text: 'Approve this request?', icon: 'question', showCancelButton: true }).then(r => {
        if (r.isConfirmed) $.post(url, {}, function(res) { Swal.fire('Approved!', res.message, 'success').then(() => table.ajax.reload()); });
    });
});
$(document).on('click', '.btn-reject', function() {
    var url = $(this).data('url');
    Swal.fire({ title: 'Reject?', input: 'text', inputPlaceholder: 'Reason...', icon: 'warning', showCancelButton: true }).then(r => {
        if (r.isConfirmed) $.post(url, { remarks: r.value }, function(res) { Swal.fire('Rejected!', res.message, 'info').then(() => table.ajax.reload()); });
    });
});
</script>
@endsection
