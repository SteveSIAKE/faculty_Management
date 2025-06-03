<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'course_id',
        'date',
        'status', // present, absent, late
        'notes'
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    // Relations
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Scopes
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Accessors & Mutators
    public function getFormattedDateAttribute()
    {
        return $this->date->format('d/m/Y H:i');
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'present' => 'Présent',
            'absent' => 'Absent',
            'late' => 'En retard',
            default => 'Inconnu'
        };
    }

    public function getIsPresentAttribute()
    {
        return $this->status === 'present';
    }

    public function getIsLateAttribute()
    {
        return $this->status === 'late';
    }

    // Méthodes statistiques
    public static function getAttendanceRate($studentId, $courseId = null, $startDate = null, $endDate = null)
    {
        $query = self::where('student_id', $studentId)
            ->where('status', 'present');

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $totalSessions = self::where('student_id', $studentId);
        if ($courseId) {
            $totalSessions->where('course_id', $courseId);
        }
        if ($startDate && $endDate) {
            $totalSessions->whereBetween('date', [$startDate, $endDate]);
        }

        $totalSessions = $totalSessions->count();
        if ($totalSessions === 0) {
            return 0;
        }

        return round(($query->count() / $totalSessions) * 100, 2);
    }

    public static function getLateRate($studentId, $courseId = null, $startDate = null, $endDate = null)
    {
        $query = self::where('student_id', $studentId)
            ->where('status', 'late');

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $totalSessions = self::where('student_id', $studentId);
        if ($courseId) {
            $totalSessions->where('course_id', $courseId);
        }
        if ($startDate && $endDate) {
            $totalSessions->whereBetween('date', [$startDate, $endDate]);
        }

        $totalSessions = $totalSessions->count();
        if ($totalSessions === 0) {
            return 0;
        }

        return round(($query->count() / $totalSessions) * 100, 2);
    }
} 