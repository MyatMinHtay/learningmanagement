<?php

namespace App\Http\Controllers;

use App\Models\AdditionalForm;
use App\Models\CompletionReport;
use App\Models\Courses;
use App\Models\FirstForm;
use App\Models\Mtc;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class CoursesController extends Controller
{

    public function index()
    {
        $user = auth()->user();

       

        $usermtc = $user->mtcname->id;


        $mtc = Mtc::find($usermtc);

      

        
        $courses = $mtc->courses; 


        
        // $course = Courses::where('user_id', $user->id)->latest()->first();


        // if ($course !== null) {
        //     // Form1 validation
        //     if (!FirstForm::where('course_id', $course->id)->exists()) {

        //         return redirect()->back()->with('warning', 'First Form doesn\'t create yet.');
        //     }

        //     if (!FirstForm::where('course_id', $course->id)->where('status', 'approve')->exists()) {

        //         return redirect()->back()->with('warning', $course->coursename . ' First Form must be approve.');
        //     }

        //     if (
        //         AdditionalForm::where('course_id', $course->id)->exists() &&
        //         !AdditionalForm::where('course_id', $course->id)->where('status', 'approve')->exists()
        //     ) {

        //         return redirect()->back()->with('warning', 'Additional Form must be approve.');
        //     }

        //     if (!CompletionReport::where('course_id', $course->id)->where('status', 'approve')->exists()) {

        //         return redirect()->back()->with('warning', 'CompletionReport Form must be approve.');
        //     }
        // }


        return view('auth.createcourse',[
            'usermtc' => $usermtc,
            'mtc' => $mtc,
            'courses' => $courses
        ]);
    }

    public function createcourse()
    {
        $formData = request()->validate([

            'course_list_id' => ['required', 'integer'],
            'mtc_id' => ['required', 'integer'],
            'batch_number' => ['required', 'string'],

        ]);

        

        $formData['user_id'] = auth()->user()->id;

        try {
            $course = Courses::create($formData);
            return redirect()->route('home')->with('success', 'Course Created Successfully');
        } catch (QueryException $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function editcourse(Courses $course)
    {

        $user = auth()->user();

       

        $usermtc = $user->mtcname->id;


        $mtc = Mtc::find($usermtc);

        $courses = $mtc->courses; 
        return view('auth.editcourse', [
            'course' => $course,
            'mtc' => $mtc,
            'courses' => $courses
        ]);
    }

    public function updatecourse(Courses $course)
    {
        $formData = request()->validate([

            'course_list_id' => ['required', 'integer'],
            'mtc_id' => ['required', 'integer'],

        ]);

        $formData['user_id'] = auth()->user()->id;

        try {
            $course = $course->update($formData);

            return redirect()->route('home')->with('success', 'Course Updated Successfully');
        } catch (QueryException $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function viewform(Courses $course)
    {

        if (!FirstForm::where('course_id', $course->id)->exists()) {

            return redirect()->back()->with('warning', 'First Form doesn\'t create yet.');
        }




        $firstform = FirstForm::where('course_id', $course->id)->get();
        $additionalform = AdditionalForm::where('course_id', $course->id)->get();
        $completionform = CompletionReport::where('course_id', $course->id)->get();


        $user = auth()->user();

        if ($user->role->role === 'adminstrator' || $user->role->role === 'moderator') {
            return view('auth.adminviewform', [
                'firstform' => $firstform,
                'additionalform' => $additionalform,
                'completionform' => $completionform,
                'course' => $course
            ]);
        } else {
            return view('auth.viewform', [
                'firstform' => $firstform,
                'additionalform' => $additionalform,
                'completionform' => $completionform,
                'course' => $course
            ]);
        }
    }

    public function adminviewform(Courses $course)
    {

        if (!FirstForm::where('course_id', $course->id)->exists()) {

            return redirect()->back()->with('warning', 'First Form doesn\'t create yet.');
        }

        $firstform = FirstForm::where('course_id', $course->id)->get();
        $additionalform = AdditionalForm::where('course_id', $course->id)->get();
        $completionform = CompletionReport::where('course_id', $course->id)->get();

        return view('auth.adminviewform', [
            'firstform' => $firstform,
            'additionalform' => $additionalform,
            'completionform' => $completionform,
            'course' => $course
        ]);
    }
}
