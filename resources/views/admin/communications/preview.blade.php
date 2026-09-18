@extends('admin.layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-show"></i></div>
            <div>
                <h4 class="m-page-title">Template Preview</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('admin.communications.index') }}">Communications</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Preview</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('admin.communications.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ $template->name }} <span class="badge bg-info">{{ strtoupper($template->channel) }}</span></h5></div>
                <div class="card-body">
                    <h6>Subject: {{ $template->subject ?? '-' }}</h6>
                    <hr>
                    <div class="border p-3 bg-light" style="min-height:200px">{!! nl2br(e($renderedBody)) !!}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Template Info</h5></div>
                <div class="card-body">
                    <p><strong>Event:</strong> {{ $template->event }}</p>
                    <p><strong>Channel:</strong> {{ strtoupper($template->channel) }}</p>
                    <p><strong>Status:</strong> {{ $template->is_active ? 'Active' : 'Inactive' }}</p>
                    <p><strong>Variables:</strong></p>
                    <ul>
                        @foreach($sampleData as $key => $val)
                        <li><code>{{ '{{' . $key . '}}' }}</code> = {{ $val }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
