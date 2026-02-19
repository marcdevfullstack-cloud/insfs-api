<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\FeeScheduleController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

// Auth routes (publiques)
Route::post('/auth/login', [AuthController::class, 'login']);

// Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Référentiel (lecture seule pour tous les rôles)
    Route::get('/schools', [SchoolController::class, 'index']);
    Route::get('/academic-years', [SchoolController::class, 'academicYears']);
    Route::get('/academic-years/current', [SchoolController::class, 'currentAcademicYear']);

    // Étudiants (ADMIN + SCOLARITE)
    Route::get('/students', [StudentController::class, 'index']);
    Route::post('/students', [StudentController::class, 'store']);
    Route::get('/students/{student}', [StudentController::class, 'show']);
    Route::put('/students/{student}', [StudentController::class, 'update']);
    Route::delete('/students/{student}', [StudentController::class, 'destroy']);
    Route::post('/students/{student}/photo', [StudentController::class, 'uploadPhoto']);

    // Inscriptions (ADMIN + SCOLARITE)
    Route::get('/enrollments', [EnrollmentController::class, 'index']);
    Route::post('/enrollments', [EnrollmentController::class, 'store']);
    Route::get('/enrollments/{enrollment}', [EnrollmentController::class, 'show']);
    Route::put('/enrollments/{enrollment}', [EnrollmentController::class, 'update']);

    // Paiements
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::post('/payments', [PaymentController::class, 'store']);                         // COMPTABILITE + ADMIN
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy']);           // ADMIN

    // Grille tarifaire
    Route::get('/fee-schedules', [FeeScheduleController::class, 'index']);
    Route::post('/fee-schedules', [FeeScheduleController::class, 'store']);                // ADMIN
    Route::put('/fee-schedules/{feeSchedule}', [FeeScheduleController::class, 'update']); // ADMIN

    // Documents PDF
    Route::get('/documents/certificate/{enrollment}', [DocumentController::class, 'certificate']);
    Route::get('/documents/student-record/{student}', [DocumentController::class, 'studentRecord']);

    // Tableau de bord
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
});