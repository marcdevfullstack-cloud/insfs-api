<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\FeeScheduleController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AdmittedStudentController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Portal\PortalAuthController;
use App\Http\Controllers\Portal\PortalApplicationController;
use App\Http\Controllers\Portal\PortalDocumentController;
use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════════════════════
// PORTAIL ÉTUDIANT — Routes publiques
// ═══════════════════════════════════════════════════════
Route::prefix('portal')->group(function () {
    Route::post('/check-eligibility', [PortalAuthController::class, 'checkEligibility']);
    Route::post('/register',          [PortalAuthController::class, 'register']);
    Route::post('/login',             [PortalAuthController::class, 'login']);
});

// PORTAIL ÉTUDIANT — Routes protégées (étudiant authentifié)
Route::prefix('portal')->middleware(['auth:sanctum', 'portal.user'])->group(function () {
    Route::post('/logout', [PortalAuthController::class, 'logout']);
    Route::get('/me',      [PortalAuthController::class, 'me']);

    // Dossier
    Route::get('/application',                              [PortalApplicationController::class, 'show']);
    Route::put('/application/renseignements',               [PortalApplicationController::class, 'saveRenseignements']);
    Route::post('/application/photo',                       [PortalApplicationController::class, 'uploadPhoto']);
    Route::post('/application/documents',                   [PortalApplicationController::class, 'uploadDocument']);
    Route::delete('/application/documents/{document}',      [PortalApplicationController::class, 'deleteDocument']);
    Route::put('/application/engagement',                   [PortalApplicationController::class, 'saveEngagement']);
    Route::put('/application/signature-renseignements',     [PortalApplicationController::class, 'saveSignatureRenseignements']);
    Route::post('/application/submit',                      [PortalApplicationController::class, 'submit']);

    // Documents finaux
    Route::get('/documents',                [PortalDocumentController::class, 'index']);
    Route::get('/documents/{type}/download', [PortalDocumentController::class, 'download']);
});

// ═══════════════════════════════════════════════════════
// ADMIN — Auth routes (publiques)
// ═══════════════════════════════════════════════════════
// Auth routes (publiques)
Route::post('/auth/login', [AuthController::class, 'login']);

// Routes protégées par Sanctum
Route::middleware(['auth:sanctum', 'admin.user'])->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Référentiel
    Route::get('/schools', [SchoolController::class, 'index']);
    Route::get('/academic-years', [SchoolController::class, 'academicYears']);
    Route::get('/academic-years/current', [SchoolController::class, 'currentAcademicYear']);
    Route::post('/academic-years', [SchoolController::class, 'storeAcademicYear']);   // ADMIN
    Route::patch('/academic-years/{academicYear}/set-current', [SchoolController::class, 'setCurrentAcademicYear']); // ADMIN

    // Étudiants (ADMIN + SCOLARITE)
    Route::get('/students', [StudentController::class, 'index']);
    Route::post('/students', [StudentController::class, 'store']);
    Route::get('/students/{student}', [StudentController::class, 'show']);
    Route::put('/students/{student}', [StudentController::class, 'update']);
    Route::delete('/students/{student}', [StudentController::class, 'destroy']);
    Route::post('/students/{student}/photo', [StudentController::class, 'uploadPhoto']);
    Route::patch('/students/{student}/block', [StudentController::class, 'block']);     // ADMIN
    Route::patch('/students/{student}/unblock', [StudentController::class, 'unblock']); // ADMIN

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
    Route::get('/documents/receipt/{payment}', [DocumentController::class, 'receipt']);

    // Tableau de bord
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Admis (import + gestion)
    Route::get('/admitted-students/template',        [AdmittedStudentController::class, 'downloadTemplate']);
    Route::post('/admitted-students/import',         [AdmittedStudentController::class, 'import']);
    Route::get('/admitted-students',                 [AdmittedStudentController::class, 'index']);
    Route::post('/admitted-students',                [AdmittedStudentController::class, 'store']);
    Route::delete('/admitted-students/{admittedStudent}', [AdmittedStudentController::class, 'destroy']);

    // Dossiers entrants (côté scolarité)
    Route::get('/applications',                                          [ApplicationController::class, 'index']);
    Route::get('/applications/{application}',                            [ApplicationController::class, 'show']);
    Route::patch('/applications/{application}/start-processing',         [ApplicationController::class, 'startProcessing']);
    Route::patch('/applications/{application}/validate',                 [ApplicationController::class, 'validate']);
    Route::patch('/applications/{application}/request-correction',       [ApplicationController::class, 'requestCorrection']);
    Route::patch('/applications/{application}/reject',                   [ApplicationController::class, 'reject']);
    Route::post('/applications/{application}/complete',                  [ApplicationController::class, 'complete']);
    Route::get('/applications/{application}/documents/{type}/download',  [ApplicationController::class, 'downloadDocument']);
});