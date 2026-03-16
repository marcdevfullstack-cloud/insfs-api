<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Mail\BienvenuPortailMail;
use App\Models\AdmittedStudent;
use App\Models\EnrollmentApplication;
use App\Models\PortalUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class PortalAuthController extends Controller
{
    /**
     * Vérifier si un étudiant est dans la liste des admis.
     */
    public function checkEligibility(Request $request): JsonResponse
    {
        $request->validate([
            'last_name'     => 'required|string|max:255',
            'first_name'    => 'required|string|max:255',
            'date_of_birth' => 'required|date',
        ]);

        $admitted = AdmittedStudent::whereRaw('LOWER(last_name) = ?', [strtolower(trim($request->last_name))])
            ->whereRaw('LOWER(first_name) = ?', [strtolower(trim($request->first_name))])
            ->whereDate('date_of_birth', $request->date_of_birth)
            ->first();

        if (!$admitted) {
            return response()->json([
                'found'              => false,
                'message'            => 'Aucun admis correspondant à ces informations. Vérifiez vos nom, prénom et date de naissance.',
            ]);
        }

        $alreadyRegistered = PortalUser::where('admitted_student_id', $admitted->id)->exists();

        return response()->json([
            'found'               => true,
            'already_registered'  => $alreadyRegistered,
            'admitted_student_id' => $admitted->id,
            'school'              => $admitted->school?->name,
            'academic_year'       => $admitted->academicYear?->label,
            'entry_mode'          => $admitted->entry_mode,
            'year_of_study'       => $admitted->year_of_study,
        ]);
    }

    /**
     * Créer un compte étudiant sur le portail.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'admitted_student_id' => 'required|uuid|exists:admitted_students,id',
            'email'               => 'required|email|unique:portal_users,email',
            'password'            => 'required|string|min:8|confirmed',
            'phone'               => 'required|string|max:20',
        ]);

        $admitted = AdmittedStudent::with(['school', 'academicYear'])->findOrFail($request->admitted_student_id);

        if (PortalUser::where('admitted_student_id', $admitted->id)->exists()) {
            throw ValidationException::withMessages([
                'admitted_student_id' => 'Un compte existe déjà pour cet admis.',
            ]);
        }

        $portalUser = PortalUser::create([
            'admitted_student_id' => $admitted->id,
            'last_name'           => $admitted->last_name,
            'first_name'          => $admitted->first_name,
            'date_of_birth'       => $admitted->date_of_birth,
            'email'               => $request->email,
            'password'            => $request->password,
            'phone'               => $request->phone,
        ]);

        // Créer le dossier vide (brouillon)
        $application = EnrollmentApplication::create([
            'portal_user_id'      => $portalUser->id,
            'admitted_student_id' => $admitted->id,
            'academic_year_id'    => $admitted->academic_year_id,
            'last_name'           => $admitted->last_name,
            'first_name'          => $admitted->first_name,
            'status'              => 'BROUILLON',
        ]);

        // Marquer l'admis comme inscrit
        $admitted->update(['status' => 'INSCRIT']);

        // Envoyer email de bienvenue
        try {
            Mail::to($portalUser->email)->send(new BienvenuPortailMail($portalUser));
        } catch (\Exception) {
            // Silencieux — ne pas bloquer l'inscription si le mail échoue
        }

        $token = $portalUser->createToken('portal-token')->plainTextToken;

        return response()->json([
            'token'       => $token,
            'user'        => $portalUser,
            'application' => $application->load(['admittedStudent.school', 'admittedStudent.academicYear']),
        ], 201);
    }

    /**
     * Connexion étudiant.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $portalUser = PortalUser::where('email', $request->email)->first();

        if (!$portalUser || !Hash::check($request->password, $portalUser->password)) {
            throw ValidationException::withMessages([
                'email' => 'Email ou mot de passe incorrect.',
            ]);
        }

        $token = $portalUser->createToken('portal-token')->plainTextToken;

        $application = $portalUser->application()->with([
            'admittedStudent.school',
            'admittedStudent.academicYear',
            'documents',
            'signatures',
        ])->first();

        return response()->json([
            'token'       => $token,
            'user'        => $portalUser,
            'application' => $application,
        ]);
    }

    /**
     * Déconnexion.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté.']);
    }

    /**
     * Profil courant.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $application = $user->application()->with([
            'admittedStudent.school',
            'admittedStudent.academicYear',
            'documents',
            'signatures',
        ])->first();

        return response()->json([
            'user'        => $user,
            'application' => $application,
        ]);
    }
}
