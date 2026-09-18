@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-message-square-dots"></i></div>
            <div>
                <h4 class="m-page-title">Available Tasks</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('staff.dashboard') }}">Staff Portal</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Available Tasks</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt me-1"></i> Back</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($tasks->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bx bx-party" style="font-size: 50px;"></i>
                <h5 class="mt-3">All caught up!</h5>
                <p>No unassigned tasks at the moment</p>
            </div>
            @else
            @foreach($tasks as $task)
            <div class="card mb-3 {{ $task->priority === 'urgent' ? 'border-danger' : ($task->priority === 'high' ? 'border-warning' : '') }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $task->request_number }}</h6>
                            <p class="mb-1"><strong>{{ $task->subject }}</strong></p>
                            <p class="mb-1 text-muted">{{ $task->description ? Str::limit($task->description, 100) : 'No description' }}</p>
                            <small class="text-muted">
                                {{ ucfirst(str_replace('_', ' ', $task->category)) }} | Room {{ $task->room?->room_number ?? '-' }}
                                @if($task->guest) | Guest: {{ $task->guest->first_name }} {{ $task->guest->last_name }} @endif
                            </small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : ($task->priority === 'medium' ? 'info' : 'secondary')) }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                            <br>
                            <small class="text-muted">{{ $task->created_at->diffForHumans() }}</small>
                            <form action="{{ route('staff.claim-task', $task) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bx bx-check"></i> Claim Task
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
