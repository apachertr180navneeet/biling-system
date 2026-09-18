@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-language"></i></div>
            <div>
                <h4 class="m-page-title">Languages</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Languages</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.languages.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add Language</a>
    </div>
    <div class="card">
        <div class="card-body">
            <table id="lang-table" class="table table-bordered table-striped" style="width:100%">
                <thead><tr><th>#</th><th>Name</th><th>Code</th><th>Native Name</th><th>Direction</th><th>Translations</th><th>Default</th><th>Status</th><th>Actions</th></tr></thead>
            </table>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$('#lang-table').DataTable({
    processing: true, serverSide: true, ajax: "{{ route('admin.languages.data') }}",
    columns: [
        { data: 'id', name: 'id', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'code', name: 'code' },
        { data: 'native_name', name: 'native_name' },
        { data: 'direction_badge', name: 'direction', orderable: false, searchable: false },
        { data: 'translations_count', name: 'translations_count' },
        { data: 'default_badge', name: 'is_default', orderable: false, searchable: false },
        { data: 'status_badge', name: 'status', orderable: false, searchable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ], order: [[0, 'desc']]
});
</script>
@endsection
