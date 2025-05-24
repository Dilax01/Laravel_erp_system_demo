<?php

use App\Http\Controllers\{
    AttendanceController,
    JobController,
    DepartmentController,
    UserController,
    AuthController
};

use App\Http\Livewire\Dashboard;
use App\Http\Livewire\ResetPassword;
use App\Http\Livewire\ForgotPassword;
use App\Http\Livewire\Auth\Login;
use App\Http\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/
// routes/web.php

use App\Http\Controllers\LeaveController;

Route::post('/leaves/{id}/update-status', [LeaveController::class, 'updateStatus'])->name('leave.updateStatus');
Route::get('/leaves', [LeaveController::class, 'index'])->name('leave.index');
Route::get('/take-leave', [LeaveController::class, 'takeLeave'])->name('leave.take-leave');
Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leave.create');
Route::post('/leave/store', [LeaveController::class, 'store'])->name('leave.store');
// web.php
Route::middleware(['auth'])->group(function () {
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leave.index');         // admin view (all leaves)
    Route::get('/leaves/apply', [LeaveController::class, 'create'])->name('leave.create'); // leave apply form
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leave.store');         // submit leave request
    Route::post('/leaves/{id}/status', [LeaveController::class, 'updateStatus'])->name('leave.updateStatus'); // admin update leave status
    Route::get('/my-leaves', [LeaveController::class, 'takeLeave'])->name('leave.takeLeave'); // user-specific leaves
});
Route::get('/leaves/{id}', [LeaveController::class, 'show'])->name('leave.show');
Route::post('/leaves/{id}/status', [LeaveController::class, 'updateStatus'])->name('leave.updateStatus');



// ✅ Login & Register
Route::get('/register', Register::class)->name('register');
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// ✅ Forgot Password (Livewire only – remove POST route)
Route::get('/forgot-password', ForgotPassword::class)->name('forgot-password');

// ✅ Reset Password via signed URL (Livewire)
Route::get('/reset-password/{token}', ResetPassword::class)
    ->name('reset-password')
    ->middleware('signed');

// ✅ Random Emp (Demo purpose?)
Route::get('/getRandomEmp', [AuthController::class, 'getRandomEmp']);

// ✅ Authenticated User Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    Route::view('/profile', 'profile')->name('profile');

    Route::get('/user-attendance/{user}', [AttendanceController::class, 'userAttendance'])
        ->name('attendances.user-attendance');

    Route::post('/attendance-complain/{attendance}', [AttendanceController::class, 'attendanceComplain'])
        ->name('attendances.attendanceComplain');

    // ✅ Admin-only Routes
    Route::middleware('is_admin')->group(function () {
        Route::get('/', Dashboard::class)->name('dashboard');

        Route::resource('departments', DepartmentController::class)->except('show');
        Route::resource('jobs', JobController::class)->except('show');
        Route::resource('users', UserController::class);

        Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
        Route::get('/take-attendance', [AttendanceController::class, 'takeAttendance'])->name('attendances.take-attendance');

        Route::get('/view-complain/{id}', [AttendanceController::class, 'viewComplain'])->name('attendances.view-complain');
        Route::post('/fix-complain/{id}', [AttendanceController::class, 'fixComplain'])->name('attendances.fix-complain');
    });
});
