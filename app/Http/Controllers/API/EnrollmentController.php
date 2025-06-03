<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollmentRequest;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::query()->with(['student', 'course']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('academic_year_id')) {
            $query->whereHas('course', function($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);
            });
        }

        $enrollments = $query->paginate(10);

        return response()->json($enrollments);
    }

    public function store(EnrollmentRequest $request)
    {
        // Vérifier si l'étudiant est déjà inscrit au cours
        $existingEnrollment = Enrollment::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'message' => 'L\'étudiant est déjà inscrit à ce cours'
            ], 422);
        }

        // Vérifier si le cours a atteint son nombre maximum d'étudiants
        $course = Course::findOrFail($request->course_id);
        if ($course->max_students && $course->enrollments()->count() >= $course->max_students) {
            return response()->json([
                'message' => 'Le cours a atteint son nombre maximum d\'étudiants'
            ], 422);
        }

        $enrollment = Enrollment::create($request->validated());

        return response()->json([
            'message' => 'Inscription créée avec succès',
            'enrollment' => $enrollment->load(['student', 'course'])
        ], 201);
    }

    public function show(Enrollment $enrollment)
    {
        return response()->json([
            'enrollment' => $enrollment->load(['student', 'course'])
        ]);
    }

    public function update(EnrollmentRequest $request, Enrollment $enrollment)
    {
        $enrollment->update($request->validated());

        return response()->json([
            'message' => 'Inscription mise à jour avec succès',
            'enrollment' => $enrollment->load(['student', 'course'])
        ]);
    }

    public function destroy(Enrollment $enrollment)
    {
        // Vérifier si l'inscription a des notes associées
        if ($enrollment->grades()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer cette inscription car elle contient des notes'
            ], 422);
        }

        // Vérifier si l'inscription a des présences associées
        if ($enrollment->attendance()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer cette inscription car elle contient des présences'
            ], 422);
        }

        $enrollment->delete();

        return response()->json([
            'message' => 'Inscription supprimée avec succès'
        ]);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'enrollments' => 'required|array',
            'enrollments.*.student_id' => 'required|exists:users,id',
            'enrollments.*.course_id' => 'required|exists:courses,id',
            'enrollments.*.status' => 'required|in:pending,approved,rejected',
            'enrollments.*.enrollment_date' => 'required|date'
        ]);

        $enrollments = collect($request->enrollments)->map(function ($enrollmentData) {
            // Vérifier si l'étudiant est déjà inscrit au cours
            $existingEnrollment = Enrollment::where('student_id', $enrollmentData['student_id'])
                ->where('course_id', $enrollmentData['course_id'])
                ->first();

            if ($existingEnrollment) {
                return null;
            }

            // Vérifier si le cours a atteint son nombre maximum d'étudiants
            $course = Course::findOrFail($enrollmentData['course_id']);
            if ($course->max_students && $course->enrollments()->count() >= $course->max_students) {
                return null;
            }

            return Enrollment::create($enrollmentData);
        })->filter();

        return response()->json([
            'message' => 'Inscriptions créées avec succès',
            'enrollments' => $enrollments->load(['student', 'course'])
        ], 201);
    }

    public function approve(Enrollment $enrollment)
    {
        if ($enrollment->status !== 'pending') {
            return response()->json([
                'message' => 'Seules les inscriptions en attente peuvent être approuvées'
            ], 422);
        }

        $enrollment->update(['status' => 'approved']);

        return response()->json([
            'message' => 'Inscription approuvée avec succès',
            'enrollment' => $enrollment->load(['student', 'course'])
        ]);
    }

    public function reject(Enrollment $enrollment)
    {
        if ($enrollment->status !== 'pending') {
            return response()->json([
                'message' => 'Seules les inscriptions en attente peuvent être rejetées'
            ], 422);
        }

        $enrollment->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Inscription rejetée avec succès',
            'enrollment' => $enrollment->load(['student', 'course'])
        ]);
    }
} 