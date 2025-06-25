<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::with('course')->latest()->paginate(10); // eager load course relationship
        return view('admin.lessons.index', compact('lessons'));
    }

    public function create()
    {
        $courses = Course::select('id', 'name')->get();
        return view('admin.lessons.create', compact('courses'));
    }

    public function store(Request $request)
    {

        
        $validated = $request->validate([
            'course_id'   => 'required|exists:courses,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'video'       => 'nullable|url',
            'video_file'  => 'nullable|file|mimetypes:video/mp4,video/x-msvideo,video/quicktime|max:102400', // 100MB max
            'attachment'  => 'required|mimes:pdf|max:20480', // 10MB max
            
        ]);  

            try {
                if ($request->hasFile('video_file')) {
                    $validated['video'] = $request->file('video_file')->store('videos', 'public');
                }

                if ($request->hasFile('attachment')) {
                    $validated['attachment'] = $request->file('attachment')->store('attachments', 'public');
                }

                Lesson::create($validated);

                return redirect()->route('lessons.index')->with('success', 'Lesson created successfully.');
            } catch (\Exception $e) {
            

                return back()->withInput()->with('error', 'Something went wrong while creating the lesson. Please try again.' . $e->getMessage());
            }
    }

    public function edit(Lesson $lesson)
    {
        $courses = Course::select('id', 'name')->get();
        return view('admin.lessons.edit', compact('lesson', 'courses'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'course_id'   => 'required|exists:courses,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'video'       => 'nullable|url',
            'video_file'  => 'nullable|file|mimetypes:video/mp4,video/x-msvideo,video/quicktime|max:51200',
            'attachment'  => 'nullable|file|max:10240',
        ]);

        try {
            if ($request->hasFile('video_file')) {
                if ($lesson->video && Storage::disk('public')->exists($lesson->video)) {
                    Storage::disk('public')->delete($lesson->video);
                }
                $validated['video'] = $request->file('video_file')->store('videos', 'public');
            }

            if ($request->hasFile('attachment')) {
                if ($lesson->attachment && Storage::disk('public')->exists($lesson->attachment)) {
                    Storage::disk('public')->delete($lesson->attachment);
                }
                $validated['attachment'] = $request->file('attachment')->store('attachments', 'public');
            }

            $lesson->update($validated);

            return redirect()->route('lessons.index')->with('success', 'Lesson updated successfully.');
        } catch (\Exception $e) {
            
            return back()->withInput()->with('error', 'An error occurred while updating the lesson.' . $e->getMessage());
        }
    }

    public function destroy(Lesson $lesson)
    {
        try {
            if ($lesson->video && Storage::disk('public')->exists($lesson->video)) {
                Storage::disk('public')->delete($lesson->video);
            }
            if ($lesson->attachment && Storage::disk('public')->exists($lesson->attachment)) {
                Storage::disk('public')->delete($lesson->attachment);
            }
            $lesson->delete();
            return redirect()->route('lessons.index')->with('success', 'Lesson deleted successfully.');
        } catch (\Exception $e) {
            
            return back()->with('error', 'An error occurred while deleting the lesson.' . $e->getMessage());
        }
    }
}
