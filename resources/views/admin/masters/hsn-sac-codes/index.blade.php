@extends('admin.layouts.app')

@section('title', 'HSN / SAC Codes - ' . config('app.name'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-barcode"></i></div>
            <div>
                <h4 class="m-page-title">HSN / SAC Codes</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">HSN / SAC Codes</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">HSN / SAC Code Master</h5>
            @if(auth()->user()->hasPermission('taxes.create'))
            <button class="btn btn-primary btn-sm" onclick="openForm()">
                <i class="bx bx-plus me-1"></i>Add New
            </button>
            @endif
        </div>
        <div class="card-body">
            <table class="table table-striped" id="hsnTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>GST Rate</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="hsnForm">
                @csrf
                <input type="hidden" name="id" id="form_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="formTitle">Add HSN/SAC Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" id="form_code" required placeholder="e.g. 996311">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="type" id="form_type" required>
                                <option value="sac">SAC (Services)</option>
                                <option value="hsn">HSN (Goods)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="form_status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name / Description <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="form_name" required placeholder="e.g. Accommodation Services">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">GST Rate (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="gst_rate" id="form_gst_rate" required step="0.01" min="0" max="100" placeholder="18">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Cess Rate (%)</label>
                            <input type="number" class="form-control" name="cess_rate" id="form_cess_rate" step="0.01" min="0" max="100" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="form_description" rows="2" placeholder="Optional description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="formSubmitBtn">
                        <i class="bx bx-save me-1"></i>Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var table;
$(function() {
    table = $('#hsnTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.finance.hsn-sac-codes.data") }}',
        columns: [
            { data: 'id', name: 'id', orderable: false, searchable: false },
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'type_label', name: 'type', orderable: false, searchable: false },
            { data: 'gst_rate_label', name: 'gst_rate', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        language: { processing: '<div class="spinner-border spinner-border-sm text-primary"></div> Loading...' }
    });
});

function openForm(data) {
    $('#form_id').val('');
    $('#form_code').val('');
    $('#form_name').val('');
    $('#form_type').val('sac');
    $('#form_gst_rate').val('');
    $('#form_cess_rate').val('0');
    $('#form_description').val('');
    $('#form_status').val('active');
    $('#formTitle').text('Add HSN/SAC Code');
    $('#formSubmitBtn').html('<i class="bx bx-save me-1"></i>Save');
    new bootstrap.Modal('#formModal').show();
}

function editRow(id, code, name, type, gst_rate, cess_rate, description, status) {
    $('#form_id').val(id);
    $('#form_code').val(code);
    $('#form_name').val(name);
    $('#form_type').val(type);
    $('#form_gst_rate').val(gst_rate);
    $('#form_cess_rate').val(cess_rate || 0);
    $('#form_description').val(description || '');
    $('#form_status').val(status);
    $('#formTitle').text('Edit HSN/SAC Code');
    $('#formSubmitBtn').html('<i class="bx bx-save me-1"></i>Update');
    new bootstrap.Modal('#formModal').show();
}

$('#hsnForm').on('submit', function(e) {
    e.preventDefault();
    var id = $('#form_id').val();
    var url = id ? '{{ route("admin.finance.hsn-sac-codes.store") }}' : '{{ route("admin.finance.hsn-sac-codes.store") }}';
    $.ajax({
        url: url,
        method: 'POST',
        data: $(this).serialize(),
        success: function(r) {
            if (r.success) {
                bootstrap.Modal.getInstance(document.getElementById('formModal')).hide();
                table.ajax.reload();
                Swal.fire({ icon: 'success', title: 'Success', text: r.message, timer: 1500, showConfirmButton: false });
            }
        },
        error: function(xhr) {
            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Something went wrong.' });
        }
    });
});

function deleteRow(id) {
    Swal.fire({
        title: 'Delete?', text: 'This HSN/SAC code will be permanently deleted.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Yes, delete'
    }).then(function(r) {
        if (r.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.finance.hsn-sac-codes.destroy", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    table.ajax.reload();
                    Swal.fire({ icon: 'success', title: 'Deleted', text: res.message, timer: 1500, showConfirmButton: false });
                }
            });
        }
    });
}

function toggleStatus(id) {
    $.ajax({
        url: '{{ route("admin.finance.hsn-sac-codes.status", ":id") }}'.replace(':id', id),
        method: 'PATCH',
        data: { _token: '{{ csrf_token() }}' },
        success: function(r) {
            if (r.success) {
                table.ajax.reload(null, false);
                Swal.fire({ icon: 'success', title: 'Success', text: r.message, timer: 1500, showConfirmButton: false });
            }
        },
        error: function(xhr) {
            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Something went wrong.' });
        }
    });
}
</script>
@endsection
