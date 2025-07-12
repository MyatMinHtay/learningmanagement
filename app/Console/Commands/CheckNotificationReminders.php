<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class CheckNotificationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:check-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and send deadline reminder notifications to students';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting notification reminders check...');
        
        try {
            $now = Carbon::now();
            $remindersSent = 0;
            
            // Get all deadline notifications that are active
            $deadlineNotifications = Notification::where('type', 'deadline_reminder')
                ->whereJsonContains('data->auto_reminders_enabled', true)
                ->get();
            
            $this->info("Found {$deadlineNotifications->count()} deadline notifications to process");
            
            foreach ($deadlineNotifications as $notification) {
                $courseId = $notification->data['course_id'] ?? null;
                $type = $notification->data['type'] ?? null;
                $deadlineDate = $notification->data['deadline_date'] ?? null;
                $reminderValue = $notification->data['reminder_value'] ?? 1;
                $reminderUnit = $notification->data['reminder_unit'] ?? 'days';
                
                if (!$courseId || !$type || !$deadlineDate) {
                    $this->warn("Skipping notification {$notification->id} - missing required data");
                    continue;
                }
                
                $deadline = Carbon::parse($deadlineDate);
                $timeUntilDeadline = $this->calculateTimeDifference($now, $deadline, $reminderUnit);
                
                // Check if it's time to send reminder
                $shouldSend = false;
                
                if ($timeUntilDeadline >= 0) {
                    // Future deadline - check for exact match
                    $shouldSend = $timeUntilDeadline == $reminderValue;
                } else {
                    // Past deadline - check if within grace period
                    $gracePeriod = $this->getGracePeriod($reminderUnit);
                    $shouldSend = abs($timeUntilDeadline) <= $gracePeriod;
                }
                
                if ($shouldSend) {
                    $course = Course::find($courseId);
                    if (!$course) {
                        $this->warn("Course {$courseId} not found for notification {$notification->id}");
                        continue;
                    }
                    
                    $enrolledStudents = $course->students()->pluck('users.id');
                    $this->info("Processing course: {$course->name} with {$enrolledStudents->count()} students");
                    
                    foreach ($enrolledStudents as $studentId) {
                        if ($type === 'quiz_deadline') {
                            if ($this->sendQuizReminder($studentId, $course, $deadline, $reminderValue, $reminderUnit)) {
                                $remindersSent++;
                            }
                        } elseif ($type === 'assignment_deadline') {
                            if ($this->sendAssignmentReminder($studentId, $course, $deadline, $reminderValue, $reminderUnit)) {
                                $remindersSent++;
                            }
                        }
                    }
                }
            }
            
            $this->info("Notification reminders check completed. Sent {$remindersSent} reminders.");
            return Command::SUCCESS;
            
        } catch (Exception $e) {
            $this->error('Error checking notification reminders: ' . $e->getMessage());
            Log::error('Error in CheckNotificationReminders command: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Calculate time difference based on unit
     */
    private function calculateTimeDifference($now, $deadline, $unit)
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
    
    /**
     * Get grace period for reminders
     */
    private function getGracePeriod($unit)
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
    
    /**
     * Send quiz reminder to student
     */
    private function sendQuizReminder($studentId, $course, $deadline, $reminderValue, $reminderUnit)
    {
        try {
            // Check if already sent reminder for this deadline
            $existingReminder = Notification::where('type', 'quiz_deadline_urgent')
                ->where('recipient_id', $studentId)
                ->whereJsonContains('data->deadline_date', $deadline->toISOString())
                ->exists();
                
            if ($existingReminder) {
                return false;
            }
            
            // Check if student has completed quiz
            $quiz = $course->quizzes;
            if (!$quiz) {
                return false;
            }
            
            $hasCompleted = \App\Models\QuizAttempt::where('quiz_id', $quiz->id)
                ->where('student_id', $studentId)
                ->where('is_completed', true)
                ->exists();
                
            if ($hasCompleted) {
                return false;
            }
            
            // Calculate time until deadline
            $now = Carbon::now();
            $timeUntilDeadline = $this->calculateTimeDifference($now, $deadline, $reminderUnit);
            
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
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $this->info("Sent quiz reminder to student {$studentId} for course {$course->name}");
            return true;
            
        } catch (Exception $e) {
            $this->error("Error sending quiz reminder to student {$studentId}: " . $e->getMessage());
            Log::error("Error sending quiz reminder: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send assignment reminder to student
     */
    private function sendAssignmentReminder($studentId, $course, $deadline, $reminderValue, $reminderUnit)
    {
        try {
            // Check if already sent reminder for this deadline
            $existingReminder = Notification::where('type', 'assignment_deadline_urgent')
                ->where('recipient_id', $studentId)
                ->whereJsonContains('data->deadline_date', $deadline->toISOString())
                ->exists();
                
            if ($existingReminder) {
                return false;
            }
            
            // Check if student has submitted assignment
            $hasSubmitted = \App\Models\Assignment::where('course_id', $course->id)
                ->where('student_id', $studentId)
                ->exists();
                
            if ($hasSubmitted) {
                return false;
            }
            
            // Calculate time until deadline
            $now = Carbon::now();
            $timeUntilDeadline = $this->calculateTimeDifference($now, $deadline, $reminderUnit);
            
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
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $this->info("Sent assignment reminder to student {$studentId} for course {$course->name}");
            return true;
            
        } catch (Exception $e) {
            $this->error("Error sending assignment reminder to student {$studentId}: " . $e->getMessage());
            Log::error("Error sending assignment reminder: " . $e->getMessage());
            return false;
        }
    }
}
