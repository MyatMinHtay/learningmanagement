<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Exception;

class LessonController extends Controller
{
    public function index()
    {
        try {
            $teacher = auth()->user();

            $courseIds = Course::where('created_by', $teacher->id)->pluck('id');

            $lessons = Lesson::with('course')
                ->whereIn('course_id', $courseIds)
                ->latest()
                ->paginate(10);
            
            return view('admin.lessons.index', compact('lessons'));

        } catch (Exception $e) {
            Log::error('Error in lessons index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load lessons. Please try again.');
        }
    }

    public function create()
    {
        try {
            $teacher = auth()->user();
            $courses = Course::where('created_by', $teacher->id)->select('id', 'name')->get();
            return view('admin.lessons.create', compact('courses'));

        } catch (Exception $e) {
            Log::error('Error in lesson create: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load lesson creation form. Please try again.');
        }
    }

    /**
     * Create a new lesson with video and attachment uploads
     * Validates lesson data, handles video and PDF file uploads, creates lesson record
     */
    public function store(Request $request)
    {

        
        $validated = $request->validate([
            'course_id'   => 'required|exists:courses,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'video'       => 'nullable|url',
            'video_file'  => 'nullable|file|mimetypes:video/mp4,video/x-msvideo,video/quicktime|max:102400', // 100MB max
            'attachment'  => 'nullable|mimes:pdf|max:20480', // 10MB max
            
        ]);  

            try {
                // Handle video file upload and store securely
                if ($request->hasFile('video_file')) {
                    $validated['video'] = $request->file('video_file')->store('videos', 'public');
                }

                // Handle PDF attachment upload for lesson materials
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
        try {
            $teacher = auth()->user();
            $courses = Course::where('created_by', $teacher->id)->select('id', 'name')->get();
            return view('admin.lessons.edit', compact('lesson', 'courses'));

        } catch (Exception $e) {
            Log::error('Error in lesson edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load lesson edit form. Please try again.');
        }
    }

    /**
     * Update lesson information with file replacement handling
     * Updates lesson data, replaces video/attachment files, cleans up old files
     */
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
            // Replace video file - delete old, upload new
            if ($request->hasFile('video_file')) {
                if ($lesson->video && Storage::disk('public')->exists($lesson->video)) {
                    Storage::disk('public')->delete($lesson->video);
                }
                $validated['video'] = $request->file('video_file')->store('videos', 'public');
            }

            // Replace attachment file - delete old, upload new
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

    /**
     * Delete lesson and cleanup associated files
     * Removes lesson video and attachment files from storage, deletes lesson record
     */
    public function destroy(Lesson $lesson)
    {
        try {
            // Delete lesson video file if exists
            if ($lesson->video && Storage::disk('public')->exists($lesson->video)) {
                Storage::disk('public')->delete($lesson->video);
            }
            // Delete lesson attachment file if exists
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
