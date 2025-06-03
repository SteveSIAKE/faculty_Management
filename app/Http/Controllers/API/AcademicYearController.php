<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicYearRequest;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        $query = AcademicYear::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $academicYears = $query->latest()->paginate(10);

        return response()->json($academicYears);
    }

    public function store(AcademicYearRequest $request)
    {
        // Vérifier si une année académique active existe déjà
        if ($request->status === 'active') {
            $activeYear = AcademicYear::where('status', 'active')->first();
            if ($activeYear) {
                return response()->json([
                    'message' => 'Une année académique active existe déjà'
                ], 422);
            }
        }

        $academicYear = AcademicYear::create($request->validated());

        return response()->json([
            'message' => 'Année académique créée avec succès',
            'academic_year' => $academicYear
        ], 201);
    }

    public function show(AcademicYear $academicYear)
    {
        return response()->json([
            'academic_year' => $academicYear->load(['courses', 'departments'])
        ]);
    }

    public function update(AcademicYearRequest $request, AcademicYear $academicYear)
    {
        // Vérifier si on essaie d'activer une année académique alors qu'une autre est déjà active
        if ($request->status === 'active' && $academicYear->status !== 'active') {
            $activeYear = AcademicYear::where('status', 'active')
                ->where('id', '!=', $academicYear->id)
                ->first();

            if ($activeYear) {
                return response()->json([
                    'message' => 'Une année académique active existe déjà'
                ], 422);
            }
        }

        $academicYear->update($request->validated());

        return response()->json([
            'message' => 'Année académique mise à jour avec succès',
            'academic_year' => $academicYear
        ]);
    }

    public function destroy(AcademicYear $academicYear)
    {
        // Vérifier si l'année académique a des cours associés
        if ($academicYear->courses()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer cette année académique car elle contient des cours'
            ], 422);
        }

        // Vérifier si l'année académique a des départements associés
        if ($academicYear->departments()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer cette année académique car elle contient des départements'
            ], 422);
        }

        $academicYear->delete();

        return response()->json([
            'message' => 'Année académique supprimée avec succès'
        ]);
    }

    public function getActive()
    {
        $activeYear = AcademicYear::where('status', 'active')->first();

        if (!$activeYear) {
            return response()->json([
                'message' => 'Aucune année académique active'
            ], 404);
        }

        return response()->json([
            'academic_year' => $activeYear->load(['courses', 'departments'])
        ]);
    }

    public function setActive(AcademicYear $academicYear)
    {
        // Désactiver l'année académique active actuelle
        AcademicYear::where('status', 'active')->update(['status' => 'inactive']);

        // Activer la nouvelle année académique
        $academicYear->update(['status' => 'active']);

        return response()->json([
            'message' => 'Année académique activée avec succès',
            'academic_year' => $academicYear->load(['courses', 'departments'])
        ]);
    }

    public function getCourses(AcademicYear $academicYear)
    {
        $courses = $academicYear->courses()->with(['teacher', 'department'])->paginate(10);

        return response()->json($courses);
    }

    public function getDepartments(AcademicYear $academicYear)
    {
        $departments = $academicYear->departments()->with('head')->paginate(10);

        return response()->json($departments);
    }
} 