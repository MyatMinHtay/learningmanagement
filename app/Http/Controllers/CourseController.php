<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index()
    {

    
        $courses = Course::latest()->paginate(10);

        
        return view('courses',[
            'courses' => $courses
        ]);
    }

    public function show(Course $course)
    {

       
        return view('showcourse', [
            'course' => $course
        ]);
    }

    public function adminindex()
    {
        $courses = Course::latest()->paginate(10);

        

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
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
        $course->load(['modules' => function ($query) {
            $query->orderBy('order');
        }]);



        return view('admin.courses.edit', compact('course'));
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
}
