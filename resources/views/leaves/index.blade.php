@extends('layouts.app')

@php
    $title = 'Employee Leave';
@endphp

@section('title', $title . 's')

@section('content')
  <div class="d-flex justify-content-between flex-md-nowrap align-items-center flex-wrap py-4">
    <div class="d-block mb-md-0 mb-4">
      <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
          <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">
              <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
            </a>
          </li>
          <li class="breadcrumb-item"><a href="{{ route('leave.index') }}">{{ $title }}s</a></li>
        </ol>
      </nav>
      <h2 class="h4">All {{ $title }}s</h2>

      <form method="GET" action="{{ route('leave.index') }}" class="d-flex mb-3">
        <input type="text" name="search" class="form-control" placeholder="Search by name or reason" value="{{ request('search') }}">
        <input type="date" name="date" class="form-control ms-2" value="{{ request('date') }}">
        <select name="status" class="form-select ms-2">
          <option value="">Select Status</option>
          <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
          <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
          <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <button type="submit" class="btn btn-primary ms-2">Search</button>
      </form>
    </div>

    <div class="btn-toolbar mb-md-0 mb-2">
      <a href="{{ route('leave.take-leave') }}" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center animate-up-2">
        <svg class="icon icon-xs me-2" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Apply Leave
      </a>
    </div>
  </div>

  <div class="card card-body table-wrapper table-responsive border-0 shadow">
    <h2 class="text-muted mb-3 text-center">{{ \Carbon\Carbon::today()->format('l, F d, Y') }}</h2>

    <table class="table-hover table">
      <thead>
        <tr>
          <th>#</th>
          <th>Employee Name</th>
          <th>From - To</th>
          <th>Days</th>
          <th>Reason</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($leaves as $leave)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $leave->user->full_name ?? 'N/A' }}</td>
            <td>
              {{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}
              -
              {{ \Carbon\Carbon::parse($leave->to_date)->format('d M Y') }}
            </td>
            <td>
              {{ \Carbon\Carbon::parse($leave->from_date)->diffInDays(\Carbon\Carbon::parse($leave->to_date)) + 1 }} days
            </td>
            <td>{{ $leave->reason }}</td>
            <td>
              @if($leave->status == 'Pending')
                <span class="badge bg-warning">{{ $leave->status }}</span>
              @elseif($leave->status == 'Approved')
                <span class="badge bg-success">{{ $leave->status }}</span>
              @else
                <span class="badge bg-danger">{{ $leave->status }}</span>
              @endif
            </td>
            <td>
              @if(auth()->user()->is_admin && $leave->status == 'Pending')
                <form action="{{ route('leave.updateStatus', $leave->id) }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="status" value="Approved" />
                  <button type="submit" class="dropdown-item text-success">Approve</button>
                </form>
                <form action="{{ route('leave.updateStatus', $leave->id) }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="status" value="Rejected" />
                  <button type="submit" class="dropdown-item text-danger">Reject</button>
                </form>
              @else
                <span class="text-muted">No action</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center">No leave requests found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="card-footer mt-3 border-0 px-3">
      {{ $leaves->links() }}
    </div>
  </div>
@endsection
