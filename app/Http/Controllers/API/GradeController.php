<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeRequest;
use App\Models\Grade;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $query = Grade::query()->with(['student', 'course', 'teacher']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->has('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }

        if ($request->has('min_grade')) {
            $query->where('grade', '>=', $request->min_grade);
        }

        if ($request->has('max_grade')) {
            $query->where('grade', '<=', $request->max_grade);
        }

        $grades = $query->latest()->paginate(10);

        return response()->json($grades);
    }

    public function store(GradeRequest $request)
    {
        // Vérifier si l'étudiant est inscrit au cours
        $enrollment = Enrollment::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->where('status', 'approved')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'message' => 'L\'étudiant n\'est pas inscrit à ce cours ou son inscription n\'est pas approuvée'
            ], 422);
        }

        // Vérifier si une note du même type existe déjà pour cet étudiant dans ce cours
        $existingGrade = Grade::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->where('type', $request->type)
            ->first();

        if ($existingGrade) {
            return response()->json([
                'message' => 'Une note de ce type existe déjà pour cet étudiant dans ce cours'
            ], 422);
        }

        $grade = Grade::create($request->validated());

        return response()->json([
            'message' => 'Note créée avec succès',
            'grade' => $grade->load(['student', 'course', 'teacher'])
        ], 201);
    }

    public function show(Grade $grade)
    {
        return response()->json([
            'grade' => $grade->load(['student', 'course', 'teacher'])
        ]);
    }

    public function update(GradeRequest $request, Grade $grade)
    {
        $grade->update($request->validated());

        return response()->json([
            'message' => 'Note mise à jour avec succès',
            'grade' => $grade->load(['student', 'course', 'teacher'])
        ]);
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();

        return response()->json([
            'message' => 'Note supprimée avec succès'
        ]);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:users,id',
            'grades.*.course_id' => 'required|exists:courses,id',
            'grades.*.teacher_id' => 'required|exists:users,id',
            'grades.*.exam_type' => 'required|in:midterm,final,quiz,assignment,other',
            'grades.*.grade' => 'required|numeric|min:0|max:20',
            'grades.*.comments' => 'nullable|string'
        ]);

        $grades = collect($request->grades)->map(function ($item) {
            return Grade::create($item);
        });

        return response()->json([
            'message' => 'Notes créées avec succès',
            'grades' => $grades->load(['student', 'course', 'teacher'])
        ], 201);
    }

    public function calculateAverage(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id'
        ]);

        $grades = Grade::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->get();

        if ($grades->isEmpty()) {
            return response()->json([
                'message' => 'Aucune note trouvée pour cet étudiant dans ce cours'
            ], 404);
        }

        $average = $grades->avg('grade');

        return response()->json([
            'average' => round($average, 2),
            'grades' => $grades
        ]);
    }

    public function studentStats(Request $request, $studentId)
    {
        $stats = Grade::where('student_id', $studentId)
            ->selectRaw('
                AVG(grade) as average_grade,
                MIN(grade) as min_grade,
                MAX(grade) as max_grade,
                COUNT(*) as total_grades,
                COUNT(CASE WHEN grade >= 10 THEN 1 END) as passed_grades,
                COUNT(CASE WHEN grade < 10 THEN 1 END) as failed_grades
            ')
            ->first();

        return response()->json($stats);
    }

    public function courseStats(Request $request, $courseId)
    {
        $stats = Grade::where('course_id', $courseId)
            ->selectRaw('
                AVG(grade) as average_grade,
                MIN(grade) as min_grade,
                MAX(grade) as max_grade,
                COUNT(*) as total_grades,
                COUNT(CASE WHEN grade >= 10 THEN 1 END) as passed_grades,
                COUNT(CASE WHEN grade < 10 THEN 1 END) as failed_grades
            ')
            ->first();

        return response()->json($stats);
    }
} 