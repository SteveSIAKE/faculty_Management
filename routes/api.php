<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\EnrollmentController;
use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\GradeController;
use App\Http\Controllers\API\ResourceController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\AcademicYearController;
use App\Http\Controllers\API\ExamGradeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Routes publiques
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // Routes Admin uniquement
    Route::middleware('role:admin')->group(function () {
        // Users
        Route::apiResource('users', UserController::class);
        Route::post('users/bulk', [UserController::class, 'bulkStore']);

        // Roles
        Route::apiResource('roles', RoleController::class);
        Route::get('roles/{role}/users', [RoleController::class, 'users']);
        Route::get('roles/{role}/permissions', [RoleController::class, 'permissions']);
        Route::put('roles/{role}/permissions', [RoleController::class, 'updatePermissions']);

        // Departments
        Route::apiResource('departments', DepartmentController::class);
        Route::get('departments/{department}/teachers', [DepartmentController::class, 'teachers']);

        // Academic Years
        Route::apiResource('academic-years', AcademicYearController::class);
        Route::post('academic-years/{academicYear}/set-active', [AcademicYearController::class, 'setActive']);
    });

    // Routes Admin et Professeur
    Route::middleware('role:admin,professor')->group(function () {
        // Courses
        Route::apiResource('courses', CourseController::class);
        Route::get('courses/{course}/students', [CourseController::class, 'students']);
        Route::get('courses/{course}/resources', [CourseController::class, 'resources']);
        Route::get('courses/{course}/grades', [CourseController::class, 'grades']);
        Route::get('courses/{course}/attendance', [CourseController::class, 'attendance']);

        // Enrollments
        Route::apiResource('enrollments', EnrollmentController::class);
        Route::post('enrollments/bulk', [EnrollmentController::class, 'bulkStore']);
        Route::post('enrollments/{enrollment}/approve', [EnrollmentController::class, 'approve']);
        Route::post('enrollments/{enrollment}/reject', [EnrollmentController::class, 'reject']);

        // Attendance
        Route::apiResource('attendance', AttendanceController::class);
        Route::post('attendance/bulk', [AttendanceController::class, 'bulkStore']);

        // Grades
        Route::apiResource('grades', GradeController::class);
        Route::post('grades/bulk', [GradeController::class, 'bulkStore']);

        // Resources
        Route::apiResource('resources', ResourceController::class);
        Route::get('resources/{resource}/download', [ResourceController::class, 'download']);
    });

    // Routes pour tous les utilisateurs authentifiés
    Route::get('users/{user}/courses', [UserController::class, 'courses']);
    Route::get('users/{user}/grades', [UserController::class, 'grades']);
    Route::get('users/{user}/attendance', [UserController::class, 'attendance']);
    Route::get('departments/{department}/courses', [DepartmentController::class, 'courses']);
    Route::get('academic-years/{academicYear}/courses', [AcademicYearController::class, 'courses']);

    // Notifications
    Route::apiResource('notifications', NotificationController::class);
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/{notification}/unread', [NotificationController::class, 'markAsUnread']);
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Routes pour les notes d'examens
    Route::prefix('exam-grades')->group(function () {
        Route::get('/', [ExamGradeController::class, 'index']);
        Route::post('/', [ExamGradeController::class, 'store']);
        Route::post('/bulk', [ExamGradeController::class, 'bulkStore']);
        Route::get('/{examGrade}', [ExamGradeController::class, 'show']);
        Route::put('/{examGrade}', [ExamGradeController::class, 'update']);
        Route::delete('/{examGrade}', [ExamGradeController::class, 'destroy']);
        Route::get('/student/{student}', [ExamGradeController::class, 'getStudentGrades']);
        Route::get('/exam/{exam}', [ExamGradeController::class, 'getExamGrades']);
    });

    // Routes pour les présences
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::post('/attendance', [AttendanceController::class, 'store']);
    Route::post('/attendance/bulk', [AttendanceController::class, 'bulkStore']);
    Route::get('/attendance/{attendance}', [AttendanceController::class, 'show']);
    Route::put('/attendance/{attendance}', [AttendanceController::class, 'update']);
    Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy']);
    
    // Routes pour les statistiques
    Route::get('/attendance/stats', [AttendanceController::class, 'getAttendanceStats']);
    Route::get('/attendance/student/{studentId}', [AttendanceController::class, 'getStudentAttendance']);
    Route::get('/attendance/course/{courseId}', [AttendanceController::class, 'getCourseAttendance']);
});

// Routes pour les années académiques
Route::prefix('academic-years')->group(function () {
    Route::get('/', [AcademicYearController::class, 'index']);
    Route::post('/', [AcademicYearController::class, 'store']);
    Route::get('/active', [AcademicYearController::class, 'getActive']);
    Route::get('/{academicYear}', [AcademicYearController::class, 'show']);
    Route::put('/{academicYear}', [AcademicYearController::class, 'update']);
    Route::delete('/{academicYear}', [AcademicYearController::class, 'destroy']);
    Route::post('/{academicYear}/activate', [AcademicYearController::class, 'setActive']);
    Route::get('/{academicYear}/courses', [AcademicYearController::class, 'getCourses']);
    Route::get('/{academicYear}/departments', [AcademicYearController::class, 'getDepartments']);
});

// Routes pour les départements
Route::prefix('departments')->group(function () {
    Route::get('/', [DepartmentController::class, 'index']);
    Route::post('/', [DepartmentController::class, 'store']);
    Route::post('/bulk', [DepartmentController::class, 'bulkStore']);
    Route::get('/{department}', [DepartmentController::class, 'show']);
    Route::put('/{department}', [DepartmentController::class, 'update']);
    Route::delete('/{department}', [DepartmentController::class, 'destroy']);
    Route::get('/{department}/teachers', [DepartmentController::class, 'getTeachers']);
    Route::get('/{department}/courses', [DepartmentController::class, 'getCourses']);
    Route::get('/{department}/students', [DepartmentController::class, 'getStudents']);
});

// Routes pour les rôles
Route::prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/', [RoleController::class, 'store']);
    Route::get('/{role}', [RoleController::class, 'show']);
    Route::put('/{role}', [RoleController::class, 'update']);
    Route::delete('/{role}', [RoleController::class, 'destroy']);
    Route::get('/{role}/users', [RoleController::class, 'users']);
    Route::get('/{role}/permissions', [RoleController::class, 'permissions']);
    Route::put('/{role}/permissions', [RoleController::class, 'updatePermissions']);
}); 