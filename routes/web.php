<?php

use App\Models\SystemRole;
use App\Models\CompletionReport;
use App\Models\StudentInformation;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SystemRoleController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\LessonController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('/', [AuthController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'showabout'])->name('about');

Route::get('/contact', [HomeController::class, 'showContact'])->name('contact');

Route::get('/team', [HomeController::class, 'showTeam'])->name('team');
Route::get('/testimonial', [HomeController::class, 'showTestimonial'])->name('testimonial');

Route::get('/search', [GlobalSearchController::class, 'globalSearch'])->name('search');

Route::get('/register', [AuthController::class, 'create'])->name('register');
Route::post('/register', [UserController::class, 'createuser'])->name('postregister');

//Admin Dashboard
Route::get('/admin/dashboard', [DashboardController::class, 'show'])->middleware('admincheck:users');

// Route::get('/preview-pdf/{filename}', [PDFController::class, 'previewPDF'])->name('preview.pdf');


//Admin User Management 

Route::get('/admin/users', [UserController::class, 'index'])->middleware('admincheck:admins')->name('users');
Route::post('/admin/users/create', [UserController::class, 'createuser'])->middleware("admincheck:roles");
Route::post('/admin/users/update/{user:username}', [UserController::class, 'updateuser'])->where('username', '[A-Za-z0-9_\-]+')->middleware('admincheck:roles')->name('admin.users.update');
Route::get('/admin/users/edit/{user:username}', [UserController::class, 'edituser'])->where('username', '[A-Za-z0-9_\-]+')->middleware('admincheck:roles');
Route::get('/admin/users/lock/{user:username}', [UserController::class, 'lockuser'])->where('username', '[A-Za-z0-9_\-]+')->middleware('admincheck:roles');
Route::get('/admin/users/unlock/{user:username}', [UserController::class, 'unlockuser'])->where('username', '[A-Za-z0-9_\-]+')->middleware('admincheck:roles');
Route::get('/admin/users/delete/{user:username}', [UserController::class, 'deleteuser'])->where('username', '[A-Za-z0-9_\-]+')->middleware('admincheck:roles');

Route::get('/login', [AuthController::class, 'login'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'postLogin'])->middleware('guest')->name('page.login');
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth');

//Profile 
Route::get('/profile/{user:username}', [HomeController::class, 'showprofile'])->where('user', '[A-z\d\-_]+')->middleware('auth');
Route::post('/editprofile', [AuthController::class, 'updateprofile'])->middleware('auth');


//Admin System Roles 

Route::get('/admin/roles', [SystemRoleController::class, 'index'])->middleware('admincheck:roles')->name('roles');
Route::post('/admin/roles/create', [SystemRoleController::class, 'createrole'])->middleware("admincheck:roles");
Route::post('/admin/roles/update/{role:role}', [SystemRoleController::class, 'updaterole'])->where('role', '[A-z\d\-_]+')->middleware('admincheck:roles');
Route::get('/admin/roles/edit/{role:role}', [SystemRoleController::class, 'editrole'])->where('role', '[A-z\d\-_]+')->middleware('admincheck:roles');
Route::get('/admin/roles/delete/{role:role}', [SystemRoleController::class, 'deleterole'])->where('role', '[A-z\d\-_]+')->middleware('admincheck:roles');




//Course Create
Route::get('/courses', [CourseController::class, 'index'])->name('courses');
Route::get('/admin/courses', [CourseController::class, 'adminindex'])->middleware('admincheck:courses')->name('admincourses');
Route::get('/courses/{course:id}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/admin/courses/create', [CourseController::class, 'create'])->middleware('admincheck:courses')->name('courses.create');
Route::post('/admin/courses', [CourseController::class, 'store'])->middleware('admincheck:courses')->name('courses.store');
Route::get('/admin/courses/{course}/edit', [CourseController::class, 'edit'])->middleware('admincheck:courses')->name('courses.edit');
Route::put('/admin/courses/{course}', [CourseController::class, 'update'])->middleware('admincheck:courses')->name('courses.update');
Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');



//Start Student Information 

Route::get('/students/dashboard', [DashboardController::class, 'showStudentDashboard'])->middleware('admincheck:students')->name('students.dashboard');


//Quiz Start 

// Public quiz listing or viewing (if needed)
Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes');

// Admin-only quiz management
Route::get('/admin/quizzes', [QuizController::class, 'adminindex'])
    ->middleware('admincheck:teachers')->name('quizzes.index');

Route::get('/quizzes/{quiz:id}', [QuizController::class, 'show'])->name('quizzes.show');

Route::get('/admin/quizzes/create', [QuizController::class, 'create'])
    ->middleware('admincheck:teachers')->name('quizzes.create');

Route::post('/admin/quizzes', [QuizController::class, 'store'])
    ->middleware('admincheck:teachers')->name('quizzes.store');

Route::get('/admin/quizzes/{quiz}/edit', [QuizController::class, 'edit'])
    ->middleware('admincheck:teachers')->name('quizzes.edit');

Route::put('/admin/quizzes/{quiz}', [QuizController::class, 'update'])
    ->middleware('admincheck:teachers')->name('quizzes.update');

Route::delete('/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');

//Lesson Start 
// Admin-only lesson management
Route::get('/admin/lessons', [LessonController::class, 'index'])
    ->middleware('admincheck:teachers')->name('lessons.index');

Route::get('/admin/lessons/create', [LessonController::class, 'create'])
    ->middleware('admincheck:teachers')->name('lessons.create');

Route::post('/admin/lessons', [LessonController::class, 'store'])
    ->middleware('admincheck:teachers')->name('lessons.store');

Route::get('/admin/lessons/{lesson}/edit', [LessonController::class, 'edit'])
    ->middleware('admincheck:teachers')->name('lessons.edit');

Route::put('/admin/lessons/{lesson}', [LessonController::class, 'update'])
    ->middleware('admincheck:teachers')->name('lessons.update');

Route::delete('/admin/lessons/{lesson}', [LessonController::class, 'destroy'])
    ->name('lessons.destroy');



//Lesson End
