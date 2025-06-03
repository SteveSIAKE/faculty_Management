<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'score',
        'comments',
        'status',
        'graded_by',
        'graded_at'
    ];

    protected $casts = [
        'score' => 'float',
        'graded_at' => 'datetime'
    ];

    // Relations
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeGraded($query)
    {
        return $query->where('status', 'graded');
    }

    public function scopeAppealed($query)
    {
        return $query->where('status', 'appealed');
    }

    public function scopeFinal($query)
    {
        return $query->where('status', 'final');
    }

    public function scopeByExam($query, $examId)
    {
        return $query->where('exam_id', $examId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    // Accessors & Mutators
    public function getFormattedScoreAttribute()
    {
        return number_format($this->score, 2);
    }

    public function getPercentageAttribute()
    {
        if ($this->exam && $this->exam->total_points > 0) {
            return round(($this->score / $this->exam->total_points) * 100, 2);
        }
        return 0;
    }

    public function getIsPassingAttribute()
    {
        if ($this->exam) {
            return $this->score >= $this->exam->passing_score;
        }
        return false;
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'En attente',
            'graded' => 'Noté',
            'appealed' => 'En appel',
            'final' => 'Final',
            default => 'Inconnu'
        };
    }

    public function getGradedAtFormattedAttribute()
    {
        return $this->graded_at?->format('d/m/Y H:i');
    }
} 