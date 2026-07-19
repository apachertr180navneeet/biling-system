@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Vehicle Master</h4>
        <div>
            <a href="{{ route('admin.vehicle-masters.import-template') }}" class="btn btn-outline-secondary me-2"><i class="bx bx-download"></i> Template</a>
            <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#importModal"><i class="bx bx-upload"></i> Import</button>
            <a href="{{ route('admin.vehicle-masters.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> New</a>
        </div>
    </div>
    @include('admin.layouts.elements.sweet_alerts')

    @if(session('import_errors'))
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        <strong>Import completed with some skipped rows/warnings:</strong>
        <ul class="mb-0 mt-2" style="max-height: 200px; overflow-y: auto;">
            @foreach(session('import_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Variant</th>
                        <th>Color</th>
                        <th>Fuel</th>
                        <th>Transmission</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $v)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $v->variant_name ?? '-' }}</td>
                        <td>
                            @if($v->color_code)
                                <span class="badge" style="background-color:{{ $v->color_code }};color:#fff;">{{ $v->color_name ?? '-' }}</span>
                            @else
                                {{ $v->color_name ?? '-' }}
                            @endif
                        </td>
                        <td>{{ $v->fuel_type ?? '-' }}</td>
                        <td>{{ $v->transmission ?? '-' }}</td>
                        <td>{{ number_format($v->ex_showroom_price, 2) }}</td>
                        <td>
                            <label class="switch switch-success">
                                <input type="checkbox" class="toggle-status"
                                    data-url="{{ route('admin.vehicle-masters.toggle-status', $v) }}"
                                    {{ $v->is_active ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <a href="{{ route('admin.vehicle-masters.edit', $v) }}" class="btn btn-sm btn-primary"><i class="bx bx-edit"></i></a>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $v->id }}" data-url="{{ route('admin.vehicle-masters.destroy', $v) }}"><i class="bx bx-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted">No vehicle records.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $vehicles->links() }}</div>
    </div>
</div>
<form id="deleteForm" method="POST">@csrf</form>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.vehicle-masters.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Vehicle Masters</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Choose CSV File</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv,.txt,.xls,.xlsx" required>
                        <div class="form-text text-muted mt-2">
                            Please upload a valid CSV file using the template headers:<br>
                            <code>variant_name, color_name, fuel_type, transmission, ex_showroom_price</code>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>

@section('script')
<script>
$(function(){
    $('.delete-btn').click(function(){
        var form=$('#deleteForm'),url=$(this).data('url');
        Swal.fire({
            title: 'Are you sure?',
            text: "Delete this vehicle?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#8592a3',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.attr('action',url);
                $.post(url,form.serialize()).done(function(r){
                    if(r.success) location.reload();
                }).fail(function(){
                    Swal.fire('Error', 'Something went wrong!', 'error');
                });
            }
        });
    });
    $('.toggle-status').change(function(){
        $.post($(this).data('url'),{_token:'{{ csrf_token() }}'}).fail(function(){
            Swal.fire('Error', 'Error toggling status', 'error');
        });
    });
});
</script>
@endsection
@endsection
