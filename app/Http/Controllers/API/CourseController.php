<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query()->with(['teacher', 'department', 'academicYear']);

        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $courses = $query->latest()->paginate(10);

        return response()->json($courses);
    }

    public function store(CourseRequest $request)
    {
        // Vérifier si le code est unique pour l'année académique
        $exists = Course::where('code', $request->code)
            ->where('academic_year_id', $request->academic_year_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ce code de cours existe déjà pour cette année académique'
            ], 422);
        }

        $course = Course::create($request->validated());

        return response()->json([
            'message' => 'Cours créé avec succès',
            'course' => $course->load(['teacher', 'department', 'academicYear'])
        ], 201);
    }

    public function show(Course $course)
    {
        return response()->json([
            'course' => $course->load(['teacher', 'department', 'academicYear', 'students', 'resources'])
        ]);
    }

    public function update(CourseRequest $request, Course $course)
    {
        // Vérifier si le code est unique pour l'année académique
        $exists = Course::where('code', $request->code)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('id', '!=', $course->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ce code de cours existe déjà pour cette année académique'
            ], 422);
        }

        $course->update($request->validated());

        return response()->json([
            'message' => 'Cours mis à jour avec succès',
            'course' => $course->load(['teacher', 'department', 'academicYear'])
        ]);
    }

    public function destroy(Course $course)
    {
        // Vérifier si le cours a des étudiants inscrits
        if ($course->students()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer ce cours car il contient des étudiants inscrits'
            ], 422);
        }

        // Vérifier si le cours a des ressources
        if ($course->resources()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer ce cours car il contient des ressources'
            ], 422);
        }

        $course->delete();

        return response()->json([
            'message' => 'Cours supprimé avec succès'
        ]);
    }

    public function students(Course $course)
    {
        $students = $course->students()
            ->with(['role', 'department'])
            ->paginate(10);

        return response()->json($students);
    }

    public function resources(Course $course)
    {
        $resources = $course->resources()
            ->with(['teacher'])
            ->paginate(10);

        return response()->json($resources);
    }

    public function grades(Course $course)
    {
        $grades = $course->grades()
            ->with(['student', 'teacher'])
            ->paginate(10);

        return response()->json($grades);
    }

    public function attendance(Course $course)
    {
        $attendance = $course->attendance()
            ->with(['student', 'teacher'])
            ->paginate(10);

        return response()->json($attendance);
    }
} 