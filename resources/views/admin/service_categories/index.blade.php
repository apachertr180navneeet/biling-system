@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Service Categories</h4>
        <a href="{{ route('admin.service-categories.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> New</a>
    </div>
        <div class="card">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>#</th><th>Name</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($categories as $c)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $c->name }}</td>
                        <td><label class="switch switch-success"><input type="checkbox" class="toggle-status" data-url="{{ route('admin.service-categories.toggle-status', $c) }}" {{ $c->is_active ? 'checked' : '' }}><span class="slider round"></span></label></td>
                        <td>
                            <a href="{{ route('admin.service-categories.edit', $c) }}" class="btn btn-sm btn-primary"><i class="bx bx-edit"></i></a>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $c->id }}" data-url="{{ route('admin.service-categories.destroy', $c) }}"><i class="bx bx-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted">No categories.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $categories->links() }}</div>
    </div>
</div>
<form id="deleteForm" method="POST">@csrf</form>
@section('script')
<script>
$(function(){
    $('.delete-btn').click(function(){
        var form=$('#deleteForm'),url=$(this).data('url');
        Swal.fire({
            title: 'Are you sure?',
            text: "Delete this category?",
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
});
</script>
@endsection
@endsection
