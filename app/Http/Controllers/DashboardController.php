<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\SystemRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class DashboardController extends Controller
{
    public function show()
    {
        try {
            if (Auth::check()) {
                $users = User::count();
                $systemroles = SystemRole::all();
                
                // Get user permissions from role
                $userPermissions = explode(',', auth()->user()->role->permissions);
                
                // Check permissions instead of roles
                if (in_array('all', $userPermissions) || in_array('admins', $userPermissions)) {
                    return redirect()->route('users');
                } else if (in_array('students', $userPermissions)) {
                    return redirect()->route('students.dashboard');
                } else if (in_array('teachers', $userPermissions)) {
                    return redirect()->route('admincourses');
                } else {
                    return back()->with('warning', 'Access denied! You do not have the required permissions to access this page.');
                }
            } else {
                return redirect('/login')->with('warning', 'access deined! Only Admin Can Access This Page');
            }

        } catch (Exception $e) {
            Log::error('Error in dashboard show: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load dashboard. Please try again.');
        }
    }

    public function showStudentDashboard()
    {
        try {
            $user = Auth::user();
            return redirect()->route('student.courses', $user->id);

        } catch (Exception $e) {
            Log::error('Error in showStudentDashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load student dashboard. Please try again.');
        }
    }
}
