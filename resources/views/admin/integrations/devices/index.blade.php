@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-chip"></i></div>
            <div>
                <h4 class="m-page-title">Devices</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Integrations</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Devices</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('devices.create'))
        <a href="{{ route('admin.integrations.devices.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Device</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <ul class="nav nav-pills" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-filter="all" href="#">All</a></li>
                        <li class="nav-item"><a class="nav-link" data-filter="biometric" href="#"><i class="bx bx-fingerprint"></i> Biometric</a></li>
                        <li class="nav-item"><a class="nav-link" data-filter="printer" href="#"><i class="bx bx-printer"></i> Printers</a></li>
                        <li class="nav-item"><a class="nav-link" data-filter="smart_lock" href="#"><i class="bx bx-lock"></i> Smart Locks</a></li>
                    </ul>
                </div>
            </div>
            <table id="devices-table" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Brand</th>
                        <th>Serial No</th>
                        <th>Hotel</th>
                        <th>Status</th>
                        <th>Online</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var typeFilter = 'all';

$('.nav-link[data-filter]').on('click', function(e) {
    e.preventDefault();
    $('.nav-link[data-filter]').removeClass('active');
    $(this).addClass('active');
    typeFilter = $(this).data('filter');
    table.ajax.reload();
});

var table = $('#devices-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('admin.integrations.devices.data') }}",
        data: function(d) {
            d.type_filter = typeFilter;
        }
    },
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'type_badge', name: 'type', orderable: false, searchable: false },
        { data: 'brand', name: 'brand' },
        { data: 'serial_number', name: 'serial_number' },
        { data: 'hotel_name', name: 'hotel_name' },
        { data: 'status_badge', name: 'status', orderable: false, searchable: false },
        { data: 'online_indicator', name: 'last_seen_at', orderable: false, searchable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ],
    order: [[0, 'desc']]
});
</script>
@endsection
