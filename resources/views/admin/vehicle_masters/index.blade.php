@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Vehicle Master</h4>
        <a href="{{ route('admin.vehicle-masters.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> New</a>
    </div>
    @include('admin.layouts.elements.sweet_alerts')
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
@section('script')
<script>
$(function(){
    $('.delete-btn').click(function(){
        if(!confirm('Delete this vehicle?'))return;
        var form=$('#deleteForm'),url=$(this).data('url');
        form.attr('action',url);$.post(url,form.serialize()).done(function(r){if(r.success)location.reload();}).fail(function(){alert('Error');});
    });
    $('.toggle-status').change(function(){
        $.post($(this).data('url'),{_token:'{{ csrf_token() }}'}).fail(function(){alert('Error toggling status');});
    });
});
</script>
@endsection
@endsection
