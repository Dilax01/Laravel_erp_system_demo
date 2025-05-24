<?php

namespace App\Http\Controllers;

use App\Models\User;  // Use the User model instead of Employee
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Fetch users, optionally filtered by the search query
        $employees = User::query()  // Changed from Employee to User
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->get();

        // Return the view with the users data
        return view('employees.index', compact('employees'));
    }
}
