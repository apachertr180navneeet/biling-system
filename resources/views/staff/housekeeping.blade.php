@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="m-page-header">
        <div class="m-page-header-left">
            <div class="m-page-icon"><i class="bx bx-cleaning-service"></i></div>
            <div>
                <h4 class="m-page-title">Housekeeping</h4>
                <ul class="m-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li><a href="{{ route('staff.dashboard') }}">Staff Portal</a></li>
                    <li class="m-breadcrumb-sep">/</li>
                    <li class="active">Housekeeping</li>
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

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Room Status Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($rooms as $room)
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <div class="card text-center {{ $room->roomStatus?->slug === 'clean' ? 'border-success' : ($room->roomStatus?->slug === 'dirty' ? 'border-danger' : 'border-warning') }}">
                                <div class="card-body p-2">
                                    <h6 class="mb-0">{{ $room->room_number }}</h6>
                                    <small class="text-muted">{{ $room->roomType?->name ?? '-' }}</small>
                                    <br>
                                    <span class="badge bg-{{ $room->roomStatus?->slug === 'clean' ? 'success' : ($room->roomStatus?->slug === 'dirty' ? 'danger' : 'warning') }} mt-1">
                                        {{ $room->roomStatus?->name ?? 'Unknown' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Housekeeping Requests</h5>
        </div>
        <div class="card-body">
            @if($housekeepingTasks->isEmpty())
            <div class="text-center py-4 text-muted">
                <i class="bx bx-check-circle" style="font-size: 40px;"></i>
                <p class="mt-2">No pending housekeeping requests</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Room</th>
                            <th>Guest</th>
                            <th>Subject</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($housekeepingTasks as $task)
                        <tr>
                            <td><small>{{ $task->request_number }}</small></td>
                            <td>{{ $task->room?->room_number ?? '-' }}</td>
                            <td>{{ $task->guest?->first_name ?? '-' }} {{ $task->guest?->last_name ?? '' }}</td>
                            <td>{{ $task->subject }}</td>
                            <td>
                                <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : 'info') }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'warning' : 'primary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('staff.task-detail', $task) }}" class="btn btn-sm btn-outline-primary">View</a>
                                @if(!$task->assigned_to)
                                <form action="{{ route('staff.claim-task', $task) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Claim</button>
                                </form>
                                @endif
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
@endsection
