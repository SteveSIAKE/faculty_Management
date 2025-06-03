<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceRequest;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Attendance::class, 'attendance');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Attendance::class);
        
        $query = Attendance::with(['student', 'course']);

        // Filtres
        if ($request->has('student_id')) {
            $query->byStudent($request->student_id);
        }

        if ($request->has('course_id')) {
            $query->byCourse($request->course_id);
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

    public function store(AttendanceRequest $request)
    {
        $this->authorize('create', Attendance::class);
        
        $data = $request->validated();
        $attendance = Attendance::create($data);

        return response()->json([
            'message' => 'Présence enregistrée avec succès',
            'attendance' => $attendance->load(['student', 'course'])
        ], 201);
    }

    public function show(Attendance $attendance)
    {
        $this->authorize('view', $attendance);
        return response()->json($attendance->load(['student', 'course']));
    }

    public function update(AttendanceRequest $request, Attendance $attendance)
    {
        $this->authorize('update', $attendance);
        
        $attendance->update($request->validated());

        return response()->json([
            'message' => 'Présence mise à jour avec succès',
            'attendance' => $attendance->load(['student', 'course'])
        ]);
    }

    public function destroy(Attendance $attendance)
    {
        $this->authorize('delete', $attendance);
        
        $attendance->delete();

        return response()->json([
            'message' => 'Présence supprimée avec succès'
        ]);
    }

    public function bulkStore(Request $request)
    {
        $this->authorize('bulkStore', Attendance::class);
        
        $request->validate([
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:users,id',
            'attendances.*.course_id' => 'required|exists:courses,id',
            'attendances.*.date' => 'required|date',
            'attendances.*.status' => 'required|in:present,absent,late',
            'attendances.*.notes' => 'nullable|string'
        ]);

        $attendances = collect($request->attendances)->map(function ($attendance) {
            return Attendance::create($attendance);
        });

        return response()->json([
            'message' => 'Présences enregistrées avec succès',
            'attendances' => $attendances->load(['student', 'course'])
        ], 201);
    }

    public function getStudentAttendance(Request $request, $studentId)
    {
        $this->authorize('viewAny', Attendance::class);
        
        $query = Attendance::with(['course'])
            ->byStudent($studentId);

        if ($request->has('course_id')) {
            $query->byCourse($request->course_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        $attendances = $query->paginate(10);

        // Ajouter les statistiques
        $stats = [
            'attendance_rate' => Attendance::getAttendanceRate(
                $studentId,
                $request->course_id,
                $request->start_date,
                $request->end_date
            ),
            'late_rate' => Attendance::getLateRate(
                $studentId,
                $request->course_id,
                $request->start_date,
                $request->end_date
            )
        ];

        return response()->json([
            'attendances' => $attendances,
            'statistics' => $stats
        ]);
    }

    public function getCourseAttendance(Request $request, $courseId)
    {
        $this->authorize('viewAny', Attendance::class);
        
        $query = Attendance::with(['student'])
            ->byCourse($courseId);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        $attendances = $query->paginate(10);

        // Calculer les statistiques pour le cours
        $stats = [
            'total_students' => $query->distinct('student_id')->count(),
            'present_count' => $query->where('status', 'present')->count(),
            'absent_count' => $query->where('status', 'absent')->count(),
            'late_count' => $query->where('status', 'late')->count()
        ];

        return response()->json([
            'attendances' => $attendances,
            'statistics' => $stats
        ]);
    }

    public function getAttendanceStats(Request $request)
    {
        $this->authorize('viewAny', Attendance::class);
        
        $query = Attendance::query();

        if ($request->has('course_id')) {
            $query->byCourse($request->course_id);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        $stats = [
            'total_sessions' => $query->count(),
            'present_count' => $query->where('status', 'present')->count(),
            'absent_count' => $query->where('status', 'absent')->count(),
            'late_count' => $query->where('status', 'late')->count(),
            'attendance_rate' => $query->count() > 0 
                ? round(($query->where('status', 'present')->count() / $query->count()) * 100, 2)
                : 0
        ];

        return response()->json($stats);
    }
} 