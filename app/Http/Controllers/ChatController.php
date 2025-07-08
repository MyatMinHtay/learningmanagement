<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display chat conversations list based on user role
     * Shows course-based conversations for students with teachers, and teachers with students
     */
    public function index()
    {
        $user = Auth::user();
        $conversations = [];

        if ($user->role->role === 'student') {
            // Get courses the student is enrolled in and create conversations with teachers
            $courses = Course::whereHas('students', function ($query) use ($user) {
                $query->where('student_id', $user->id);
            })->with('creator')->get();

            foreach ($courses as $course) {
                // Get last message and unread count for each teacher conversation
                $lastMessage = Message::forConversation($user->id, $course->created_by, $course->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $unreadCount = Message::forConversation($user->id, $course->created_by, $course->id)
                    ->where('receiver_id', $user->id)
                    ->unread()
                    ->count();

                $conversations[] = [
                    'course' => $course,
                    'teacher' => $course->creator,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                ];
            }
        } else {
            // Get courses the teacher created and conversations with enrolled students
            $courses = Course::where('created_by', $user->id)->get();

            foreach ($courses as $course) {
                $students = User::whereHas('courses', function ($query) use ($course) {
                    $query->where('course_id', $course->id);
                })->get();

                foreach ($students as $student) {
                    // Only show conversations that have at least one message
                    $lastMessage = Message::forConversation($user->id, $student->id, $course->id)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    $unreadCount = Message::forConversation($user->id, $student->id, $course->id)
                        ->where('receiver_id', $user->id)
                        ->unread()
                        ->count();

                    if ($lastMessage) {
                        $conversations[] = [
                            'course' => $course,
                            'student' => $student,
                            'last_message' => $lastMessage,
                            'unread_count' => $unreadCount,
                        ];
                    }
                }
            }
        }

        return view('chat.index', compact('conversations'));
    }

    /**
     * Display specific chat conversation with permission validation
     * Validates user permissions, loads chat history, marks messages as read
     */
    public function show($courseId, $userId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);
        $otherUser = User::findOrFail($userId);

        // Validate chat permissions based on user role and enrollment
        if ($user->role->role === 'student') {
            // Student can only chat with teachers of courses they're enrolled in
            $enrolled = $course->students()->where('student_id', $user->id)->exists();
            if (!$enrolled || $course->created_by !== $otherUser->id) {
                abort(403, 'Unauthorized');
            }
        } else {
            // Teacher can only chat with students enrolled in their courses
            if ($course->created_by !== $user->id) {
                abort(403, 'Unauthorized');
            }
            $enrolled = $course->students()->where('student_id', $otherUser->id)->exists();
            if (!$enrolled) {
                abort(403, 'Unauthorized');
            }
        }

        // Load conversation messages in chronological order
        $messages = Message::forConversation($user->id, $otherUser->id, $courseId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark all received messages as read
        Message::where('receiver_id', $user->id)
            ->where('sender_id', $otherUser->id)
            ->where('course_id', $courseId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('chat.show', compact('course', 'otherUser', 'messages'));
    }

    /**
     * Send a new message with permission validation
     * Validates sender permissions, creates message record, returns JSON response for AJAX
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'course_id' => 'required|exists:courses,id',
            'receiver_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();
        $course = Course::findOrFail($request->course_id);
        $receiver = User::findOrFail($request->receiver_id);

        // Validate message sending permissions based on user roles
        if ($user->role->role === 'student') {
            // Student can only send to course teacher
            if ($course->created_by !== $receiver->id) {
                abort(403, 'Unauthorized');
            }
            // Check if student is enrolled
            $enrolled = $course->students()->where('student_id', $user->id)->exists();
            if (!$enrolled) {
                abort(403, 'Unauthorized');
            }
        } else {
            // Teacher can only send to enrolled students
            if ($course->created_by !== $user->id) {
                abort(403, 'Unauthorized');
            }
            $enrolled = $course->students()->where('student_id', $receiver->id)->exists();
            if (!$enrolled) {
                abort(403, 'Unauthorized');
            }
        }

        // Create message record
        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'course_id' => $course->id,
            'message' => $request->message,
        ]);

        // Return JSON response for AJAX requests, redirect for regular requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
            ]);
        }

        return redirect()->route('chat.show', [$course->id, $receiver->id])
            ->with('success', 'Message sent successfully');
    }

    public function loadMessages($courseId, $userId)
    {
        $user = Auth::user();
        
        $messages = Message::forConversation($user->id, $userId, $courseId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        
        $unreadCount = Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'count' => $unreadCount,
        ]);
    }
}
