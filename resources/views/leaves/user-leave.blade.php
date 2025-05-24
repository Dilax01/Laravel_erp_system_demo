@extends('layouts.app')
@php
$title = 'User Leave';
@endphp
@section('title', $title . 's')

@section('content')
  {{-- header --}}
  <div class="d-flex justify-content-between flex-md-nowrap align-items-center flex-wrap py-4">
    <div class="d-block mb-md-0 mb-4">
      <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
          <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">
              <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                </path>
              </svg>
            </a>
          </li>
          <li class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ $title }}s</a></li>
        </ol>
      </nav>
      <h2 class="h4">All {{ $title }}s for {{ $user->full_name }}</h2>
    </div>
  </div>

  <div class="card card-body table-wrapper table-responsive border-0 shadow">
    <h2 class="text-muted mb-3 text-center">{{ $user->full_name }}</h2>
    <table class="table-hover table">
      <thead>
        <tr>
          <th class="border-gray-200">#</th>
          <th class="border-gray-200">Leave Date</th>
          <th class="border-gray-200">Reason</th>
          <th class="border-gray-200">Status</th>
          @if (!auth()->user()->is_admin)
            <th class="border-gray-200">Action</th>
          @endif
        </tr>
      </thead>
      <tbody>

        @forelse ($user->leaves as $leave)
          <tr>
            <td class="fw-bold">{{ $leave->id }}</td>
            <td><span class="fw-normal">{{ $leave->leave_date->format('F d, Y') }}</span></td>
            <td><span class="fw-normal">{{ $leave->reason }}</span></td>
            <td><span class="badge 
              @if($leave->status === 'Approved') bg-success
              @elseif($leave->status === 'Rejected') bg-danger
              @else bg-warning
              @endif">
              {{ $leave->status }}
            </span></td>

            @if (!auth()->user()->is_admin)
              <td>
                <button class="btn me-3 btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#leave_{{ $leave->id }}">
                  Complain
                </button>
              </td>
            @endif
          </tr>

          <!-- Modal for Complain -->
          <div class="modal fade" id="leave_{{ $leave->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header border-0">
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-md-5">
                  <h2 class="h4 text-center">Complain About Leave on {{ $leave->leave_date->format('F d, Y') }}</h2>
                  <p class="mb-4 text-center">
                    Your leave status is <span class="text-danger fw-bold">{{ $leave->status }}</span>
                  </p>
                  <form action="{{ route('leaves.leaveComplain', $leave->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="current_status" value="{{ $leave->status }}">
                    <div class="form-group mb-4">
                      <label for="status">Select New Status</label>
                      @php
                        $leaveStatuses = ['Pending', 'Approved', 'Rejected']; // Define statuses as needed
                      @endphp
                      @foreach ($leaveStatuses as $status)
                        @if ($status === $leave->status)
                          @continue
                        @endif
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="status" id="status_{{ $leave->id }}_{{ $status }}" value="{{ $status }}" required>
                          <label class="form-check-label" for="status_{{ $leave->id }}_{{ $status }}">
                            {{ $status }}
                          </label>
                        </div>
                      @endforeach
                    </div>

                    <div class="form-group mb-4">
                      <label for="message">Your Message</label>
                      <textarea class="form-control border-gray-300" name="message" cols="30" rows="2" required autofocus></textarea>
                    </div>

                    <div class="d-grid">
                      <button type="submit" class="btn btn-primary">Submit Complaint</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- End of Modal -->

        @empty
          <tr>
            <td colspan="5" class="text-center">No leave requests found for this user.</td>
          </tr>
        @endforelse

      </tbody>
    </table>
  </div>
@endsection
