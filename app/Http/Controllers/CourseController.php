<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\CourseModule;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use Exception;

class CourseController extends Controller
{
    public function index()
    {
        try {
            $courses = Course::latest()->paginate(10);
            
            return view('courses',[
                'courses' => $courses
            ]);

        } catch (Exception $e) {
            Log::error('Error in courses index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load courses. Please try again.');
        }
    }

    public function show(Course $course)
    {
        try {
            return view('showcourse', [
                'course' => $course
            ]);

        } catch (Exception $e) {
            Log::error('Error in course show: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load course details. Please try again.');
        }
    }

    public function adminindex()
    {
        try {
            $teacher = auth()->user();
            $courses = Course::where('created_by', $teacher->id)->latest()->paginate(10);

            return view('admin.courses.index', compact('courses'));

        } catch (Exception $e) {
            Log::error('Error in admin courses index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load courses. Please try again.');
        }
    }

    public function create()
    {
        try {
            return view('admin.courses.create');

        } catch (Exception $e) {
            Log::error('Error in course create: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load course creation form. Please try again.');
        }
    }

    public function store(Request $request)
    {

        
        
        $formData = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer',
            'modules' => 'nullable|array',
            'modules.*.title' => 'required',
            'modules.*.content' => 'required',
        ]);

        

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $cleanName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '', str_replace(' ', '_', $file->getClientOriginalName()));
            $uploadPath = public_path("assets/courses");

            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0777, true);
            }

            $file->move($uploadPath, $cleanName);
            $formData['image'] = "assets/courses/$cleanName";
        }

        $formData['created_by'] = Auth::id();

        

        DB::beginTransaction();

        try {
            $course = Course::create($formData);

            if ($request->has('modules')) {
                foreach ($request->modules as $index => $moduleData) {
                    $course->modules()->create([
                        'title' => $moduleData['title'],
                        'content' => $moduleData['content'],
                        'order' => $index + 1,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create course: ' . $e->getMessage()]);
        }
        

        return redirect()->route('admincourses')->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        try {
            $course->load(['modules' => function ($query) {
                $query->orderBy('order');
            }]);

            return view('admin.courses.edit', compact('course'));

        } catch (Exception $e) {
            Log::error('Error in course edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load course edit form. Please try again.');
        }
    }


    public function update(Request $request, Course $course)
    {
        $formData = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer',
            'modules' => 'nullable|array',
            'modules.*.title' => 'required',
            'modules.*.content' => 'required',
        ]);

        DB::beginTransaction();

        try {
            // Handle image replacement
            if ($request->hasFile('image')) {
                if (!empty($course->image) && File::exists(public_path($course->image))) {
                    File::delete(public_path($course->image));
                }

                $file = $request->file('image');
                $cleanName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '', str_replace(' ', '_', $file->getClientOriginalName()));
                $uploadPath = public_path("assets/courses");

                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0777, true);
                }

                $file->move($uploadPath, $cleanName);
                $formData['image'] = "assets/courses/$cleanName";
            } else {
                $formData['image'] = $course->image;
            }

            $formData['is_free'] = $request->has('is_free');

            // Update the course
            $course->update($formData);

            // Remove old modules
            $course->modules()->delete();

            // Add new modules
            if ($request->has('modules')) {
                foreach ($request->modules as $index => $moduleData) {
                    $course->modules()->create([
                        'title' => $moduleData['title'],
                        'content' => $moduleData['content'],
                        'order' => $index + 1,
                    ]);
                }
            }

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update course: ' . $e->getMessage()]);
        }

        return redirect()->route('admincourses')->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
    DB::beginTransaction();

        try {
            // Delete course image if exists
            if ($course->image && File::exists(public_path($course->image))) {
                File::delete(public_path($course->image));
            }

            // Delete related modules (if not using ON DELETE CASCADE in DB)
            $course->modules()->delete();

            // Delete the course itself
            $course->delete();

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete course: ' . $e->getMessage()]);
        }

        return redirect()->route('admincourses')->with('success', 'Course deleted successfully.');
    }

    public function enrollJson(Request $request, Course $course)
    {
        try {
            $student = auth()->user();
            $studentId = $student->id;

            if (auth()->user()->role->role != 'student') { 
                return response()->json(['status' => 'error', 'message' => 'Only students can enroll.'], 403);
            }

            $alreadyEnrolled = DB::table('course_students')
                ->where('course_id', $course->id)
                ->where('student_id', $studentId)
                ->exists();

            if ($alreadyEnrolled) {
                return response()->json(['status' => 'error', 'message' => 'You are already enrolled in this course.'], 409);
            }

            DB::table('course_students')->insert([
                'course_id' => $course->id,
                'student_id' => $studentId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create notification for course creator
            if ($course->created_by) {
                Notification::createEnrollmentNotification(
                    $course->created_by,
                    $studentId,
                    $course->id,
                    $course->name
                );
            }

            return response()->json(['status' => 'success', 'message' => 'Successfully enrolled in the course.']);

        } catch (Exception $e) {
            Log::error('Error in enrollJson: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to enroll. Please try again.'], 500);
        }
    }

    public function showLessons(Course $course)
    {
        try {
            $studentId = auth()->id();

            $isEnrolled = DB::table('course_students')
                ->where('course_id', $course->id)
                ->where('student_id', $studentId)
                ->exists();

            if (!$isEnrolled) {
                return redirect()->back()->withErrors(['access' => 'You must be enrolled to view the lessons.']);
            }

            // Load lessons
            $lessons = $course->lessons()->get(); // Assuming Course has lessons() relationship

            return view('courses.lessons', compact('course', 'lessons'));

        } catch (Exception $e) {
            Log::error('Error in showLessons: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load lessons. Please try again.');
        }
    }

    public function showStudentCourses(User $student)
    {
        try {
            $studentCourses = $student->courses()->paginate(20);

            return view('admin.student.courses', compact('studentCourses'));

        } catch (Exception $e) {
            Log::error('Error in showStudentCourses: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load student courses. Please try again.');
        }
    }



}
