@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Service Categories</h4>
        <a href="{{ route('admin.service-categories.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> New</a>
    </div>
    @include('admin.layouts.elements.sweet_alerts')
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
@push('scripts')
<script>
$(function(){
    $('.delete-btn').click(function(){
        if(!confirm('Delete this category?'))return;
        var form=$('#deleteForm'),url=$(this).data('url');
        form.attr('action',url);$.post(url,form.serialize()).done(function(r){if(r.success)location.reload();}).fail(function(){alert('Error');});
    });
    $('.toggle-status').change(function(){
        $.post($(this).data('url'),{_token:'{{ csrf_token() }}'}).fail(function(){alert('Error toggling status');});
    });
});
</script>
@endpush
@endsection
