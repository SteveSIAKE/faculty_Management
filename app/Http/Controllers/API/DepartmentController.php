<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::query()->with(['head', 'academicYear']);

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

        $departments = $query->latest()->paginate(10);

        return response()->json($departments);
    }

    public function store(DepartmentRequest $request)
    {
        // Vérifier si le code est unique pour l'année académique
        $exists = Department::where('code', $request->code)
            ->where('academic_year_id', $request->academic_year_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ce code de département existe déjà pour cette année académique'
            ], 422);
        }

        $department = Department::create($request->validated());

        return response()->json([
            'message' => 'Département créé avec succès',
            'department' => $department->load(['head', 'academicYear'])
        ], 201);
    }

    public function show(Department $department)
    {
        return response()->json([
            'department' => $department->load(['head', 'academicYear', 'teachers', 'courses'])
        ]);
    }

    public function update(DepartmentRequest $request, Department $department)
    {
        // Vérifier si le code est unique pour l'année académique
        $exists = Department::where('code', $request->code)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('id', '!=', $department->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ce code de département existe déjà pour cette année académique'
            ], 422);
        }

        $department->update($request->validated());

        return response()->json([
            'message' => 'Département mis à jour avec succès',
            'department' => $department->load(['head', 'academicYear'])
        ]);
    }

    public function destroy(Department $department)
    {
        // Vérifier si le département a des enseignants
        if ($department->teachers()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer ce département car il contient des enseignants'
            ], 422);
        }

        // Vérifier si le département a des cours
        if ($department->courses()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer ce département car il contient des cours'
            ], 422);
        }

        $department->delete();

        return response()->json([
            'message' => 'Département supprimé avec succès'
        ]);
    }

    public function getTeachers(Department $department)
    {
        $teachers = $department->teachers()
            ->with(['role', 'department'])
            ->paginate(10);

        return response()->json($teachers);
    }

    public function getCourses(Department $department)
    {
        $courses = $department->courses()
            ->with(['teacher', 'academicYear'])
            ->paginate(10);

        return response()->json($courses);
    }

    public function getStudents(Department $department)
    {
        $students = $department->students()
            ->with(['role', 'department'])
            ->paginate(10);

        return response()->json($students);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'departments' => 'required|array',
            'departments.*.name' => 'required|string|max:255',
            'departments.*.code' => 'required|string|max:50',
            'departments.*.description' => 'required|string',
            'departments.*.academic_year_id' => 'required|exists:academic_years,id',
            'departments.*.head_id' => 'nullable|exists:users,id'
        ]);

        $departments = collect($request->departments)->map(function ($departmentData) {
            // Vérifier si le code est unique pour l'année académique
            $exists = Department::where('code', $departmentData['code'])
                ->where('academic_year_id', $departmentData['academic_year_id'])
                ->exists();

            if ($exists) {
                return null;
            }

            return Department::create($departmentData);
        })->filter();

        return response()->json([
            'message' => 'Départements créés avec succès',
            'departments' => $departments->load(['head', 'academicYear'])
        ], 201);
    }
} 