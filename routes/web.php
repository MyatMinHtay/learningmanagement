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
    LessonController,
    QuizController,
    ForgotPasswordController,
    AssignmentController,
    NotificationController
};

// ========================================
// Public Pages
// ========================================
Route::get('/', [AuthController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'showabout'])->name('about');
Route::get('/contact', [HomeController::class, 'showContact'])->name('contact');
Route::get('/team', [HomeController::class, 'showTeam'])->name('team');
Route::get('/testimonial', [HomeController::class, 'showTestimonial'])->name('testimonial');
Route::get('/search', [GlobalSearchController::class, 'globalSearch'])->name('search');

// ========================================
// Authentication
// ========================================
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'create'])->name('register');
    Route::post('/register', [UserController::class, 'createuser'])->name('postregister');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'postLogin'])->name('page.login');
});

Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth');

// ========================================
// Profile
// ========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile/{user:username}', [HomeController::class, 'showprofile'])->name('profile.show');
    Route::get('/profile/{user:id}/edit', [AuthController::class, 'editprofile'])->name('profile.edit');
    Route::post('/profile/{user:id}/edit', [AuthController::class, 'updateprofile'])->name('profile.update');
});

// ========================================
// Admin Dashboard
// ========================================
Route::get('/admin/dashboard', [DashboardController::class, 'show'])->middleware('admincheck:users');
Route::get('/students/{student}/courses', [CourseController::class, 'showStudentCourses'])->middleware('admincheck:students');
// ========================================
// Admin User Management
// ========================================
Route::prefix('admin/users')->middleware('admincheck:admins')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users');
    Route::post('/create', [UserController::class, 'createuser'])->middleware('admincheck:roles');
    Route::post('/update/{user:username}', [UserController::class, 'updateuser'])->middleware('admincheck:roles')->where('username', '[A-Za-z0-9_\-]+')->name('admin.users.update');
    Route::get('/edit/{user:username}', [UserController::class, 'edituser'])->middleware('admincheck:roles')->where('username', '[A-Za-z0-9_\-]+');
    Route::get('/lock/{user:username}', [UserController::class, 'lockuser'])->middleware('admincheck:roles')->where('username', '[A-Za-z0-9_\-]+');
    Route::get('/unlock/{user:username}', [UserController::class, 'unlockuser'])->middleware('admincheck:roles')->where('username', '[A-Za-z0-9_\-]+');
    Route::get('/delete/{user:username}', [UserController::class, 'deleteuser'])->middleware('admincheck:roles')->where('username', '[A-Za-z0-9_\-]+');
});

// ========================================
// Admin System Roles
// ========================================
Route::prefix('admin/roles')->middleware('admincheck:roles')->group(function () {
    Route::get('/', [SystemRoleController::class, 'index'])->name('roles');
    Route::post('/create', [SystemRoleController::class, 'createrole']);
    Route::post('/update/{role:role}', [SystemRoleController::class, 'updaterole'])->where('role', '[A-z\d\-_]+');
    Route::get('/edit/{role:role}', [SystemRoleController::class, 'editrole'])->where('role', '[A-z\d\-_]+');
    Route::get('/delete/{role:role}', [SystemRoleController::class, 'deleterole'])->where('role', '[A-z\d\-_]+');
});

// ========================================
// Courses
// ========================================

// Public courses
Route::get('/courses', [CourseController::class, 'index'])->name('courses');
Route::get('/courses/{course:id}', [CourseController::class, 'show'])->name('courses.show');
Route::post('/courses/{course}/enroll', [CourseController::class, 'enrollJson'])->middleware('auth');
Route::get('/courses/{course}/lessons', [CourseController::class, 'showLessons'])->middleware('auth')->name('showlesson');

// Admin courses
Route::prefix('admin/courses')->middleware('admincheck:teachers')->group(function () {
    Route::get('/', [CourseController::class, 'adminindex'])->name('admincourses');
    Route::get('/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
});


// ========================================
// Lessons (Admin)
// ========================================
Route::prefix('admin/lessons')->middleware('admincheck:teachers')->group(function () {
    Route::get('/', [LessonController::class, 'index'])->name('lessons.index');
    Route::get('/create', [LessonController::class, 'create'])->name('lessons.create');
    Route::post('/', [LessonController::class, 'store'])->name('lessons.store');
    Route::get('/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');
});

// ========================================
// Student Dashboard
// ========================================
Route::get('/students/dashboard', [DashboardController::class, 'showStudentDashboard'])->middleware('admincheck:students')->name('students.dashboard');
Route::get('/student/{student:id}/courses', [CourseController::class, 'showStudentCourses'])->middleware('admincheck:students')->name('student.courses');
Route::get('/student/{student:id}/quizzes', [QuizController::class, 'index'])->middleware('admincheck:students')->name('student.quizzes');
Route::get('/student/quiz/{quiz}/result', [QuizController::class, 'adminresult'])->middleware('admincheck:students')->name('student.quiz.result');




// ========================================
// Assignments
// ========================================
Route::middleware('auth')->group(function () {
    Route::get('/admin/assignments/', [AssignmentController::class, 'showAssignments'])->name('assignments.index');
    Route::get('/admin/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/course/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::get('/assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
    Route::patch('/assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
    Route::patch('/assignments/{assignment}/status', [AssignmentController::class, 'updateStatus'])
    ->middleware('auth')
    ->name('assignments.updateStatus');

});

// ========================================
// Quizzes
// ========================================

// Quiz actions for authenticated users
Route::middleware('auth')->group(function () {
    Route::get('course/{course}/quizzes/{quiz}/start', [QuizController::class, 'start'])->name('quiz.start');
    Route::post('/quiz/{quiz}/submit', [QuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz/{quiz}/result', [QuizController::class, 'result'])->name('quiz.result');
});

// Admin quizzes
Route::prefix('admin/quizzes')->middleware('admincheck:teachers')->group(function () {
    Route::get('/', [QuizController::class, 'adminindex'])->name('quizzes.index');
    Route::get('/create', [QuizController::class, 'create'])->name('quizzes.create');
    Route::post('/', [QuizController::class, 'store'])->name('quizzes.store');
    Route::get('/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');
    Route::delete('/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
});

// ========================================
// Notifications
// ========================================
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    
    // For teachers to create deadline notifications
    Route::get('/notifications/create-deadline', [NotificationController::class, 'createDeadlineForm'])->name('notifications.create-deadline');
    Route::post('/notifications/create-deadline', [NotificationController::class, 'storeDeadlineNotification'])->name('notifications.store-deadline');
});
