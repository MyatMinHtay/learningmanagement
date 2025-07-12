<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\User;
use App\Models\Notification;
use App\Models\Assignment;
use Illuminate\Support\Facades\DB;

class CheckDeadlineReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:check-assignments {courseId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug assignment-specific notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $courseId = $this->argument('courseId');
        
        $this->info('=== ASSIGNMENT-SPECIFIC NOTIFICATION DEBUG ===');
        
        // Check basic counts
        $this->info('Total Users: ' . User::count());
        $this->info('Total Courses: ' . Course::count());
        $this->info('Total Assignments: ' . Assignment::count());
        $this->info('Total Notifications: ' . Notification::count());
        
        // Check assignment structure
        $this->info("\n=== ASSIGNMENT STRUCTURE ===");
        $assignments = Assignment::with(['course', 'student'])->limit(5)->get();
        foreach ($assignments as $assignment) {
            $this->info("Assignment ID: {$assignment->id}");
            $this->info("  - Course: " . ($assignment->course->name ?? 'N/A'));
            $this->info("  - Student: " . ($assignment->student->username ?? 'N/A'));
            $this->info("  - Title: " . ($assignment->assignment_title ?? 'NO TITLE SET'));
            $this->info("  - Status: {$assignment->status}");
            $this->info("---");
        }
        
        // Check deadline notifications
        $this->info("\n=== DEADLINE NOTIFICATIONS ===");
        $deadlineNotifications = Notification::where('type', 'deadline_reminder')
            ->whereJsonContains('data->auto_reminders_enabled', true)
            ->get();
        
        foreach ($deadlineNotifications as $notification) {
            $this->info("Notification ID: {$notification->id}");
            $this->info("  - Type: " . ($notification->data['type'] ?? 'N/A'));
            $this->info("  - Course ID: " . ($notification->data['course_id'] ?? 'N/A'));
            $this->info("  - Assignment Title: " . ($notification->data['assignment_title'] ?? 'NO ASSIGNMENT TITLE'));
            $this->info("  - Deadline: " . ($notification->data['deadline_date'] ?? 'N/A'));
            $this->info("---");
        }
        
        // Test assignment checking functionality
        if ($courseId) {
            $this->info("\n=== SPECIFIC COURSE TEST: {$courseId} ===");
            
            $course = Course::find($courseId);
            if (!$course) {
                $this->error("Course {$courseId} not found!");
                return;
            }
            
            $this->info("Course: {$course->name}");
            
            // Get enrolled students
            $students = $course->students()->get();
            $this->info("Enrolled students: {$students->count()}");
            
            foreach ($students as $student) {
                $this->info("\nStudent: {$student->username} (ID: {$student->id})");
                
                // Check assignments for this student in this course
                $studentAssignments = Assignment::where('course_id', $courseId)
                    ->where('student_id', $student->id)
                    ->get();
                
                $this->info("  Submitted assignments: {$studentAssignments->count()}");
                foreach ($studentAssignments as $assignment) {
                    $this->info("    - {$assignment->assignment_title} (Status: {$assignment->status})");
                }
                
                // Test specific assignment checking
                $testAssignmentTitle = "Assignment 1";
                $hasSubmitted = Assignment::hasStudentSubmittedAssignment($courseId, $student->id, $testAssignmentTitle);
                $this->info("  Has submitted '{$testAssignmentTitle}': " . ($hasSubmitted ? 'YES' : 'NO'));
            }
        }
        
        return Command::SUCCESS;
    }
}
