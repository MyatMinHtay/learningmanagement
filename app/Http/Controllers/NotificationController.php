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
                ->orderBy('created_at', 'desc')
                ->orderByRaw("CASE 
                    WHEN type = 'deadline_reminder' THEN 1 
                    WHEN type IN ('quiz_deadline_urgent', 'assignment_deadline_urgent') THEN 2 
                    ELSE 3 
                END")
                ->paginate(15);

            return view('notifications.index', compact('notifications'));

        } catch (Exception $e) {
            Log::error('Error in notifications index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load notifications. Please try again.');
        }
    }

    /**
     * Mark a specific notification as read for the authenticated user
     * Validates ownership and updates notification read status with timestamp
     */
    public function markAsRead($id)
    {
        try {
            // Ensure user can only mark their own notifications as read
            $notification = Notification::where('id', $id)
                ->where('recipient_id', auth()->id())
                ->firstOrFail();

            $notification->markAsRead();

            // Check if request expects JSON (AJAX)
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification marked as read'
                ]);
            }

            return redirect()->back()->with('success', 'Notification marked as read');

        } catch (Exception $e) {
            Log::error('Error in markAsRead: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to mark notification as read'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to mark notification as read. Please try again.');
        }
    }

    /**
     * Mark all unread notifications as read for the authenticated user
     * Bulk updates all unread notifications with read timestamp
     */
    public function markAllAsRead()
    {
        try {
            // Update all unread notifications for current user
            Notification::forUser(auth()->id())
                ->unread()
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            // Check if request expects JSON (AJAX)
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'All notifications marked as read'
                ]);
            }

            return redirect()->back()->with('success', 'All notifications marked as read');

        } catch (Exception $e) {
            Log::error('Error in markAllAsRead: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to mark all notifications as read'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to mark all notifications as read. Please try again.');
        }
    }

    /**
     * Get unread notification count for authenticated user via AJAX
     * Returns JSON response with current unread notification count
     */
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

    /**
     * Get recent unread notifications for popup display
     * Returns JSON response with recent unread notifications
     */
    public function getRecentNotifications()
    {
        try {
            $notifications = Notification::forUser(auth()->id())
                ->unread()
                ->with(['sender'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'type' => $notification->type,
                        'created_at' => $notification->created_at->diffForHumans(),
                        'sender' => $notification->sender ? $notification->sender->username : 'System'
                    ];
                });

            return response()->json([
                'notifications' => $notifications,
                'total_unread' => Notification::forUser(auth()->id())->unread()->count()
            ]);

        } catch (Exception $e) {
            Log::error('Error in getRecentNotifications: ' . $e->getMessage());
            return response()->json(['notifications' => [], 'total_unread' => 0, 'error' => 'Unable to fetch notifications'], 500);
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

    /**
     * Create deadline notifications for all students in a course
     * Validates course ownership, gets enrolled students, creates batch notifications
     */
    public function storeDeadlineNotification(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'type' => 'required|in:quiz_deadline,assignment_deadline',
                'assignment_title' => ['nullable', 'string', 'max:255'],
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'deadline_date' => 'required|date|after:now',
                'reminder_value' => 'required|integer|min:1|max:365',
                'reminder_unit' => 'required|in:seconds,minutes,hours,days'
            ]);

            $course = Course::findOrFail($request->course_id);
            
            // Verify teacher owns the course before creating notifications
            if ($course->created_by !== auth()->id()) {
                return redirect()->back()->with('error', 'You can only create notifications for your own courses');
            }

            // Get all students enrolled in this course
            $studentIds = $course->students()->pluck('users.id')->toArray();

            if (empty($studentIds)) {
                return redirect()->back()->with('warning', 'No students enrolled in this course');
            }

            // Prepare notification data
            $notificationData = [
                'course_id' => $course->id,
                'type' => $request->type,
                'deadline_date' => $request->deadline_date,
                'reminder_value' => $request->reminder_value,
                'reminder_unit' => $request->reminder_unit,
                'auto_reminders_enabled' => true
            ];

            

            // Add assignment title if it's an assignment deadline
            if ($request->type === 'assignment_deadline') {
                $notificationData['assignment_title'] = $request->assignment_title;
            }

            // Create deadline notifications for all enrolled students
            Notification::createDeadlineNotification(
                $studentIds,
                auth()->id(),
                $request->title,
                $request->message,
                $notificationData
            );

            $successMessage = 'Deadline notification sent to ' . count($studentIds) . ' students. Auto-reminders will be sent ' . $request->reminder_value . ' ' . $request->reminder_unit . ' before the deadline.';
            
            if ($request->type === 'assignment_deadline') {
                $successMessage .= ' (Assignment: ' . $request->assignment_title . ')';
            }

            return redirect()->route('notifications.index')
                ->with('success', $successMessage);

        } catch (Exception $e) {
            
            return redirect()->back()->with('error', 'Failed to create deadline notification.' . $e->getMessage());
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
