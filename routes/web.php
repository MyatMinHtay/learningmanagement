<?php

use App\Models\SystemRole;
use App\Models\CompletionReport;
use App\Models\StudentInformation;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FirstFormController;
use App\Http\Controllers\MtcCourseController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\SecondFormController;
use App\Http\Controllers\SystemRoleController;
use App\Http\Controllers\AdditionalFormController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\CompletionReportController;
use App\Http\Controllers\StudentInformationController;

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
Route::get('/courses', [HomeController::class, 'showcourses'])->name('courses');


Route::get('/register', [AuthController::class, 'create'])->name('register');
Route::post('/register', [UserController::class, 'createuser'])->name('postregister');

//Admin Dashboard
Route::get('/admin/dashboard', [DashboardController::class, 'show'])->middleware('admincheck:dashboard');

// Route::get('/preview-pdf/{filename}', [PDFController::class, 'previewPDF'])->name('preview.pdf');


//Admin User Management 

Route::get('/admin/users', [UserController::class, 'index'])->middleware('admincheck:users')->name('users');
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
Route::get('/createcourse', [CoursesController::class, 'index'])->middleware('admincheck:user')->name('createcourse');
Route::post('/course/create', [CoursesController::class, 'createcourse'])->middleware('admincheck:user')->name('courses');
Route::get('{course:id}/editcourse', [CoursesController::class, 'editcourse'])->middleware('admincheck:user')->name('editcourse');
Route::post('{course:id}/course/update', [CoursesController::class, 'updatecourse'])->middleware('admincheck:user')->name('updatecourse');



Route::get('/student-search', [StudentInformationController::class, 'search'])->name('student.search');






//Start Student Information 

Route::get('/students', [StudentInformationController::class, 'index'])->middleware('admincheck:students')->name('students');
Route::get('/students/create', [StudentInformationController::class, 'createstudent'])->middleware('admincheck:students')->name('students.create');
Route::post('/students/create', [StudentInformationController::class, 'storestudent'])->middleware("admincheck:students")->name('students.store');
Route::post('/students/update/{student:id}', [StudentInformationController::class, 'updatestudent'])->middleware('admincheck:students')->name('students.update');
Route::get('/students/edit/{student:id}', [StudentInformationController::class, 'editstudent'])->middleware('admincheck:students')->name('students.edit');
Route::get('/students/delete/{student:id}', [StudentInformationController::class, 'destorystudent'])->middleware('admincheck:students')->name('students.delete');


