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
    public function adminshow()
    {
        try {
            $users = User::count();
            $systemroles = SystemRole::all();
            
            // Admin dashboard - redirect to users management
            return redirect()->route('users');

        } catch (Exception $e) {
            Log::error('Error in admin dashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load admin dashboard. Please try again.');
        }
    }

    public function studentshow()
    {
        try {
            $users = User::count();
            $systemroles = SystemRole::all();
            
            // Student dashboard - redirect to student dashboard
            return redirect()->route('students.dashboard');

        } catch (Exception $e) {
            Log::error('Error in student dashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load student dashboard. Please try again.');
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
