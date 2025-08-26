<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    HomeController,
    UserController,
    DashboardController,
    SystemRoleController,
    GlobalSearchController,
    CourseController,
    CourseCategoryController,
    LessonController,
    QuizController,
    ForgotPasswordController,
    AssignmentController,
    NotificationController,
    ChatController
};

// ========================================
// PUBLIC PAGES
// ========================================
Route::get('/', [AuthController::class, 'index'])->name('home');
Route::get('/home', [AuthController::class, 'index'])->name('homeslug');
Route::get('/about', [HomeController::class, 'showabout'])->name('about');
Route::get('/contact', [HomeController::class, 'showContact'])->name('contact');
Route::get('/team', [HomeController::class, 'showTeam'])->name('team');
Route::get('/testimonial', [HomeController::class, 'showTestimonial'])->name('testimonial');
Route::get('/search', [GlobalSearchController::class, 'globalSearch'])->name('search');

// Public courses
Route::get('/courses', [CourseController::class, 'index'])->name('courses');
Route::get('/courses/{course:id}', [CourseController::class, 'show'])->name('courses.show');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'create'])->name('register');
    Route::post('/register', [UserController::class, 'createuser'])->name('postregister');
    Route::get('/studentlogin', [AuthController::class, 'login'])->name('login');
    Route::get('/adminlogin', [AuthController::class, 'adminlogin'])->name('admin.login');
    Route::get('teacherlogin',[AuthController::class,'teacherlogin'])->name('teacher.login');
    Route::post('/login', [AuthController::class, 'postLogin'])->name('page.login');
});

Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth');

// ========================================
// STUDENT ROUTES
// ========================================
Route::middleware(['auth', 'admincheck:students'])->group(function () {
    // Student Dashboard
    Route::get('/students/dashboard', [DashboardController::class, 'showStudentDashboard'])->name('students.dashboard');
    Route::get('/student/{student:id}/courses', [CourseController::class, 'showStudentCourses'])->name('student.courses');
    Route::get('/student/{student:id}/quizzes', [QuizController::class, 'index'])->name('student.quizzes');
    Route::get('/student/quiz/{quiz}/result', [QuizController::class, 'adminresult'])->name('student.quiz.result');
    
    // Course Lessons
    Route::get('/courses/{course}/lessons', [CourseController::class, 'showLessons'])->name('showlesson');
    
    // Quiz Taking
    Route::get('course/{course}/quizzes/{quiz}/start', [QuizController::class, 'start'])->name('quiz.start');
    Route::post('/quiz/{quiz}/submit', [QuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz/{quiz}/result', [QuizController::class, 'result'])->name('quiz.result');

    Route::prefix('student')->group(function(){
        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('student.notifications.index');
        
        // Student Chat Routes
        Route::get('/chat', [ChatController::class, 'index'])->name('student.chat.index');
        Route::get('/chat/{course}/{user}', [ChatController::class, 'show'])->name('student.chat.show');
    });
});

// ========================================
// AUTHENTICATED USER ROUTES (Students & Teachers)
// ========================================
Route::middleware('auth')->group(function () {
    // Profile Management
    Route::get('/profile/{user:username}', [HomeController::class, 'showprofile'])->name('profile.show');
    Route::get('/profile/{user:id}/edit', [AuthController::class, 'editprofile'])->name('profile.edit');
    Route::post('/profile/{user:id}/edit', [AuthController::class, 'updateprofile'])->name('profile.update');
    
    // Course Enrollment
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enrollJson']);
    
    // Assignment Management
    Route::get('/student/assignments/', [AssignmentController::class, 'showAssignments'])->name('student.assignments.index');
    Route::get('/teacher/assignments/', [AssignmentController::class, 'showAssignments'])->name('assignments.index');
    Route::get('/teacher/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/course/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::get('/assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
    Route::patch('/assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
    Route::patch('/assignments/{assignment}/status', [AssignmentController::class, 'updateStatus'])->name('assignments.updateStatus');
    
    // Notifications
    
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/recent', [NotificationController::class, 'getRecentNotifications'])->name('notifications.recent');
    
    // Chat System
    
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/{course}/{user}/messages', [ChatController::class, 'loadMessages'])->name('chat.messages');
    Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unread-count');
});

// ========================================
// TEACHER ROUTES
// ========================================
Route::middleware(['auth', 'admincheck:teachers'])->group(function () {
    // Course Management
    Route::prefix('teacher/courses')->group(function () {
        Route::get('/', [CourseController::class, 'adminindex'])->name('teachercourses');
        Route::get('/create', [CourseController::class, 'create'])->name('courses.create');
        Route::post('/', [CourseController::class, 'store'])->name('courses.store');
        Route::get('/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
        Route::put('/{course}', [CourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    });
    
    // Course Categories Management
    Route::prefix('teacher/categories')->group(function () {
        Route::get('/', [CourseCategoryController::class, 'index'])->name('categories.index');
        Route::get('/create', [CourseCategoryController::class, 'create'])->name('categories.create');
        Route::post('/', [CourseCategoryController::class, 'store'])->name('categories.store');
        Route::get('/{category}/edit', [CourseCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/{category}', [CourseCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/{category}', [CourseCategoryController::class, 'destroy'])->name('categories.destroy');
    });
    
    // Lesson Management
    Route::prefix('teacher/lessons')->group(function () {
        Route::get('/', [LessonController::class, 'index'])->name('lessons.index');
        Route::get('/create', [LessonController::class, 'create'])->name('lessons.create');
        Route::post('/', [LessonController::class, 'store'])->name('lessons.store');
        Route::get('/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
        Route::put('/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
        Route::delete('/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');
    });
    
    // Quiz Management
    Route::prefix('teacher/quizzes')->group(function () {
        Route::get('/', [QuizController::class, 'adminindex'])->name('quizzes.index');
        Route::get('/create', [QuizController::class, 'create'])->name('quizzes.create');
        Route::post('/', [QuizController::class, 'store'])->name('quizzes.store');
        Route::get('/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
        Route::put('/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');
        Route::delete('/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
    });
    

    Route::prefix('teacher')->group(function(){
        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

        // Teacher Chat Route
        Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/{course}/{user}', [ChatController::class, 'show'])->name('chat.show');

        // Teacher Notifications (Deadline creation)
        Route::get('/notifications/create-deadline', [NotificationController::class, 'createDeadlineForm'])->name('notifications.create-deadline');
        Route::post('/notifications/create-deadline', [NotificationController::class, 'storeDeadlineNotification'])->name('notifications.store-deadline');
    });
    
});

// ========================================
// ADMIN ROUTES
// ========================================
Route::middleware(['auth', 'admincheck:users'])->group(function () {
    // Admin Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'show'])->name('admin.dashboard');
    Route::get('/teacher/dashboard', [DashboardController::class, 'show'])->name('teacher.dashboard');
    Route::get('/student/dashboard', [DashboardController::class, 'show'])->name('student.dashboard');
    Route::get('/students/{student}/courses', [CourseController::class, 'showStudentCourses']);
    Route::get('/admin/analytics', [DashboardController::class, 'showAnalytics'])->name('admin.analytics');
    
    // User Management
    Route::prefix('admin/users')->middleware('admincheck:admins')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users');
        Route::post('/create', [UserController::class, 'createuser'])->middleware('admincheck:roles');
        Route::post('/update/{user:username}', [UserController::class, 'updateuser'])->middleware('admincheck:roles')->where('username', '[A-Za-z0-9_\-]+')->name('admin.users.update');
        Route::get('/edit/{user:username}', [UserController::class, 'edituser'])->middleware('admincheck:roles')->where('username', '[A-Za-z0-9_\-]+');
        Route::get('/lock/{user:username}', [UserController::class, 'lockuser'])->middleware('admincheck:roles')->where('username', '[A-Za-z0-9_\-]+');
        Route::get('/unlock/{user:username}', [UserController::class, 'unlockuser'])->middleware('admincheck:roles')->where('username', '[A-Za-z0-9_\-]+');
        Route::get('/delete/{user:username}', [UserController::class, 'deleteuser'])->middleware('admincheck:roles')->where('username', '[A-Za-z0-9_\-]+');
    });
    
    // System Roles Management
    Route::prefix('admin/roles')->middleware('admincheck:roles')->group(function () {
        Route::get('/', [SystemRoleController::class, 'index'])->name('roles');
        Route::post('/create', [SystemRoleController::class, 'createrole']);
        Route::post('/update/{role:role}', [SystemRoleController::class, 'updaterole'])->where('role', '[A-z\d\-_]+');
        Route::get('/edit/{role:role}', [SystemRoleController::class, 'editrole'])->where('role', '[A-z\d\-_]+');
        Route::get('/delete/{role:role}', [SystemRoleController::class, 'deleterole'])->where('role', '[A-z\d\-_]+');
    });
    
    // Admin Report Tables
    Route::prefix('admin/reports')->middleware('admincheck:admins')->group(function () {
        Route::get('/courses', [CourseController::class, 'reportTable'])->name('admin.reports.courses');
        Route::get('/courses/export-pdf', [CourseController::class, 'exportPDF'])->name('admin.reports.courses.pdf');
        Route::get('/quizzes', [QuizController::class, 'reportTable'])->name('admin.reports.quizzes');
        Route::get('/quizzes/export-pdf', [QuizController::class, 'exportPDF'])->name('admin.reports.quizzes.pdf');
        Route::get('/quiz-submissions', [QuizController::class, 'submissionReportTable'])->name('admin.reports.quiz-submissions');
        Route::get('/quiz-submissions/export-pdf', [QuizController::class, 'exportSubmissionsPDF'])->name('admin.reports.quiz-submissions.pdf');
        Route::get('/assignments', [AssignmentController::class, 'reportTable'])->name('admin.reports.assignments');
        Route::get('/assignments/export-pdf', [AssignmentController::class, 'exportPDF'])->name('admin.reports.assignments.pdf');
        Route::get('/assignment-submissions', [AssignmentController::class, 'submissionReportTable'])->name('admin.reports.assignment-submissions');
        Route::get('/assignment-submissions/export-pdf', [AssignmentController::class, 'exportSubmissionsPDF'])->name('admin.reports.assignment-submissions.pdf');
    });
});
