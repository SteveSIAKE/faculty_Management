<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamGradeRequest;
use App\Models\Exam;
use App\Models\ExamGrade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExamGradeController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamGrade::with(['exam', 'student', 'grader']);

        // Filtres
        if ($request->has('exam_id')) {
            $query->byExam($request->exam_id);
        }

        if ($request->has('student_id')) {
            $query->byStudent($request->student_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('is_passing')) {
            $query->whereHas('exam', function ($q) use ($request) {
                $q->whereRaw('exam_grades.score >= exams.passing_score');
            });
        }

        // Recherche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('exam', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            });
        }

        // Tri
        $query->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc');

        return response()->json([
            'data' => $query->paginate($request->per_page ?? 15)
        ]);
    }

    public function store(ExamGradeRequest $request)
    {
        $validated = $request->validated();

        // Vérifier que l'examen existe et n'est pas terminé
        $exam = Exam::findOrFail($validated['exam_id']);
        if ($exam->is_completed) {
            return response()->json([
                'message' => 'Impossible de modifier les notes d\'un examen terminé'
            ], 422);
        }

        // Vérifier que l'étudiant est inscrit au cours
        if (!$exam->course->students()->where('users.id', $validated['student_id'])->exists()) {
            return response()->json([
                'message' => 'L\'étudiant n\'est pas inscrit à ce cours'
            ], 422);
        }

        // Vérifier que la note ne dépasse pas le total des points
        if ($validated['score'] > $exam->total_points) {
            return response()->json([
                'message' => 'La note ne peut pas dépasser le total des points de l\'examen'
            ], 422);
        }

        $examGrade = ExamGrade::create([
            ...$validated,
            'graded_by' => Auth::id(),
            'graded_at' => now()
        ]);

        return response()->json([
            'message' => 'Note créée avec succès',
            'data' => $examGrade->load(['exam', 'student', 'grader'])
        ], 201);
    }

    public function show(ExamGrade $examGrade)
    {
        return response()->json([
            'data' => $examGrade->load(['exam', 'student', 'grader'])
        ]);
    }

    public function update(ExamGradeRequest $request, ExamGrade $examGrade)
    {
        $validated = $request->validated();

        // Vérifier que l'examen n'est pas terminé
        if ($examGrade->exam->is_completed) {
            return response()->json([
                'message' => 'Impossible de modifier les notes d\'un examen terminé'
            ], 422);
        }

        // Vérifier que la note ne dépasse pas le total des points
        if ($validated['score'] > $examGrade->exam->total_points) {
            return response()->json([
                'message' => 'La note ne peut pas dépasser le total des points de l\'examen'
            ], 422);
        }

        $examGrade->update([
            ...$validated,
            'graded_by' => Auth::id(),
            'graded_at' => now()
        ]);

        return response()->json([
            'message' => 'Note mise à jour avec succès',
            'data' => $examGrade->load(['exam', 'student', 'grader'])
        ]);
    }

    public function destroy(ExamGrade $examGrade)
    {
        // Vérifier que l'examen n'est pas terminé
        if ($examGrade->exam->is_completed) {
            return response()->json([
                'message' => 'Impossible de supprimer les notes d\'un examen terminé'
            ], 422);
        }

        $examGrade->delete();

        return response()->json([
            'message' => 'Note supprimée avec succès'
        ]);
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:users,id',
            'grades.*.score' => 'required|numeric|min:0',
            'grades.*.comments' => 'nullable|string',
            'grades.*.status' => ['required', Rule::in(['pending', 'graded', 'appealed', 'final'])],
        ]);

        $exam = Exam::findOrFail($validated['exam_id']);

        // Vérifier que l'examen n'est pas terminé
        if ($exam->is_completed) {
            return response()->json([
                'message' => 'Impossible de modifier les notes d\'un examen terminé'
            ], 422);
        }

        // Vérifier que toutes les notes sont valides
        foreach ($validated['grades'] as $grade) {
            if ($grade['score'] > $exam->total_points) {
                return response()->json([
                    'message' => 'La note ne peut pas dépasser le total des points de l\'examen'
                ], 422);
            }

            if (!$exam->course->students()->where('users.id', $grade['student_id'])->exists()) {
                return response()->json([
                    'message' => 'L\'étudiant n\'est pas inscrit à ce cours'
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $grades = [];
            foreach ($validated['grades'] as $grade) {
                $grades[] = ExamGrade::create([
                    'exam_id' => $validated['exam_id'],
                    'student_id' => $grade['student_id'],
                    'score' => $grade['score'],
                    'comments' => $grade['comments'] ?? null,
                    'status' => $grade['status'],
                    'graded_by' => Auth::id(),
                    'graded_at' => now()
                ]);
            }
            DB::commit();

            return response()->json([
                'message' => 'Notes créées avec succès',
                'data' => ExamGrade::whereIn('id', collect($grades)->pluck('id'))
                    ->with(['exam', 'student', 'grader'])
                    ->get()
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Une erreur est survenue lors de la création des notes'
            ], 500);
        }
    }

    public function getStudentGrades(Request $request, User $student)
    {
        $query = ExamGrade::with(['exam', 'grader'])
            ->where('student_id', $student->id);

        if ($request->has('course_id')) {
            $query->whereHas('exam', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'data' => $query->paginate($request->per_page ?? 15)
        ]);
    }

    public function getExamGrades(Request $request, Exam $exam)
    {
        $query = ExamGrade::with(['student', 'grader'])
            ->where('exam_id', $exam->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('is_passing')) {
            $query->where('score', '>=', $exam->passing_score);
        }

        return response()->json([
            'data' => $query->paginate($request->per_page ?? 15)
        ]);
    }
} 