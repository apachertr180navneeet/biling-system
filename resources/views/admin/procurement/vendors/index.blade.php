@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-user"></i></div>
            <div>
                <h4 class="m-page-title">Vendors</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="#">Procurement</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Vendors</li>
                </ul>
            </div>
        </div>
        @if(auth()->user()->hasPermission('vendors.create'))
        <a href="{{ route('admin.procurement.vendors.create') }}" class="btn btn-primary m-btn-add"><i class="bx bx-plus"></i> Add Vendor</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <table id="vendor-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Hotel</th><th>Company</th><th>Contact</th><th>Phone</th><th>Category</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function vendorStatusBadge(status, statusUrl) {
    @if(auth()->user()->hasPermission('vendors.edit'))
    return '<button type="button" class="btn btn-sm btn-status-toggle btn-' + (status === 'active' ? 'success' : (status === 'blacklisted' ? 'danger' : 'warning')) + '" data-url="' + statusUrl + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</button>';
    @else
    return '<span class="badge bg-label-' + (status === 'active' ? 'success' : (status === 'blacklisted' ? 'danger' : 'warning')) + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    @endif
}

function vendorTypeBadge(type) {
    var colors = { material: 'primary', service: 'info', both: 'success' };
    return '<span class="badge bg-label-' + (colors[type] || 'secondary') + '">' + type.charAt(0).toUpperCase() + type.slice(1) + '</span>';
}

function vendorActions(row) {
    var html = '<div class="btn-group btn-group-sm">';
    @if(auth()->user()->hasPermission('vendors.view'))
    html += '<a href="' + row.edit_url.replace('/edit', '') + '" class="btn btn-outline-info"><i class="bx bx-show"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('vendors.edit'))
    html += '<a href="' + row.edit_url + '" class="btn btn-outline-primary"><i class="bx bx-edit"></i></a>';
    @endif
    @if(auth()->user()->hasPermission('vendors.delete'))
    html += '<button type="button" class="btn btn-outline-danger btn-delete" data-url="' + row.delete_url + '" data-name="' + row.company_name + '"><i class="bx bx-trash"></i></button>';
    @endif
    html += '</div>';
    return html;
}

var table = $('#vendor-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.procurement.vendors.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'hotel_name', name: 'hotel.name' },
        { data: 'company_name', name: 'company_name' },
        { data: 'contact_person', name: 'contact_person' },
        { data: 'phone', name: 'phone' },
        { data: 'category_name', name: 'category.name' },
        { data: 'vendor_type', name: 'vendor_type', orderable: false, searchable: false, render: function(data) {
            return vendorTypeBadge(data);
        }},
        { data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
            return vendorStatusBadge(data, row.status_url);
        }},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, render: function(data, type, row) {
            return vendorActions(row);
        }}
    ],
    order: [[0, 'desc']]
});

$(document).on('click', '.btn-status-toggle', function() {
    var btn = $(this);
    $.ajax({ url: btn.data('url'), type: 'PATCH', headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(res) { btn.text(res.status.charAt(0).toUpperCase() + res.status.slice(1)); }
    });
});

$(document).on('click', '.btn-delete', function() {
    var btn = $(this);
    if (confirm('Are you sure you want to delete "' + btn.data('name') + '"?')) {
        $.ajax({ url: btn.data('url'), type: 'DELETE', headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() { table.ajax.reload(); }
        });
    }
});
</script>
@endsection
