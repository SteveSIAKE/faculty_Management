<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AbsenceRequest;
use App\Models\Absence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Absence::with(['student', 'course', 'justifier']);

        // Filtres
        if ($request->has('student_id')) {
            $query->byStudent($request->student_id);
        }

        if ($request->has('course_id')) {
            $query->byCourse($request->course_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate(10));
    }

    public function store(AbsenceRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('justification_file')) {
            $data['justification_file'] = $request->file('justification_file')
                ->store('justifications', 'public');
        }

        $absence = Absence::create($data);

        return response()->json([
            'message' => 'Absence enregistrée avec succès',
            'absence' => $absence->load(['student', 'course'])
        ], 201);
    }

    public function show(Absence $absence)
    {
        return response()->json($absence->load(['student', 'course', 'justifier']));
    }

    public function update(AbsenceRequest $request, Absence $absence)
    {
        $data = $request->validated();

        if ($request->hasFile('justification_file')) {
            // Supprimer l'ancien fichier s'il existe
            if ($absence->justification_file) {
                Storage::disk('public')->delete($absence->justification_file);
            }

            $data['justification_file'] = $request->file('justification_file')
                ->store('justifications', 'public');
        }

        $absence->update($data);

        return response()->json([
            'message' => 'Absence mise à jour avec succès',
            'absence' => $absence->load(['student', 'course', 'justifier'])
        ]);
    }

    public function destroy(Absence $absence)
    {
        if ($absence->justification_file) {
            Storage::disk('public')->delete($absence->justification_file);
        }

        $absence->delete();

        return response()->json([
            'message' => 'Absence supprimée avec succès'
        ]);
    }

    public function justify(Request $request, Absence $absence)
    {
        $request->validate([
            'justification' => 'required|string',
            'justification_file' => 'nullable|file|max:10240', // 10MB max
            'status' => 'required|in:justified,unjustified'
        ]);

        $data = [
            'justification' => $request->justification,
            'status' => $request->status,
            'justified_by' => auth()->id(),
            'justified_at' => now()
        ];

        if ($request->hasFile('justification_file')) {
            if ($absence->justification_file) {
                Storage::disk('public')->delete($absence->justification_file);
            }

            $data['justification_file'] = $request->file('justification_file')
                ->store('justifications', 'public');
        }

        $absence->update($data);

        return response()->json([
            'message' => 'Absence justifiée avec succès',
            'absence' => $absence->load(['student', 'course', 'justifier'])
        ]);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'absences' => 'required|array',
            'absences.*.student_id' => 'required|exists:users,id',
            'absences.*.course_id' => 'required|exists:courses,id',
            'absences.*.date' => 'required|date',
            'absences.*.type' => 'required|in:absence,retard',
            'absences.*.comments' => 'nullable|string'
        ]);

        $absences = collect($request->absences)->map(function ($absence) {
            return Absence::create($absence);
        });

        return response()->json([
            'message' => 'Absences enregistrées avec succès',
            'absences' => $absences->load(['student', 'course'])
        ], 201);
    }

    public function getStudentAbsences(Request $request, $studentId)
    {
        $query = Absence::with(['course', 'justifier'])
            ->byStudent($studentId);

        if ($request->has('course_id')) {
            $query->byCourse($request->course_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        return response()->json($query->paginate(10));
    }

    public function getCourseAbsences(Request $request, $courseId)
    {
        $query = Absence::with(['student', 'justifier'])
            ->byCourse($courseId);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        return response()->json($query->paginate(10));
    }

    public function downloadJustification(Absence $absence)
    {
        if (!$absence->justification_file) {
            return response()->json([
                'message' => 'Aucun fichier de justification trouvé'
            ], 404);
        }

        return Storage::disk('public')->download($absence->justification_file);
    }
} 