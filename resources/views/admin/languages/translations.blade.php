@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-language"></i></div>
            <div>
                <h4 class="m-page-title">Translations - {{ $language->name }} ({{ strtoupper($language->code) }})</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.languages.index') }}">Languages</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Translations</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.languages.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Translations ({{ $translations->count() }})</h5></div>
                <div class="card-body">
                    @if($translations->isEmpty())
                        <p class="text-muted">No translations yet. Add your first translation below.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead><tr><th>Group</th><th>Key</th><th>Value</th><th></th></tr></thead>
                            <tbody>
                            @foreach($translations as $t)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $t->group }}</span></td>
                                <td><code>{{ $t->key }}</code></td>
                                <td>{{ $t->value }}</td>
                                <td>
                                    <form action="{{ route('admin.languages.destroy-translation', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Add Translation</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.languages.store-translation', $language) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Group</label>
                            <input type="text" name="group" class="form-control" value="messages" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Key</label>
                            <input type="text" name="key" class="form-control" required placeholder="e.g. welcome_message">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Value</label>
                            <textarea name="value" class="form-control" rows="3" required placeholder="Translated text..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="bx bx-plus"></i> Add Translation</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
