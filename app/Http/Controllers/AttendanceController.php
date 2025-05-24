<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Notifications\AdminAttendanceComplainNotification;
use App\Notifications\EmployeeAttendanceNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AttendanceController extends Controller
{
    public function index(Request $request)
{
    $search = $request->get('search');
    $status = $request->get('status'); // To filter by attendance status
    $date = $request->get('date'); // To filter by a specific date

    $users = User::query()
        ->where('is_admin', 0)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('job', function ($q2) use ($search) {
                        $q2->where('title', 'like', "%{$search}%");
                    })
                    ->orWhereHas('job.department', function ($q3) use ($search) {
                        $q3->where('name', 'like', "%{$search}%");
                    });
            });
        })
        ->when($status, function ($query) use ($status) {
            $query->whereHas('attendance', function ($q) use ($status) {
                $q->where('status', $status);
            });
        })
        ->when($date, function ($query) use ($date) {
            $query->whereHas('attendance', function ($q) use ($date) {
                $q->whereDate('created_at', Carbon::parse($date));
            });
        })
        ->with([
            'attendance' => function ($q) {
                $q->whereDate('created_at', Carbon::today());
            }
        ])
        ->orderByDesc('created_at')
        ->paginate(15);

    return view('attendances.index', compact("users"));
}


    public function takeAttendance()
    {
        return view('attendances.take-attendance');
    }

    public function userAttendance(User $user)
    {
        $statuses = ["At Work", "Absent", "Late"];
        $user = User::query()->where('id', $user->id)->with("attendances")->first();
        return view('attendances.user-attendance', compact("user", "statuses"));
    }

    public function attendanceComplain(Request $request, Attendance $attendance)
    {

        //find all admins
        $admins = User::whereIsAdmin(1)->get();

        //send notifiaction to all admins 
        Notification::send($admins, new AdminAttendanceComplainNotification($attendance, $request));

        return redirect()->back()->with([
            "message" => "Complain Successfully Sent",
            "title" => "Sent",
            "icon" => "success",
        ]);
    }

    public function viewComplain($id)
    {
        $statuses = ["At Work", "Absent", "Late"];

        $notification = auth()->user()
            ->notifications
            ->when($id, function ($query) use ($id) {
                return $query->where('id', $id);
            })[0];

        return view('attendances.view-complain', compact("notification", 'statuses'));
    }


    public function fixComplain(Request $request, $id)
    {

        try {
            //get notification
            $notification = auth()->user()
                ->unreadNotifications
                ->when($id, function ($query) use ($id) {
                    return $query->where('id', $id);
                });

            //NOTE
            // 0 => At Work
            // 1 => Absent
            // 2 => Late

            //convert status from word to number
            $status = null;
            if ($notification[0]->data['status'] === "At Work") {
                $status = 0;
            } elseif ($notification[0]->data['status'] === "Absent") {
                $status = 1;
            } else {
                $status = 2;
            }

            // find attencance 
            $attendance  = Attendance::where('id', $notification[0]->data['attendance_id'])->first();

            if ($request->result === "accept") {
                //update attendance
                $attendance->status = $status;
                $attendance->save();

                $action = "Accept";
                $message = "Your request to change attendance status from " . $notification[0]->data['current_status'] . " to " . $notification[0]->data['status'] . " is accepted";
                Notification::send($attendance->user, new EmployeeAttendanceNotification($attendance->user, $action, $message));
            } else {
                $action = "Reject";
                $message = "Your request to change attendance status from " . $notification[0]->data['current_status'] . " to " . $notification[0]->data['status'] . " is rejected";
                Notification::send($attendance->user, new EmployeeAttendanceNotification($attendance->user, $action, $message));
            }

            //mark notification as read
            $notification->markAsRead();

            return redirect()->route('attendances.index')->with([
                "message" => "Attendance Updated Successfully",
                "title" => "Updated",
                "icon" => "success",
            ]);
        } catch (\Exception $e) {
            return redirect()->route('attendances.index')->with([
                "message" => $e->getMessage(),
                "title" => "Code Error",
                "icon" => "error",
            ]);
        }
    }
}
