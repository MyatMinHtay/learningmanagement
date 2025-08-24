<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
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

    public function index(Request $request)
    {
        try {
            $query = Course::with(['category', 'students']);
            
            // Filter by category if provided
            if ($request->has('category') && $request->category != '') {
                $query->where('category_id', $request->category);
            }
            
            $courses = $query->latest()->paginate(10);
            $categories = CourseCategory::ordered()->get();
            
            return view('courses',[
                'courses' => $courses,
                'categories' => $categories,
                'selectedCategory' => $request->category
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

    public function adminindex(Request $request)
    {
        try {
            $teacher = auth()->user();
            $query = Course::with('category')->where('created_by', $teacher->id);
            
            // Filter by category if provided
            if ($request->has('category') && $request->category != '') {
                $query->where('category_id', $request->category);
            }
            
            $courses = $query->latest()->paginate(10);
            $categories = CourseCategory::ordered()->get();

            return view('admin.courses.index', compact('courses', 'categories'));

        } catch (Exception $e) {
            Log::error('Error in admin courses index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load courses. Please try again.');
        }
    }

    public function create()
    {
        try {
            $categories = CourseCategory::ordered()->get();
            return view('admin.courses.create', compact('categories'));

        } catch (Exception $e) {
            Log::error('Error in course create: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load course creation form. Please try again.');
        }
    }

    /**
     * Create a new course with modules and handle file uploads
     * Validates input, uploads course image, creates course and associated modules
     */
    public function store(Request $request)
    {
        
        $formData = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:course_categories,id',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer',
            'modules' => 'nullable|array',
            'modules.*.title' => 'required',
            'modules.*.content' => 'required',
        ]);

        

        // Handle course image upload with secure filename generation
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

            // Create course modules with proper ordering
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
        

        return redirect()->route('teachercourses')->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        try {
            $course->load(['modules' => function ($query) {
                $query->orderBy('order');
            }]);
            
            $categories = CourseCategory::ordered()->get();

            return view('admin.courses.edit', compact('course', 'categories'));

        } catch (Exception $e) {
            Log::error('Error in course edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load course edit form. Please try again.');
        }
    }

    /**
     * Update course information, handle image replacement, and rebuild modules
     * Replaces old course image, updates course data, and recreates all modules
     */
    public function update(Request $request, Course $course)
    {
        $formData = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:course_categories,id',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer',
            'modules' => 'nullable|array',
            'modules.*.title' => 'required',
            'modules.*.content' => 'required',
        ]);

        DB::beginTransaction();

        try {
            // Handle course image replacement - delete old, upload new
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

            // Remove old modules and recreate with new data
            $course->modules()->delete();

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

        return redirect()->route('teachercourses')->with('success', 'Course updated successfully.');
    }

    /**
     * Delete course and cleanup associated files and data
     * Removes course image, deletes related modules, and removes course record
     */
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

    /**
     * Handle student enrollment in course with validation and notification
     * Validates student role, checks for duplicate enrollment, creates enrollment record
     */
    public function enrollJson(Request $request, Course $course)
    {
        try {
            $student = auth()->user();
            $studentId = $student->id;

            // Validate user role - only students can enroll
            if (auth()->user()->role->role != 'student') { 
                return response()->json(['status' => 'error', 'message' => 'Only students can enroll.'], 403);
            }

            // Check for existing enrollment to prevent duplicates
            $alreadyEnrolled = DB::table('course_students')
                ->where('course_id', $course->id)
                ->where('student_id', $studentId)
                ->exists();

            if ($alreadyEnrolled) {
                return response()->json(['status' => 'error', 'message' => 'You are already enrolled in this course.'], 409);
            }

            // Create enrollment record with timestamps
            DB::table('course_students')->insert([
                'course_id' => $course->id,
                'student_id' => $studentId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create notification for course creator about new enrollment
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

    /**
     * Display course lessons for enrolled students only
     * Validates student enrollment before allowing access to course content
     */
    public function showLessons(Course $course)
    {
        try {
            $studentId = auth()->id();

            // Verify student is enrolled in the course before showing lessons
            $isEnrolled = DB::table('course_students')
                ->where('course_id', $course->id)
                ->where('student_id', $studentId)
                ->exists();

            if (!$isEnrolled) {
                return redirect()->back()->withErrors(['access' => 'You must be enrolled to view the lessons.']);
            }

            // Load lessons for enrolled student
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

    public function reportTable(Request $request)
    {
        try {
            $query = Course::with(['creator', 'category', 'students']);
            
            // Filter by teacher/creator
            if ($request->has('teacher') && $request->teacher != '') {
                $query->where('created_by', $request->teacher);
            }
            
            // Filter by category
            if ($request->has('category') && $request->category != '') {
                $query->where('category_id', $request->category);
            }
            
            // Filter by date range
            if ($request->has('date_from') && $request->date_from != '') {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->has('date_to') && $request->date_to != '') {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            $courses = $query->latest()->paginate(15);
            $teachers = User::whereHas('role', function($q) {
                $q->where('role', 'teacher');
            })->get();
            $categories = CourseCategory::all();
            
            return view('admin.reports.courses', compact('courses', 'teachers', 'categories'));
            
        } catch (Exception $e) {
            Log::error('Error in course report table: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load course reports. Please try again.');
        }
    }

}
