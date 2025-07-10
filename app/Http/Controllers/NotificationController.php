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
            // Check for deadline reminders when user visits notifications page
            self::checkAndSendReminders();
            
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

            return redirect()->back()->with('success', 'Notification marked as read');

        } catch (Exception $e) {
            Log::error('Error in markAsRead: ' . $e->getMessage());
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

            return redirect()->back()->with('success', 'All notifications marked as read');

        } catch (Exception $e) {
            Log::error('Error in markAllAsRead: ' . $e->getMessage());
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

            // Create deadline notifications for all enrolled students
            Notification::createDeadlineNotification(
                $studentIds,
                auth()->id(),
                $request->title,
                $request->message,
                [
                    'course_id' => $course->id,
                    'type' => $request->type,
                    'deadline_date' => $request->deadline_date,
                    'reminder_value' => $request->reminder_value,
                    'reminder_unit' => $request->reminder_unit,
                    'auto_reminders_enabled' => true
                ]
            );

            return redirect()->route('notifications.index')
                ->with('success', 'Deadline notification sent to ' . count($studentIds) . ' students. Auto-reminders will be sent ' . $request->reminder_value . ' ' . $request->reminder_unit . ' before the deadline.');

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

    /**
     * Simple method to check deadlines and send reminders
     * Called when users visit pages
     */
    public static function checkAndSendReminders()
    {
        try {
            $now = \Carbon\Carbon::now();
            
            // Get all deadline notifications
            $deadlineNotifications = Notification::where('type', 'deadline_reminder')->get();
            
            foreach ($deadlineNotifications as $notification) {
                $courseId = $notification->data['course_id'] ?? null;
                $type = $notification->data['type'] ?? null;
                $deadlineDate = $notification->data['deadline_date'] ?? null;
                $reminderValue = $notification->data['reminder_value'] ?? 1;
                $reminderUnit = $notification->data['reminder_unit'] ?? 'days';
                
                if (!$courseId || !$type || !$deadlineDate) continue;
                
                $deadline = \Carbon\Carbon::parse($deadlineDate);
                $timeUntilDeadline = self::calculateTimeDifference($now, $deadline, $reminderUnit);
                
                // Check if it's time to send reminder
                $shouldSend = false;
                
                if ($timeUntilDeadline >= 0) {
                    // Future deadline - check for exact match
                    $shouldSend = $timeUntilDeadline == $reminderValue;
                } else {
                    // Past deadline - check if within grace period
                    $gracePeriod = self::getGracePeriod($reminderUnit);
                    $shouldSend = abs($timeUntilDeadline) <= $gracePeriod;
                }
                
                if ($shouldSend) {
                    $course = Course::find($courseId);
                    if (!$course) continue;
                    
                    $enrolledStudents = $course->students()->pluck('users.id');
                    
                    foreach ($enrolledStudents as $studentId) {
                        if ($type === 'quiz_deadline') {
                            self::sendQuizReminder($studentId, $course, $deadline, $reminderValue, $reminderUnit);
                        } elseif ($type === 'assignment_deadline') {
                            self::sendAssignmentReminder($studentId, $course, $deadline, $reminderValue, $reminderUnit);
                        }
                    }
                }
            }
            
        } catch (Exception $e) {
            Log::error('Error in checkAndSendReminders: ' . $e->getMessage());
        }
    }
    
    private static function calculateTimeDifference($now, $deadline, $unit)
    {
        switch ($unit) {
            case 'seconds':
                return $now->diffInSeconds($deadline, false);
            case 'minutes':
                return $now->diffInMinutes($deadline, false);
            case 'hours':
                return $now->diffInHours($deadline, false);
            case 'days':
            default:
                return $now->diffInDays($deadline, false);
        }
    }
    
    private static function getGracePeriod($unit)
    {
        switch ($unit) {
            case 'seconds':
                return 60; // 1 minute grace period
            case 'minutes':
                return 30; // 30 minute grace period
            case 'hours':
                return 12; // 12 hour grace period
            case 'days':
            default:
                return 1; // 1 day grace period
        }
    }
    
    private static function sendQuizReminder($studentId, $course, $deadline, $reminderValue, $reminderUnit)
    {
        try {
            // Check if already sent reminder for this deadline
            $existingReminder = Notification::where('type', 'quiz_deadline_urgent')
                ->where('recipient_id', $studentId)
                ->whereJsonContains('data->deadline_date', $deadline->toISOString())
                ->exists();
                
            if ($existingReminder) return;
            
            // Check if student has completed quiz
            $quiz = $course->quizzes;
            if (!$quiz) return;
            
            $hasCompleted = \App\Models\QuizAttempt::where('quiz_id', $quiz->id)
                ->where('student_id', $studentId)
                ->where('is_completed', true)
                ->exists();
                
            if ($hasCompleted) return;
            
            // Calculate time until deadline
            $now = \Carbon\Carbon::now();
            $timeUntilDeadline = self::calculateTimeDifference($now, $deadline, $reminderUnit);
            
            // Determine urgency
            $isUrgent = ($reminderUnit === 'days' && $timeUntilDeadline <= 1) || 
                       ($reminderUnit === 'hours' && $timeUntilDeadline <= 2) || 
                       ($reminderUnit === 'minutes' && $timeUntilDeadline <= 30) ||
                       ($reminderUnit === 'seconds' && $timeUntilDeadline <= 300);
            
            $urgencyLevel = $isUrgent ? 'URGENT' : 'REMINDER';
            $urgencyEmoji = $isUrgent ? '🚨' : '⏰';
            
            $unitText = $timeUntilDeadline == 1 ? rtrim($reminderUnit, 's') : $reminderUnit;
            $title = "{$urgencyEmoji} {$urgencyLevel}: Quiz Deadline in {$timeUntilDeadline} {$unitText}";
            
            $message = $isUrgent 
                ? "🚨 URGENT REMINDER: Your quiz '{$quiz->title}' in {$course->name} is due soon!\n\nDeadline: {$deadline->format('M j, Y \a\t h:i A')}\n\nPlease complete it as soon as possible!"
                : "⏰ REMINDER: Your quiz '{$quiz->title}' in {$course->name} is due in {$timeUntilDeadline} {$unitText}.\n\nDeadline: {$deadline->format('M j, Y \a\t h:i A')}\n\nDon't forget to complete it before the deadline!";

            Notification::create([
                'type' => 'quiz_deadline_urgent',
                'recipient_id' => $studentId,
                'sender_id' => $course->created_by,
                'title' => $title,
                'message' => $message,
                'data' => [
                    'quiz_id' => $quiz->id,
                    'course_id' => $course->id,
                    'deadline_date' => $deadline->toISOString(),
                    'time_until_deadline' => $timeUntilDeadline,
                    'reminder_value' => $reminderValue,
                    'reminder_unit' => $reminderUnit,
                    'urgency_level' => $urgencyLevel
                ],
                'created_at' => now()->addSecond(), // Add 1 second to ensure proper ordering
                'updated_at' => now()->addSecond()
            ]);
            
        } catch (Exception $e) {
            Log::error("Error sending quiz reminder: " . $e->getMessage());
        }
    }
    
    private static function sendAssignmentReminder($studentId, $course, $deadline, $reminderValue, $reminderUnit)
    {
        try {
            // Check if already sent reminder for this deadline
            $existingReminder = Notification::where('type', 'assignment_deadline_urgent')
                ->where('recipient_id', $studentId)
                ->whereJsonContains('data->deadline_date', $deadline->toISOString())
                ->exists();
                
            if ($existingReminder) return;
            
            // Check if student has submitted assignment
            $hasSubmitted = \App\Models\Assignment::where('course_id', $course->id)
                ->where('student_id', $studentId)
                ->exists();
                
            if ($hasSubmitted) return;
            
            // Calculate time until deadline
            $now = \Carbon\Carbon::now();
            $timeUntilDeadline = self::calculateTimeDifference($now, $deadline, $reminderUnit);
            
            // Determine urgency
            $isUrgent = ($reminderUnit === 'days' && $timeUntilDeadline <= 1) || 
                       ($reminderUnit === 'hours' && $timeUntilDeadline <= 2) || 
                       ($reminderUnit === 'minutes' && $timeUntilDeadline <= 30) ||
                       ($reminderUnit === 'seconds' && $timeUntilDeadline <= 300);
            
            $urgencyLevel = $isUrgent ? 'URGENT' : 'REMINDER';
            $urgencyEmoji = $isUrgent ? '🚨' : '📋';
            
            $unitText = $timeUntilDeadline == 1 ? rtrim($reminderUnit, 's') : $reminderUnit;
            $title = "{$urgencyEmoji} {$urgencyLevel}: Assignment Deadline in {$timeUntilDeadline} {$unitText}";
            
            $message = $isUrgent 
                ? "🚨 URGENT REMINDER: Your assignment for {$course->name} is due soon!\n\nDeadline: {$deadline->format('M j, Y \a\t h:i A')}\n\nPlease submit it as soon as possible!"
                : "📋 REMINDER: Your assignment for {$course->name} is due in {$timeUntilDeadline} {$unitText}.\n\nDeadline: {$deadline->format('M j, Y \a\t h:i A')}\n\nDon't forget to submit it before the deadline!";

            Notification::create([
                'type' => 'assignment_deadline_urgent',
                'recipient_id' => $studentId,
                'sender_id' => $course->created_by,
                'title' => $title,
                'message' => $message,
                'data' => [
                    'course_id' => $course->id,
                    'deadline_date' => $deadline->toISOString(),
                    'time_until_deadline' => $timeUntilDeadline,
                    'reminder_value' => $reminderValue,
                    'reminder_unit' => $reminderUnit,
                    'urgency_level' => $urgencyLevel
                ],
                'created_at' => now()->addSecond(), // Add 1 second to ensure proper ordering
                'updated_at' => now()->addSecond()
            ]);
            
        } catch (Exception $e) {
            Log::error("Error sending assignment reminder: " . $e->getMessage());
        }
    }
}
