<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Log;
use Exception;

class HomeController extends Controller
{
    public function showabout()
    {
        try {

            $teachers = User::with('role')->whereHas('role', function ($query) {
                $query->where('role', 'teacher');
            })->limit(10)->get();

            return view('about', [
                'teachers' => $teachers
            ]);

        } catch (Exception $e) {
            Log::error('Error in showabout: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load about page. Please try again.');
        }
    }
    

    public function showcourses()
    {
        try {
            $courses = Course::all();
            return view('courses');

        } catch (Exception $e) {
            Log::error('Error in showcourses: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load courses page. Please try again.');
        }
    }

    public function showContact()
    {
        
            return view('contact');
        
    }

    public function showprofile(User $user)
    {
        try {
            return view('auth.profile',[
                'user' => $user,
            ]);

        } catch (Exception $e) {
            Log::error('Error in showprofile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load profile page. Please try again.');
        }
    }

    public function showeditprofile()
    {
        try {
            return view('auth.editprofile');

        } catch (Exception $e) {
            Log::error('Error in showeditprofile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load edit profile page. Please try again.');
        }
    }
}
