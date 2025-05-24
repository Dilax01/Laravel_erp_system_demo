<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    // Admin: List all leaves with search and filters
    public function index(Request $request)
    {
        $query = Leave::with('user')->orderByDesc('created_at');

        // Search by user name or reason
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%");
                })->orWhere('reason', 'like', "%$search%");
            });
        }

        // Filter by a specific date (between from_date and to_date)
        if ($date = $request->input('date')) {
            $query->whereDate('from_date', '<=', $date)
                  ->whereDate('to_date', '>=', $date);
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $leaves = $query->paginate(10);

        return view('leaves.index', compact('leaves'));
    }

    // Show leave application form
    public function create()
    {
        return view('leaves.create');
    }

    // Store new leave request with validation and leave limit checks
    public function store(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string|max:255',
        ]);

        $userId = Auth::id();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date);
        $requestedDays = $fromDate->diffInDays($toDate) + 1;

        // Calculate user's approved leaves in the month of from_date
        $currentMonthLeaves = Leave::where('user_id', $userId)
            ->where('status', Leave::STATUS_APPROVED)
            ->whereYear('from_date', $fromDate->year)
            ->whereMonth('from_date', $fromDate->month)
            ->sum('number_of_days');

        // Calculate user's approved leaves in the year of from_date
        $currentYearLeaves = Leave::where('user_id', $userId)
            ->where('status', Leave::STATUS_APPROVED)
            ->whereYear('from_date', $fromDate->year)
            ->sum('number_of_days');

        $maxMonthlyLeave = 3;
        $maxAnnualLeave = 30;

        if (($currentMonthLeaves + $requestedDays) > $maxMonthlyLeave) {
            return back()->withErrors(['from_date' => "Monthly leave limit exceeded. Maximum allowed is $maxMonthlyLeave days."])->withInput();
        }

        if (($currentYearLeaves + $requestedDays) > $maxAnnualLeave) {
            return back()->withErrors(['from_date' => "Annual leave limit exceeded. Maximum allowed is $maxAnnualLeave days."])->withInput();
        }

        Leave::create([
            'user_id' => $userId,
            'from_date' => $fromDate->toDateString(),
            'to_date' => $toDate->toDateString(),
            'number_of_days' => $requestedDays,
            'reason' => $request->reason,
            'status' => Leave::STATUS_PENDING,
        ]);

        return redirect()->route('leave.takeLeave')->with([
            'message' => 'Leave request submitted successfully.',
            'title' => 'Success',
            'icon' => 'success',
        ]);
    }

    // Admin: update leave status (approve or reject)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:' . Leave::STATUS_APPROVED . ',' . Leave::STATUS_REJECTED,
        ]);

        $leave = Leave::findOrFail($id);
        $leave->status = $request->status;
        $leave->save();

        return redirect()->route('leave.index')->with([
            'message' => 'Leave status updated successfully.',
            'title' => 'Success',
            'icon' => 'success',
        ]);
    }

    // User-specific leave history page
    public function takeLeave()
    {
        $userLeaves = Leave::where('user_id', Auth::id())
            ->orderByDesc('from_date')
            ->paginate(10);

        return view('leaves.take', compact('userLeaves'));
    }
}
