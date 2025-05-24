@extends('layouts.app')

@section('content')
    <h2>Take Leave</h2>

    {{-- Display validation errors --}}
    @if ($errors->any())
        <div style="color:red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('leave.store') }}" method="POST">
        @csrf
        <label>Start Date:</label>
        <input type="date" name="start_date" required><br><br>

        <label>End Date:</label>
        <input type="date" name="end_date" required><br><br>

        <label>Reason:</label>
        <textarea name="reason" required></textarea><br><br>

        <button type="submit">Submit</button>
    </form>

    <hr>

    <h3>Your Leave History</h3>
    <ul>
        @forelse ($userLeaves as $leave)
            <li>
                {{ $leave->start_date }} to {{ $leave->end_date }} - 
                {{ $leave->reason }} 
                ({{ $leave->status }}) 
                — 
                <strong>{{ \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }} days</strong>
            </li>
        @empty
            <li>No leaves taken yet.</li>
        @endforelse
    </ul>
@endsection
