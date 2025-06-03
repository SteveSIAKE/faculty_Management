<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamRequest;
use App\Models\Exam;
use App\Models\Course;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::query()->with(['course', 'teacher']);

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->has('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $exams = $query->latest()->paginate(10);

        return response()->json($exams);
    }

    public function store(ExamRequest $request)
    {
        // Vérifier si la date de l'examen est dans les dates du cours
        $course = Course::findOrFail($request->course_id);
        if ($request->date < $course->start_date || $request->date > $course->end_date) {
            return response()->json([
                'message' => 'La date de l\'examen doit être comprise entre les dates de début et de fin du cours'
            ], 422);
        }

        $exam = Exam::create($request->validated());

        return response()->json([
            'message' => 'Examen créé avec succès',
            'exam' => $exam->load(['course', 'teacher'])
        ], 201);
    }

    public function show(Exam $exam)
    {
        return response()->json([
            'exam' => $exam->load(['course', 'teacher'])
        ]);
    }

    public function update(ExamRequest $request, Exam $exam)
    {
        // Vérifier si la date de l'examen est dans les dates du cours
        $course = Course::findOrFail($request->course_id);
        if ($request->date < $course->start_date || $request->date > $course->end_date) {
            return response()->json([
                'message' => 'La date de l\'examen doit être comprise entre les dates de début et de fin du cours'
            ], 422);
        }

        $exam->update($request->validated());

        return response()->json([
            'message' => 'Examen mis à jour avec succès',
            'exam' => $exam->load(['course', 'teacher'])
        ]);
    }

    public function destroy(Exam $exam)
    {
        // Vérifier si l'examen a des notes associées
        if ($exam->grades()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer cet examen car il contient des notes'
            ], 422);
        }

        $exam->delete();

        return response()->json([
            'message' => 'Examen supprimé avec succès'
        ]);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'exams' => 'required|array',
            'exams.*.title' => 'required|string|max:255',
            'exams.*.description' => 'required|string',
            'exams.*.type' => 'required|in:midterm,final,quiz,assignment,other',
            'exams.*.course_id' => 'required|exists:courses,id',
            'exams.*.teacher_id' => 'required|exists:users,id',
            'exams.*.date' => 'required|date',
            'exams.*.duration' => 'required|integer|min:1',
            'exams.*.total_points' => 'required|numeric|min:0',
            'exams.*.passing_score' => 'required|numeric|min:0',
            'exams.*.status' => 'required|in:scheduled,ongoing,completed,cancelled',
            'exams.*.room' => 'nullable|string|max:255',
            'exams.*.instructions' => 'nullable|string'
        ]);

        $exams = collect($request->exams)->map(function ($item) {
            return Exam::create($item);
        });

        return response()->json([
            'message' => 'Examens créés avec succès',
            'exams' => $exams->load(['course', 'teacher'])
        ], 201);
    }

    public function getUpcoming(Request $request)
    {
        $query = Exam::query()
            ->with(['course', 'teacher'])
            ->where('date', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('date');

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        $exams = $query->paginate(10);

        return response()->json($exams);
    }

    public function getResults(Exam $exam)
    {
        $results = $exam->grades()
            ->with(['student'])
            ->selectRaw('
                student_id,
                AVG(grade) as average_grade,
                MIN(grade) as min_grade,
                MAX(grade) as max_grade,
                COUNT(*) as total_grades,
                COUNT(CASE WHEN grade >= ? THEN 1 END) as passed_count,
                COUNT(CASE WHEN grade < ? THEN 1 END) as failed_count
            ', [$exam->passing_score, $exam->passing_score])
            ->groupBy('student_id')
            ->get();

        return response()->json($results);
    }
} 