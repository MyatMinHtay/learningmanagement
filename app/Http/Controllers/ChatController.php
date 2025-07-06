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

    public function index()
    {
        $user = Auth::user();
        $conversations = [];

        if ($user->role->role === 'student') {
            // Get courses the student is enrolled in
            $courses = Course::whereHas('students', function ($query) use ($user) {
                $query->where('student_id', $user->id);
            })->with('creator')->get();

            foreach ($courses as $course) {
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
            // Get courses the teacher created
            $courses = Course::where('created_by', $user->id)->get();

            foreach ($courses as $course) {
                $students = User::whereHas('courses', function ($query) use ($course) {
                    $query->where('course_id', $course->id);
                })->get();

                foreach ($students as $student) {
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

    public function show($courseId, $userId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);
        $otherUser = User::findOrFail($userId);

        // Check if user has permission to chat
        if ($user->role->role === 'student') {
            // Check if student is enrolled in the course
            $enrolled = $course->students()->where('student_id', $user->id)->exists();
            if (!$enrolled || $course->created_by !== $otherUser->id) {
                abort(403, 'Unauthorized');
            }
        } else {
            // Check if teacher owns the course and student is enrolled
            if ($course->created_by !== $user->id) {
                abort(403, 'Unauthorized');
            }
            $enrolled = $course->students()->where('student_id', $otherUser->id)->exists();
            if (!$enrolled) {
                abort(403, 'Unauthorized');
            }
        }

        $messages = Message::forConversation($user->id, $otherUser->id, $courseId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('receiver_id', $user->id)
            ->where('sender_id', $otherUser->id)
            ->where('course_id', $courseId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('chat.show', compact('course', 'otherUser', 'messages'));
    }

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

        // Check permissions
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

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'course_id' => $course->id,
            'message' => $request->message,
        ]);

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
