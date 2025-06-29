<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class NotificationController extends Controller
{
    public function index()
    {
        try {
            $notifications = Notification::forUser(auth()->id())
                ->with(['sender'])
                ->latest()
                ->paginate(15);

            return view('notifications.index', compact('notifications'));

        } catch (Exception $e) {
            Log::error('Error in notifications index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load notifications. Please try again.');
        }
    }

    public function markAsRead($id)
    {
        try {
            $notification = Notification::where('id', $id)
                ->where('recipient_id', auth()->id())
                ->firstOrFail();

            $notification->markAsRead();

            return redirect()->back()->with('success', 'Notification marked as read');

        } catch (Exception $e) {
            Log::error('Error in markAsRead: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to mark notification as read. Please try again.');
        }
    }

    public function markAllAsRead()
    {
        try {
            Notification::forUser(auth()->id())
                ->unread()
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return redirect()->back()->with('success', 'All notifications marked as read');

        } catch (Exception $e) {
            Log::error('Error in markAllAsRead: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to mark all notifications as read. Please try again.');
        }
    }

    public function getUnreadCount()
    {
        try {
            $count = Notification::forUser(auth()->id())->unread()->count();
            return response()->json(['count' => $count]);

        } catch (Exception $e) {
            Log::error('Error in getUnreadCount: ' . $e->getMessage());
            return response()->json(['count' => 0, 'error' => 'Unable to fetch count'], 500);
        }
    }

    // For teachers to create deadline notifications
    public function createDeadlineForm()
    {
        try {
            $user = auth()->user();
            $courses = Course::where('created_by', $user->id)->get();
            
            return view('notifications.create-deadline', compact('courses'));

        } catch (Exception $e) {
            Log::error('Error in createDeadlineForm: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load notification form. Please try again.');
        }
    }

    public function storeDeadlineNotification(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'type' => 'required|in:quiz_deadline,assignment_deadline',
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'deadline_date' => 'required|date|after:today'
            ]);

            $course = Course::findOrFail($request->course_id);
            
            // Check if user is course creator
            if ($course->created_by !== auth()->id()) {
                return redirect()->back()->with('error', 'You can only create notifications for your own courses');
            }

            // Get all enrolled students
            $studentIds = $course->students()->pluck('users.id')->toArray();

            if (empty($studentIds)) {
                return redirect()->back()->with('warning', 'No students enrolled in this course');
            }

            // Create notifications for all enrolled students
            Notification::createDeadlineNotification(
                $studentIds,
                auth()->id(),
                $request->title,
                $request->message,
                [
                    'course_id' => $course->id,
                    'type' => $request->type,
                    'deadline_date' => $request->deadline_date
                ]
            );

            return redirect()->route('notifications.index')
                ->with('success', 'Deadline notification sent to ' . count($studentIds) . ' students');

        } catch (Exception $e) {
            Log::error('Error in storeDeadlineNotification: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create deadline notification. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $notification = Notification::where('id', $id)
                ->where('recipient_id', auth()->id())
                ->firstOrFail();

            $notification->delete();

            return redirect()->back()->with('success', 'Notification deleted');

        } catch (Exception $e) {
            Log::error('Error in notification destroy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete notification. Please try again.');
        }
    }
}
